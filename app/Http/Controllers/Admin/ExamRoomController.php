<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamRoom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        $allIds = array_merge([$creatorId], \App\Models\User::where('created_by', $creatorId)->pluck('id')->toArray());

        $rooms = ExamRoom::whereIn('created_by', $allIds)
            ->orderBy('name')
            ->paginate(10);

        return view('admin.exam_room.index', compact('rooms'));
    }

    public function create()
    {
        return view('admin.exam_room.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ExamRoom::create([
            'name' => $request->name,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.exam_room.index')
            ->with('success', 'Ruangan berhasil dibuat.');
    }

    public function show(string $id)
    {
        return redirect()->route('admin.exam_room.assignments', $id);
    }

    public function edit(string $id)
    {
        $room = $this->findOwnedRoom($id);

        return view('admin.exam_room.edit', compact('room'));
    }

    public function update(Request $request, string $id)
    {
        $room = $this->findOwnedRoom($id);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $room->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('admin.exam_room.index')
            ->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $room = $this->findOwnedRoom($id);

        Student::where('exam_room_id', $room->id)
            ->update(['exam_room_id' => null]);

        $room->delete();

        return redirect()
            ->route('admin.exam_room.index')
            ->with('success', 'Ruangan berhasil dihapus.');
    }

    public function assignments(Request $request, string $id)
    {
        $room = $this->findOwnedRoom($id);

        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 10;

        $students = Student::where('exam_room_id', $room->id)
            ->orderBy('name')
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        $availableQuery = $this->availableStudentsQuery();

        $availableCount = (clone $availableQuery)->count();

        $availableMaleCount = (clone $availableQuery)
            ->whereIn('gender', $this->maleGenderValues())
            ->count();

        $availableFemaleCount = (clone $availableQuery)
            ->whereIn('gender', $this->femaleGenderValues())
            ->count();

        $unidentifiedGenderCount = max(0, $availableCount - ($availableMaleCount + $availableFemaleCount));

        return view('admin.exam_room.manage_students', compact(
            'room',
            'students',
            'availableCount',
            'availableMaleCount',
            'availableFemaleCount',
            'unidentifiedGenderCount'
        ));
    }

    /**
     * Fitur lama: tambah siswa acak tanpa memperhatikan jenis kelamin.
     * Tetap dipertahankan.
     */
    public function assignRandom(Request $request, string $id)
    {
        $room = $this->findOwnedRoom($id);

        $request->validate([
            'count' => 'required|integer|min:1',
        ]);

        $limit = (int) $request->count;
        $availableCount = $this->availableStudentsQuery()->count();

        if ($availableCount === 0) {
            return redirect()
                ->back()
                ->with('error', 'Tidak ada siswa yang tersedia atau semua siswa sudah memiliki ruangan.');
        }

        if ($limit > $availableCount) {
            $limit = $availableCount;
        }

        $studentIds = $this->availableStudentsQuery()
            ->inRandomOrder()
            ->limit($limit)
            ->pluck('id');

        Student::whereIn('id', $studentIds)
            ->update(['exam_room_id' => $room->id]);

        return redirect()
            ->back()
            ->with('success', "Berhasil menambahkan {$limit} siswa ke ruangan ini secara acak.");
    }

    /**
     * Fitur baru:
     * Tambah siswa acak seimbang berdasarkan jenis kelamin.
     *
     * Contoh:
     * - Request 10, tersedia L=20 P=20 => masuk 5 L + 5 P.
     * - Request 10, tersedia L=3 P=20 => masuk 3 L + 3 P, tidak error.
     * - Request 11 => otomatis dibulatkan menjadi 10 agar tetap seimbang.
     */
    public function assignBalancedGender(Request $request, string $id)
    {
        $room = $this->findOwnedRoom($id);

        $request->validate([
            'count' => 'required|integer|min:2',
        ]);

        $requestedTotal = (int) $request->count;
        $targetPerGender = (int) ceil($requestedTotal / 2);

        $maleQuery = $this->availableStudentsQuery()
            ->whereIn('gender', $this->maleGenderValues());
        $femaleQuery = $this->availableStudentsQuery()
            ->whereIn('gender', $this->femaleGenderValues());
        $unidentifiedQuery = $this->availableStudentsQuery()
            ->whereNotIn('gender', array_merge($this->maleGenderValues(), $this->femaleGenderValues()));

        $availableMale = (clone $maleQuery)->count();
        $availableFemale = (clone $femaleQuery)->count();

        $takeMale = min($targetPerGender, $availableMale);
        $takeFemale = min($targetPerGender, $availableFemale);

        // Jika ada kekurangan untuk mencapai target requestedTotal, ambil dari stok yang sisa
        $shortage = $requestedTotal - ($takeMale + $takeFemale);
        
        if ($shortage > 0) {
            $remainingMale = $availableMale - $takeMale;
            if ($remainingMale > 0) {
                $extraMale = min($shortage, $remainingMale);
                $takeMale += $extraMale;
                $shortage -= $extraMale;
            }
            
            // Coba ambil sisa dari Perempuan
            $remainingFemale = $availableFemale - $takeFemale;
            if ($shortage > 0 && $remainingFemale > 0) {
                $extraFemale = min($shortage, $remainingFemale);
                $takeFemale += $extraFemale;
                $shortage -= $extraFemale;
            }
        }

        // Jika MASIH ada kekurangan (misal ada siswa yang gendernya tidak teridentifikasi)
        $takeUnidentified = 0;
        if ($shortage > 0) {
            $availableUnidentified = (clone $unidentifiedQuery)->count();
            $takeUnidentified = min($shortage, $availableUnidentified);
            $shortage -= $takeUnidentified;
        }

        $actualTotal = $takeMale + $takeFemale + $takeUnidentified;

        if ($actualTotal <= 0) {
            return redirect()
                ->back()
                ->with('error', "Tidak ada siswa yang tersedia untuk dimasukkan ke ruangan.");
        }

        try {
            DB::beginTransaction();

            $studentIds = collect();

            if ($takeMale > 0) {
                $studentIds = $studentIds->merge((clone $maleQuery)->inRandomOrder()->limit($takeMale)->pluck('id'));
            }
            if ($takeFemale > 0) {
                $studentIds = $studentIds->merge((clone $femaleQuery)->inRandomOrder()->limit($takeFemale)->pluck('id'));
            }
            if ($takeUnidentified > 0) {
                $studentIds = $studentIds->merge((clone $unidentifiedQuery)->inRandomOrder()->limit($takeUnidentified)->pluck('id'));
            }

            Student::whereIn('id', $studentIds)
                ->whereNull('exam_room_id')
                ->update(['exam_room_id' => $room->id]);

            DB::commit();

            $message = "Berhasil menambahkan {$actualTotal} siswa ke ruangan. Detail: {$takeMale} Laki-laki, {$takeFemale} Perempuan";
            if ($takeUnidentified > 0) {
                $message .= ", {$takeUnidentified} Tanpa Keterangan Gender";
            }
            $message .= ".";

            if ($actualTotal < $requestedTotal) {
                $message .= " Catatan: Kuota {$requestedTotal} tidak penuh karena hanya tersisa {$actualTotal} siswa yang belum mendapat ruangan.";
            } else if ($takeMale !== $takeFemale) {
                $message .= " Catatan: Rasio L/P tidak seimbang sempurna karena ketersediaan stok siswa L/P yang belum kebagian tidak mencukupi.";
            }

            return redirect()
                ->back()
                ->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Gagal menambahkan siswa seimbang: ' . $e->getMessage());
        }
    }

    public function removeStudent(string $id, string $student_id)
    {
        $room = $this->findOwnedRoom($id);

        $student = Student::where('id', $student_id)
            ->where('exam_room_id', $room->id)
            ->firstOrFail();

        $student->update(['exam_room_id' => null]);

        return redirect()
            ->back()
            ->with('success', 'Siswa berhasil dikeluarkan dari ruangan.');
    }

    /**
     * Method darurat.
     * Bisa dihapus dari route kalau sudah tidak diperlukan.
     */
    public function fixData()
    {
        $user = auth()->user();

        $count = Student::where(function ($q) {
            $q->where('user_id', 0)
                ->orWhereNull('user_id');
        })
            ->update(['user_id' => $user->id]);

        return "Fixed {$count} students. Assigned to User ID: {$user->id}. <a href='" . route('admin.exam_room.index') . "'>Back</a>";
    }

    public function bulkRemove(Request $request, string $id)
    {
        $room = $this->findOwnedRoom($id);

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $count = Student::whereIn('id', $request->student_ids)
            ->where('exam_room_id', $room->id)
            ->update(['exam_room_id' => null]);

        return redirect()
            ->back()
            ->with('success', "{$count} siswa terpilih berhasil dikeluarkan dari ruangan.");
    }

    /**
     * Ambil ruangan milik admin login.
     */
    private function findOwnedRoom(string $id): ExamRoom
    {
        return ExamRoom::where('id', $id)
            ->firstOrFail();
    }

    /**
     * Query siswa yang tersedia untuk dimasukkan ke ruangan.
     */
    private function availableStudentsQuery()
    {
        $user = auth()->user();
        $creatorId = in_array($user->role, ['operator', 'pengajar']) ? $user->created_by : $user->id;
        
        $ownerIds = [$creatorId];
        $subUserIds = \App\Models\User::where('created_by', $creatorId)->pluck('id')->toArray();
        $allIds = array_merge($ownerIds, $subUserIds);

        return Student::query()
            ->whereIn('created_by', $allIds)
            ->whereNull('exam_room_id');
    }

    /**
     * Variasi nilai gender laki-laki yang umum dipakai.
     * Sesuaikan jika database bro punya format lain.
     */
    private function maleGenderValues(): array
    {
        return [
            'L',
            'l',
            'Laki-laki',
            'laki-laki',
            'Laki Laki',
            'laki laki',
            'Laki-laki ',
            'Laki Laki ',
            'Male',
            'male',
            'M',
            'm',
        ];
    }

    /**
     * Variasi nilai gender perempuan yang umum dipakai.
     * Sesuaikan jika database bro punya format lain.
     */
    private function femaleGenderValues(): array
    {
        return [
            'P',
            'p',
            'Perempuan',
            'perempuan',
            'Perempuan ',
            'Female',
            'female',
            'F',
            'f',
        ];
    }
}
