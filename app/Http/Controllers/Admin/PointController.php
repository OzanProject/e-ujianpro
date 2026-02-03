<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\PointTransaction;
use Illuminate\Http\Request;

use App\Models\Setting; // Import Setting Model

class PointController extends Controller
{
    // Helper to get packages
    private function getPackages($pricePerPoint)
    {
        return [
            ['id' => 1, 'name' => 'Amarilis', 'points' => 100, 'price' => 100 * $pricePerPoint],
            ['id' => 2, 'name' => 'Bouvardia', 'points' => 250, 'price' => 250 * $pricePerPoint],
            ['id' => 3, 'name' => 'Cranberry', 'points' => 500, 'price' => 500 * $pricePerPoint],
            ['id' => 4, 'name' => 'Daffodil', 'points' => 750, 'price' => 750 * $pricePerPoint],
            ['id' => 5, 'name' => 'Euphorbia', 'points' => 1000, 'price' => 1000 * $pricePerPoint],
        ];
    }

    public function index()
    {
        $user = auth()->user();
        $transactions = $user->pointTransactions()->latest()->paginate(10);
        return view('admin.point.index', compact('transactions'));
    }

    public function topup()
    {
        $pointPrice = Setting::getValue('point_price', 675);
        $packages = $this->getPackages($pointPrice);
        
        return view('admin.point.topup', compact('pointPrice', 'packages'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:20',
        ]);

        $pointPrice = Setting::getValue('point_price', 675);
        $amount = $request->amount;
        $totalPrice = $amount * $pointPrice;

        // Find package name if matches standard packages
        $packages = $this->getPackages($pointPrice);
        $packageName = 'Paket Custom';
        foreach ($packages as $pkg) {
            if ($pkg['points'] == $amount) {
                $packageName = $pkg['name'];
                break;
            }
        }

        return view('admin.point.checkout', compact('amount', 'totalPrice', 'packageName'));
    }

    public function storeCheckout(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:20',
            'package_name' => 'required|string',
        ]);

        $user = auth()->user();
        $pointPrice = Setting::getValue('point_price', 675);
        $totalPrice = $request->amount * $pointPrice; // Recalculate for security

        $transaction = PointTransaction::create([
            'user_id' => $user->id,
            'type' => 'in',
            'amount' => $request->amount,
            'description' => 'Topup ' . $request->amount . ' Poin (' . $request->package_name . ')',
            'status' => 'pending', // Waiting for payment proof
            'reference_id' => null, // No proof yet
        ]);

        return redirect()->route('admin.point.payment', $transaction->id);
    }

    public function payment($id)
    {
        $transaction = PointTransaction::where('user_id', auth()->id())->findOrFail($id);
        
        // If already approved or has proof, maybe redirect?
        // Let's allow re-upload if pending.

        $bankAccounts = Setting::getValue('bank_accounts', []);
        $pointPrice = Setting::getValue('point_price', 675);

        return view('admin.point.payment', compact('transaction', 'bankAccounts', 'pointPrice'));
    }

    public function storePayment(Request $request, $id)
    {
        $transaction = PointTransaction::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('proof')->store('payment_proofs', 'public');

        $transaction->update([
            'reference_id' => $path,
            // Status remains pending until Admin approves, but now it has proof
        ]);

        return redirect()->route('admin.point.index')->with('success', 'Bukti pembayaran berhasil diupload. Mohon tunggu verifikasi admin.');
    }
}
