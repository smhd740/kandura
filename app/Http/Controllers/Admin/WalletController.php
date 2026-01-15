<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Http\Requests\AddBalanceRequest;
use App\Http\Requests\DeductBalanceRequest;
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

    /**S
     * Get user wallet balance
     * GET /api/admin/wallet/{user_id}/balance
     */
    public function balance($userId): JsonResponse
    {
        try {
            $this->authorize('manageAny', Wallet::class);
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => app()->getLocale() === 'ar'
                        ? 'المستخدم غير موجود'
                        : 'User not found',
                ], 404);
            }

            $wallet = $this->walletService->getOrCreateWallet($user);

            return response()->json([
    'success' => true,
    'message' => app()->getLocale() === 'ar'
        ? 'تم جلب رصيد المحفظة بنجاح'
        : 'Wallet balance retrieved successfully',
    'data' => [
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
        'wallet' => new WalletResource($wallet),
    ],
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
     * Add balance to user wallet
     * POST /api/admin/wallet/add-balance
     */
    public function addBalance(AddBalanceRequest $request): JsonResponse
    {
        try {
            $this->authorize('addBalance', Wallet::class);
            $user = User::find($request->user_id);
            $wallet = $this->walletService->getOrCreateWallet($user);

            $result = $this->walletService->addBalance(
                $wallet,
                $request->amount,
                $request->description
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'previous_balance' => $result['data']['new_balance'] - $request->amount,
                        'added_amount' => $request->amount,
                        'new_balance' => $result['data']['new_balance'],
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'data' => $result['data'],
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'فشل إضافة الرصيد'
                    : 'Failed to add balance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deduct balance from user wallet
     * POST /api/admin/wallet/deduct-balance
     */
    public function deductBalance(DeductBalanceRequest $request): JsonResponse
    {
        try {
            $this->authorize('deductBalance', Wallet::class);
            $user = User::find($request->user_id);
            $wallet = $this->walletService->getOrCreateWallet($user);

            $result = $this->walletService->deductBalance(
                $wallet,
                $request->amount,
                $request->description
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'previous_balance' => $result['data']['new_balance'] + $request->amount,
                        'deducted_amount' => $request->amount,
                        'new_balance' => $result['data']['new_balance'],
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'data' => $result['data'],
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'فشل خصم الرصيد'
                    : 'Failed to deduct balance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user wallet transactions
     * GET /api/admin/wallet/{user_id}/transactions
     */
    public function transactions($userId, Request $request): JsonResponse
    {
        try {
            $this->authorize('manageAny', Wallet::class);
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => app()->getLocale() === 'ar'
                        ? 'المستخدم غير موجود'
                        : 'User not found',
                ], 404);
            }

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
    'data' => [
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
        'transactions' => TransactionResource::collection($transactions->items()),
    ],
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
