<?php

namespace App\Http\Controllers\AdminUI;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $query = Wallet::with(['user'])->latest();

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('balance_min')) {
            $query->where('amount', '>=', $request->balance_min);
        }

        if ($request->filled('balance_max')) {
            $query->where('amount', '<=', $request->balance_max);
        }

        $wallets = $query->paginate(15);

        $stats = [
            'total_wallets' => Wallet::count(),
            'total_balance' => Wallet::sum('amount'),
            'avg_balance' => Wallet::avg('amount'),
        ];

        return view('admin.wallets.index', compact('wallets', 'stats'));
    }

    public function show(Wallet $wallet)
    {
        $wallet->load(['user', 'transactions.order']);

        return view('admin.wallets.show', compact('wallet'));
    }

    public function addBalance(Request $request, Wallet $wallet)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $wallet->addBalance($request->amount);

            Transaction::create([
                'amount' => $request->amount,
                'type' => 'admin_credit',
                'wallet_id' => $wallet->id,
                'status' => 'complete',
                'description' => $request->description ?? 'Balance added by admin',
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', __('Balance added successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', __('Failed to add balance: ') . $e->getMessage());
        }
    }

    public function deductBalance(Request $request, Wallet $wallet)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $wallet->amount,
            'description' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $wallet->deductBalance($request->amount);

            Transaction::create([
                'amount' => $request->amount,
                'type' => 'admin_debit',
                'wallet_id' => $wallet->id,
                'status' => 'complete',
                'description' => $request->description,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', __('Balance deducted successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', __('Failed to deduct balance: ') . $e->getMessage());
        }
    }

    public function byUser(User $user)
    {
        $wallet = $user->getOrCreateWallet();

        return redirect()->route('admin.wallets.show', $wallet);
    }
}
