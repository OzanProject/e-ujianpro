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
            'academic_year' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo_kiri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo_kanan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'signature' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'stamp' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ];

        $rules['subdomain'] = 'required|string|alpha_dash|max:50|unique:institutions,subdomain,' . $institution->id;

        $request->validate($rules);

        $data = $request->except(['logo', 'logo_kiri', 'logo_kanan', 'signature', 'stamp']); 
        
        $data['subdomain'] = $request->subdomain;

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
    public function deleteAsset($type)
    {
        $institution = Institution::where('user_id', auth()->id())->firstOrFail();
        $allowedTypes = ['logo', 'logo_kiri', 'logo_kanan', 'signature', 'stamp'];

        if (in_array($type, $allowedTypes)) {
            if ($institution->{$type}) {
                Storage::disk('public')->delete($institution->{$type});
                $institution->{$type} = null;
                $institution->save();
                
                return redirect()->back()->with('success', ucfirst(str_replace('_', ' ', $type)) . ' berhasil dihapus.');
            }
        }

        return redirect()->back()->with('error', 'Gagal menghapus aset.');
    }
}
