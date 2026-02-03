<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Institution;
use App\Models\Student;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        // 1. Total Sekolah (Lembaga)
        $totalInstitutions = \App\Models\Institution::count();

        // 2. Total Guru (Role: Pengajar)
        // Previously counted all users, filtered to Teachers only
        $totalTeachers = User::where('role', 'pengajar')->count(); 

        // 3. Total Siswa (Global)
        $totalStudents = Student::count();

        // 4. Ujian Aktif (Count)
        $activeExamsCount = \App\Models\ExamSession::where('is_active', true)->count();
        
        // 4b. Ujian Aktif (List for Table)
        $activeExamSessions = \App\Models\ExamSession::where('is_active', true)
                                ->with(['subject', 'examType'])
                                ->latest()
                                ->take(5)
                                ->get();

        // 5. Recent Institutions (Latest 5)
        $recentInstitutions = \App\Models\Institution::latest()->take(5)->get();

        // 6. Recent Users (Latest 5 Registered)
        $recentUsers = User::latest()->take(5)->get();

        // 7. Chart Data (Registrasi Sekolah 6 Bulan Terakhir)
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('F'); // Januari
            $year = $date->format('Y');
            
            $count = \App\Models\Institution::whereYear('created_at', $year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
            
            $chartLabels[] = "$monthName";
            $chartData[] = $count;
        }

        return view('super_admin.dashboard', compact(
            'totalInstitutions',
            'totalTeachers',
            'totalStudents', 
            'activeExamsCount',
            'activeExamSessions',
            'recentInstitutions',
            'recentUsers',
            'chartLabels',
            'chartData'
        ));
    }

    public function institutions()
    {
        $institutions = User::where('role', 'admin_lembaga')->latest()->paginate(10);
        return view('super_admin.institutions.index', compact('institutions'));
    }

    public function updateQuota(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'max_students' => 'nullable|integer|min:0',
            'points_balance' => 'nullable|integer|min:0',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $request) {
            // Handle Points
            if ($request->has('points_balance')) {
                $oldPoints = $user->points_balance;
                $newPoints = $request->points_balance;
                
                if ($oldPoints != $newPoints) {
                    $diff = $newPoints - $oldPoints;
                    $type = $diff > 0 ? 'in' : 'out';
                    
                    \App\Models\PointTransaction::create([
                        'user_id' => $user->id,
                        'amount' => abs($diff),
                        'type' => $type,
                        'description' => 'Penyesuaian Manual oleh Super Admin',
                        'status' => 'approved',
                        'reference_id' => 'MANUAL-ADJ-' . time(),
                        'file_proof' => null
                    ]);
                    
                    $user->points_balance = $newPoints;
                }
            }

            // Handle Quota
            $user->max_students = $request->max_students;
            $user->save();
        });

        return redirect()->back()->with('success', 'Data sekolah (Poin & Kuota) berhasil diperbarui.');
    }

    public function create()
    {
        return view('super_admin.institutions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255', // Nama Admin
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'institution_name' => 'required|string|max:255',
            'subdomain' => 'required|string|alpha_dash|max:50|unique:institutions,subdomain',
            'city' => 'required|string',
            'type' => 'required|string',
            'whatsapp' => 'nullable|string|max:20',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => 'admin_lembaga',
                'status' => 'active', // Direct active for Super Admin creation
                'whatsapp' => $request->whatsapp,
                'max_students' => 50, // Default quota
                'points_balance' => 50, // Default Points
            ]);

            // Create associated institution
            Institution::create([
                'user_id' => $user->id,
                'name' => $request->institution_name,
                'email' => $request->email,
                'subdomain' => $request->subdomain,
                'city' => $request->city,
                'type' => $request->type,
                'affiliate_code' => $request->affiliate_code, // Optional
            ]);

            // Create Initial Transaction
            \App\Models\PointTransaction::create([
                'user_id' => $user->id,
                'amount' => 50,
                'type' => 'in',
                'description' => 'Bonus Pendaftaran (Via Admin)',
                'status' => 'approved',
                'reference_id' => 'BONUS-ADMIN-' . $user->id,
                'file_proof' => null
            ]);
        });

        return redirect()->back()->with('success', 'Sekolah berhasil ditambahkan dan langsung Aktif.');
    }

    public function destroy($id)
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
                // Feature: Ultimate Deep Delete (Hapus Bersih)
                // Target: Admin Lembaga
                $user = User::where('role', 'admin_lembaga')->findOrFail($id);
                
                // Collect all User IDs associated with this Institution (Admin + Teachers + Operators)
                // This is needed to delete data created by Teachers as well
                $childUserIds = User::where('created_by', $user->id)->pluck('id')->toArray();
                $allCreatorIds = array_merge([$user->id], $childUserIds);

                // 1. Delete Content Data (Subjects, Classes, Exams)
                // Delete Subjects created by this institution
                \App\Models\Subject::whereIn('created_by', $allCreatorIds)->delete();
                
                // Delete Student Groups (Classes) created by this institution
                \App\Models\StudentGroup::whereIn('created_by', $allCreatorIds)->delete();

                // Delete Exam Packages created by this institution
                \App\Models\ExamPackage::whereIn('created_by', $allCreatorIds)->delete();
                
                // 2. Delete Students & Related Data
                // Students are owned by the Admin User ID
                $students = Student::where('user_id', $user->id)->get();
                foreach ($students as $student) {
                    \App\Models\ExamAttempt::where('student_id', $student->id)->delete();
                    $student->delete();
                }

                // 3. Delete Institution Profile
                if ($user->institution) {
                    $user->institution->delete();
                }

                // 4. Delete Teachers & Operators (Child Users)
                User::whereIn('id', $childUserIds)->delete();

                // 5. Delete the User Account
                $user->delete();
            });

            return redirect()->back()->with('success', 'Sekolah dan seluruh data asetnya (Mapel, Kelas, Ujian, Siswa, Guru) berhasil dihapus TOTAL.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $user = User::where('role', 'admin_lembaga')->findOrFail($id);
        $user->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Akun sekolah berhasil disetujui dan diaktifkan.');
    }

    public function suspend($id)
    {
        $user = User::where('role', 'admin_lembaga')->findOrFail($id);
        $user->update(['status' => 'suspended']);

        return redirect()->back()->with('success', 'Akun sekolah berhasil dinonaktifkan (suspend).');
    }

    public function activate($id)
    {
        $user = User::where('role', 'admin_lembaga')->findOrFail($id);
        $user->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Akun sekolah berhasil diaktifkan kembali.');
    }
}
