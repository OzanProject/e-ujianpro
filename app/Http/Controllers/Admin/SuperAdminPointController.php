<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuperAdminPointController extends Controller
{
    public function index()
    {
        $transactions = PointTransaction::with('user')
                            ->where('status', 'pending')
                            ->latest()
                            ->paginate(10);
                            
        return view('super_admin.points.index', compact('transactions'));
    }

    public function approve($id)
    {
        $transaction = PointTransaction::with('user')->findOrFail($id);
        
        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Transaksi sudah diproses sebelumnya.');
        }

        // Update Transaction Status
        $transaction->update(['status' => 'approved']);

        // Add Points to User Balance
        $user = $transaction->user;
        $user->increment('points_balance', $transaction->amount);
        
        // AUTO-UPGRADE QUOTA:
        // As per user requirement, Topup points should imply Quota Upgrade.
        // We add the purchased points/quota to the existing limit.
        $user->increment('max_students', $transaction->amount);

        return redirect()->back()->with('success', 'Topup berhasil disetujui. Poin ' . $transaction->amount . ' telah ditambahkan ke ' . $user->name . ' dan Kuota Siswa bertambah menjadi ' . $user->max_students . '.');
    }

    public function reject(Request $request, $id)
    {
        $transaction = PointTransaction::findOrFail($id);
        
        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Transaksi sudah diproses sebelumnya.');
        }

        $transaction->update([
            'status' => 'rejected',
            'description' => $transaction->description . ' [Ditolak: ' . ($request->reason ?? 'Bukti tidak valid') . ']' 
        ]);

        return redirect()->back()->with('success', 'Topup ditolak.');
    }
}
