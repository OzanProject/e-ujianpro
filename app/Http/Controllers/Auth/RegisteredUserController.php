<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    // Generic create method redirects or handles legacy
    public function create(): View
    {
        return view('auth.register', ['role' => 'admin_lembaga']); 
    }

    public function createSekolah(): View
    {
        return view('auth.register', ['role' => 'admin_lembaga']);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin_lembaga,pengajar'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
        ];

        if ($request->role === 'admin_lembaga') {
            $rules['institution_name'] = ['required', 'string', 'max:255'];
            $rules['subdomain'] = ['required', 'string', 'alpha_dash', 'max:50', 'unique:institutions,subdomain'];
            $rules['city'] = ['required', 'string'];
            $rules['type'] = ['required', 'string'];
        }

        $request->validate($rules);

        $status = in_array($request->role, ['admin_lembaga', 'pengajar']) ? 'pending' : 'active';
        $maxStudents = ($request->role === 'admin_lembaga') ? 50 : null;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $status,
            'max_students' => $maxStudents,
            'whatsapp' => $request->whatsapp,
        ]);

        event(new Registered($user));

        if ($request->role === 'admin_lembaga') {
            // Create default institution with enhanced data
            \App\Models\Institution::create([
                'user_id' => $user->id,
                'name' => $request->institution_name,
                'email' => $user->email, // Default email same as user
                'address' => $request->address,
                'city' => $request->city,
                'type' => $request->type,
                'subdomain' => $request->subdomain,
                'affiliate_code' => $request->affiliate_code,
                'phone' => $request->whatsapp, // Map WhatsApp to Institution Phone
            ]);

            // Default Points Bonus
            $user->update(['points_balance' => 50]);
            \App\Models\PointTransaction::create([
                'user_id' => $user->id,
                'amount' => 50,
                'type' => 'in',
                'description' => 'Bonus Pendaftaran (Saldo Awal)',
                'status' => 'approved',
                'reference_id' => 'BONUS-' . $user->id,
                'file_proof' => null
            ]);

            return redirect()->route('login')->with('success', 'Registrasi berhasil. Akun Sekolah Anda sedang dalam proses verifikasi oleh Admin Platform. Silakan tunggu persetujuan sebelum login.');
        }

        if ($request->role === 'pengajar') {
            return redirect()->route('login')->with('success', 'Registrasi berhasil. Akun Guru Anda sedang dalam proses verifikasi oleh Admin Lembaga. Silakan tunggu persetujuan sebelum login.');
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
