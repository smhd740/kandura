<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\WalletResource;
use App\Http\Resources\TransactionResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class WalletController extends Controller
{
    use AuthorizesRequests;
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get wallet balance
     * GET /api/wallet/balance
     */
    public function balance(): JsonResponse
{
    try {
        $user = auth()->user();
        $wallet = $this->walletService->getOrCreateWallet($user);

        // Authorization
        $this->authorize('view', $wallet);

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar'
                ? 'تم جلب رصيد المحفظة بنجاح'
                : 'Wallet balance retrieved successfully',
            'data' => new WalletResource($wallet),
        ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'فشل جلب رصيد المحفظة'
                    : 'Failed to retrieve wallet balance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get wallet transactions
     * GET /api/wallet/transactions
     */
    public function transactions(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $wallet = $this->walletService->getOrCreateWallet($user);

            $filters = $request->only([
                'type',
                'status',
                'date_from',
                'date_to',
                'sort_by',
                'sort_order',
                'per_page'
            ]);

            $transactions = $this->walletService->getTransactions($wallet, $filters);

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم جلب المعاملات بنجاح'
                    : 'Transactions retrieved successfully',
                'data' => TransactionResource::collection($transactions->items()),
                'meta' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'فشل جلب المعاملات'
                    : 'Failed to retrieve transactions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
