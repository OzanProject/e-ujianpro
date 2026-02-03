<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SuperAdminSettingController extends Controller
{
    public function index()
    {
        $pointPrice = Setting::getValue('point_price', 675);
        $bankAccounts = Setting::getValue('bank_accounts', []);
        $appName = Setting::getValue('app_name', 'CBT Pro Platform');
        $appLogo = Setting::getValue('app_logo', null);
        $appWhatsapp = Setting::getValue('app_whatsapp', '');
        $contentPrivacy = Setting::getValue('content_privacy', '');
        $contentTerms = Setting::getValue('content_terms', '');
        $contentContact = Setting::getValue('content_contact', '');
        
        return view('super_admin.settings.index', compact('pointPrice', 'bankAccounts', 'appName', 'appLogo', 'appWhatsapp', 'contentPrivacy', 'contentTerms', 'contentContact'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'point_price' => 'required|integer|min:1',
            'bank_name' => 'required|array',
            'bank_name.*' => 'required|string',
            'account_number' => 'required|array',
            'account_number.*' => 'required|string',
            'account_name' => 'required|array',
            'account_name.*' => 'required|string',
            'app_name' => 'nullable|string|max:255',
            'app_whatsapp' => 'nullable|string|max:20',
            'content_privacy' => 'nullable|string',
            'content_terms' => 'nullable|string',
            'content_contact' => 'nullable|string',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Save Price
        Setting::setValue('point_price', $request->point_price);
        Setting::setValue('app_name', $request->app_name);
        Setting::setValue('app_whatsapp', $request->app_whatsapp);
        
        // Save Page Content
        Setting::setValue('content_privacy', $request->content_privacy);
        Setting::setValue('content_terms', $request->content_terms);
        Setting::setValue('content_contact', $request->content_contact);
        
        // Handle Logo Upload
        if ($request->hasFile('app_logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::getValue('app_logo');
            if ($oldLogo && \Illuminate\Support\Facades\Storage::exists('public/' . $oldLogo)) {
                \Illuminate\Support\Facades\Storage::delete('public/' . $oldLogo);
            }

            $path = $request->file('app_logo')->store('settings', 'public');
            Setting::setValue('app_logo', $path);
        }

        // Prepare Bank Accounts JSON
        $banks = [];
        foreach ($request->bank_name as $index => $name) {
            if (!empty($name) && !empty($request->account_number[$index])) {
                $banks[] = [
                    'bank' => $name,
                    'number' => $request->account_number[$index],
                    'name' => $request->account_name[$index],
                ];
            }
        }

        Setting::setValue('bank_accounts', $banks);

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
