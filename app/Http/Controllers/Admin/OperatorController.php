<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class OperatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil user dengan role 'operator'
        $operators = User::where('role', 'operator')->latest()->get();
        return view('admin.operator.index', compact('operators'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.operator.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'operator', // Set role otomatis
        ]);

        return redirect()->route('admin.operator.index')->with('success', 'Operator berhasil ditambahkan.');
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
        $operator = User::findOrFail($id);
        
        // Pastikan yang diedit adalah operator
        if ($operator->role !== 'operator') {
            abort(403);
        }

        return view('admin.operator.edit', compact('operator'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $operator = User::findOrFail($id);

        if ($operator->role !== 'operator') {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$operator->id],
        ]);

        $operator->name = $request->name;
        $operator->email = $request->email;
        
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $operator->password = Hash::make($request->password);
        }

        $operator->save();

        return redirect()->route('admin.operator.index')->with('success', 'Data Operator berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $operator = User::findOrFail($id);
        
        if ($operator->role !== 'operator') {
            abort(403);
        }

        $operator->delete();

        return redirect()->route('admin.operator.index')->with('success', 'Operator berhasil dihapus.');
    }
}
