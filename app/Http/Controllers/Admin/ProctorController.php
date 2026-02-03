<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proctors = User::where('role', 'proctor')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.proctor.index', compact('proctors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Typically handled by modal in index, but if separate page needed:
        $rooms = \App\Models\ExamRoom::where('created_by', auth()->id())->get();
        return view('admin.proctor.create', compact('rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'proctor',
            'status' => 'active',
            'created_by' => auth()->id(),
            'exam_room_id' => $request->exam_room_id,
        ]);

        return redirect()->route('admin.proctor.index')->with('success', 'Akun Pengawas berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $proctor = User::findOrFail($id);
        $rooms = \App\Models\ExamRoom::where('created_by', auth()->id())->get();
        return view('admin.proctor.edit', compact('proctor', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $proctor = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($proctor->id)],
            'password' => 'nullable|string|min:8',
            'status' => 'required|in:active,suspended',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
            'exam_room_id' => $request->exam_room_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $proctor->update($data);

        return redirect()->route('admin.proctor.index')->with('success', 'Data Pengawas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $proctor = User::findOrFail($id);
        
        // Prevent deleting self (not applicable here) or protected users
        if ($proctor->id == auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $proctor->delete();

        return redirect()->route('admin.proctor.index')->with('success', 'Akun Pengawas berhasil dihapus.');
    }
}
