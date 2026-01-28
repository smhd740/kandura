<?php

namespace App\Http\Controllers\AdminUI;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['wallet.user', 'order'])->latest();

        if ($request->filled('search')) {
            $query->whereHas('wallet.user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('date_from') || $request->filled('date_to')) {
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
        }

        if ($request->filled('amount_from') || $request->filled('amount_to')) {
            if ($request->filled('amount_from')) {
                $query->where('amount', '>=', $request->amount_from);
            }
            if ($request->filled('amount_to')) {
                $query->where('amount', '<=', $request->amount_to);
            }
        }

        $transactions = $query->paginate(20);

        $stats = [
    'total_transactions' => Transaction::count(),
    'completed_transactions' => Transaction::byStatus('completed')->count(),
    'total_volume' => Transaction::byStatus('completed')->sum('amount'),
    'admin_credits' => Transaction::byType('admin_credit')->sum('amount'),
    'order_payments' => Transaction::byType('order')->sum('amount'),
];

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }



}
