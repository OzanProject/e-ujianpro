<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = \App\Models\ExamRoom::where('created_by', auth()->id())->paginate(10);
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

        \App\Models\ExamRoom::create([
            'name' => $request->name,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.exam_room.index')->with('success', 'Ruangan berhasil dibuat.');
    }

    public function show(string $id)
    {
        // Not implemented (maybe show detail students later)
    }

    public function edit(string $id)
    {
        $room = \App\Models\ExamRoom::where('id', $id)->where('created_by', auth()->id())->firstOrFail();
        return view('admin.exam_room.edit', compact('room'));
    }

    public function update(Request $request, string $id)
    {
         $room = \App\Models\ExamRoom::where('id', $id)->where('created_by', auth()->id())->firstOrFail();
         
         $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $room->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.exam_room.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $room = \App\Models\ExamRoom::where('id', $id)->where('created_by', auth()->id())->firstOrFail();
        
        // Remove students from this room first? Or let them be null?
        // Let's set them to null.
        \App\Models\Student::where('exam_room_id', $room->id)->update(['exam_room_id' => null]);
        
        $room->delete();
        
        return redirect()->route('admin.exam_room.index')->with('success', 'Ruangan berhasil dihapus.');
    }

    public function assignments(Request $request, string $id)
    {
        $room = \App\Models\ExamRoom::where('id', $id)->where('created_by', auth()->id())->firstOrFail();
        
        $perPage = $request->input('per_page', 10);

        $students = \App\Models\Student::where('exam_room_id', $room->id)
                    ->orderBy('name')
                    ->paginate($perPage)
                    ->appends(['per_page' => $perPage]); // Keep parameter in links
                    
        // Count available students (no room assigned)
        // Linking via exam_group or similar might be better, but for now filtering by creator.
        // Assuming Admin creates students directly or via import.
        $availableCount = \App\Models\Student::whereNull('exam_room_id')->count(); // WARNING: This counts ALL students in DB if not scoped?
        
        // NEED SCOPING:
        // We don't have direct 'created_by' on students table based on previous file views.
        // But Student belongsTo StudentGroup.
        // Or we can use `auth()->user()->students()`.
         $availableCount = auth()->user()->students()->whereNull('students.exam_room_id')->count();

        return view('admin.exam_room.manage_students', compact('room', 'students', 'availableCount'));
    }

    public function assignRandom(Request $request, string $id)
    {
        $room = \App\Models\ExamRoom::where('id', $id)->where('created_by', auth()->id())->firstOrFail();
        
        $request->validate([
            'count' => 'required|integer|min:1',
        ]);

        $limit = $request->count;
        $availableCount = auth()->user()->students()->whereNull('students.exam_room_id')->count();

        if ($limit > $availableCount && $availableCount > 0) {
            $limit = $availableCount;
        }
        
        if ($availableCount == 0) {
             return redirect()->back()->with('error', 'Tidak ada siswa yang tersedia (belum punya ruangan).');
        }

        // Fetch random IDs
        $studentIds = auth()->user()->students()
                        ->whereNull('students.exam_room_id')
                        ->inRandomOrder()
                        ->limit($limit)
                        ->pluck('students.id');

        \App\Models\Student::whereIn('id', $studentIds)->update(['exam_room_id' => $room->id]);

        return redirect()->back()->with('success', "Berhasil menambahkan $limit siswa ke ruangan ini secara acak.");
    }

    public function removeStudent(string $id, string $student_id)
    {
         $room = \App\Models\ExamRoom::where('id', $id)->where('created_by', auth()->id())->firstOrFail();
         
         $student = \App\Models\Student::where('id', $student_id)
                        ->where('exam_room_id', $room->id)
                        ->firstOrFail();
                        
         $student->update(['exam_room_id' => null]);
         
         return redirect()->back()->with('success', 'Siswa berhasil dikeluarkan dari ruangan.');
    }

    public function fixData() {
        $user = auth()->user();
        $count = \App\Models\Student::where('user_id', 0)->orWhereNull('user_id')->update(['user_id' => $user->id]);
        return "Fixed $count students. Assigned to User ID: " . $user->id . ". <a href='".route('admin.exam_room.index')."'>Back</a>";
    }
    public function bulkRemove(Request $request, string $id)
    {
         $room = \App\Models\ExamRoom::where('id', $id)->where('created_by', auth()->id())->firstOrFail();
         
         $request->validate([
             'student_ids' => 'required|array',
             'student_ids.*' => 'exists:students,id',
         ]);

         \App\Models\Student::whereIn('id', $request->student_ids)
                        ->where('exam_room_id', $room->id)
                        ->update(['exam_room_id' => null]);
         
         return redirect()->back()->with('success', 'Siswa terpilih berhasil dikeluarkan dari ruangan.');
    }
}
