<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InstitutionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $institution = Institution::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'Lembaga ' . $user->name,
                'email' => $user->email,
            ]
        );

        return view('admin.institution.edit', compact('institution'));
    }

    public function update(Request $request)
    {
        $institution = Institution::where('user_id', auth()->id())->firstOrFail();

        $rules = [
            'name' => 'required|string|max:255',
            'dinas_name' => 'nullable|string|max:255',
            'npsn' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'head_master' => 'nullable|string|max:255',
            'nip_head_master' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo_kiri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo_kanan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'signature' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'stamp' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ];

        // Only allow setting subdomain if it's currently null users shouldn't change it freely to avoid broken links
        if (!$institution->subdomain) {
            $rules['subdomain'] = 'required|string|alpha_dash|max:50|unique:institutions,subdomain,' . $institution->id;
        }

        $request->validate($rules);

        $data = $request->except(['logo', 'logo_kiri', 'logo_kanan', 'signature', 'stamp', 'subdomain']); // Exclude subdomain initially

        // Manually handle subdomain update if allowed
        if (!$institution->subdomain && $request->filled('subdomain')) {
            $data['subdomain'] = $request->subdomain;
        }

        // Handle File Uploads Helper
        $files = ['logo', 'logo_kiri', 'logo_kanan', 'signature', 'stamp'];
        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                if ($institution->{$file}) {
                    Storage::disk('public')->delete($institution->{$file});
                }
                $data[$file] = $request->file($file)->store('institution', 'public');
            }
        }

        $institution->update($data);

        return redirect()->route('admin.institution.index')->with('success', 'Data lembaga berhasil diperbarui.');
    }
}
