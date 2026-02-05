<?php

namespace App\Services;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Design;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Create a new order with items
     */


//  public function createOrder(array $data, $userId)
// {
//     DB::beginTransaction();
//     $user = User::findOrFail($userId);
//     try {
//         // 1. حساب الـ subtotal
//         $subtotal = 0;
//         foreach ($data['items'] as $itemData) {
//             $design = Design::find($itemData['design_id']);
//             $subtotal += ($design->price * $itemData['quantity']);
//         }

//         // 2. التحقق من الكوبون
//         $couponId = null;
//         $discountAmount = 0;

//         if (!empty($data['coupon_code'])) {
//             $couponValidation = $this->couponService->validateCoupon(
//                 $data['coupon_code'],
//                 $user,
//                 $subtotal
//             );

//             if (!$couponValidation['valid']) {
//                 DB::rollBack();
//                 throw new \Exception($couponValidation['message']);
//             }

//             $couponId = $couponValidation['coupon']->id;
//             $discountAmount = $couponValidation['discount_amount'];
//         }

//         $totalAmount = $subtotal - $discountAmount;

//         // 3. إنشاء الطلب
//         $order = Order::create([

//             'user_id' => $userId,
//             'address_id' => $data['address_id'],
//             'order_number' => Order::generateOrderNumber(),
//             'status' => 'pending',
//             'notes' => $data['notes'] ?? null,
//             'coupon_id' => $couponId,
//             'discount_amount' => $discountAmount,
//             'subtotal' => $subtotal,
//             'total_amount' => $totalAmount,
//         ]);
//         Log::critical('ORDER AFTER CREATE', $order->toArray());
//         // 4. إنشاء عناصر الطلب
//         foreach ($data['items'] as $itemData) {
//             $design = Design::find($itemData['design_id']);

//             $orderItem = OrderItem::create([
//                 'order_id' => $order->id,
//                 'design_id' => $design->id,
//                 'quantity' => $itemData['quantity'],
//                 'unit_price' => $design->price,
//                 'subtotal' => 0, // بينحسب من المودل
//             ]);

//             // Attach measurements
//             if (!empty($itemData['measurement_ids'])) {
//                 $orderItem->measurements()->attach($itemData['measurement_ids']);
//             }

//             // Attach design options
//             if (!empty($itemData['design_option_ids'])) {
//                 $orderItem->designOptions()->attach($itemData['design_option_ids']);
//             }
//         }

//         // 5. تطبيق الكوبون
//         if ($couponId) {
//             $this->couponService->applyCouponToOrder(
//                 $couponValidation['coupon'],
//                 $user,
//                 $order->id,
//                 $discountAmount
//             );
//         }

//         DB::commit();
//         $order->refresh();
// Log::critical('ORDER AFTER COMMIT', $order->toArray());


//         $order->load(['user', 'address.city', 'items.design', 'items.measurements', 'items.designOptions', 'coupon']);

//         return $order;

//     } catch (\Exception $e) {
//         DB::rollBack();
//         Log::error('Order creation failed: ' . $e->getMessage());
//         throw $e;
//     }
// }

    public function createOrder(array $data, $userId)
{
    DB::beginTransaction();
    $user = User::findOrFail($userId);

    try {
        // 1. حساب الـ subtotal
        $subtotal = 0;
        foreach ($data['items'] as $itemData) {
            $design = Design::find($itemData['design_id']);
            $subtotal += ($design->price * $itemData['quantity']);
        }

        // 2. التحقق من الكوبون
        $couponId = null;
        $discountAmount = 0;

        if (!empty($data['coupon_code'])) {
            $couponValidation = $this->couponService->validateCoupon(
                $data['coupon_code'],
                $user,
                $subtotal
            );

            if (!$couponValidation['valid']) {
                DB::rollBack();
                throw new \Exception($couponValidation['message']);
            }

            $couponId = $couponValidation['coupon']->id;
            $discountAmount = $couponValidation['discount_amount'];
        }

        $totalAmount = $subtotal - $discountAmount;

        // 3. إنشاء الطلب
        $order = Order::create([
            'user_id' => $userId,
            'address_id' => $data['address_id'],
            'order_number' => Order::generateOrderNumber(),
            'status' => 'pending',
            'payment_status' => 'pending',
            'notes' => $data['notes'] ?? null,
            'coupon_id' => $couponId,
            'discount_amount' => $discountAmount,
            'subtotal' => $subtotal,
            'total_amount' => $totalAmount,
        ]);

        Log::critical('ORDER AFTER CREATE', $order->toArray());

        // 4. إنشاء عناصر الطلب
        foreach ($data['items'] as $itemData) {
            $design = Design::find($itemData['design_id']);

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'design_id' => $design->id,
                'quantity' => $itemData['quantity'],
                'unit_price' => $design->price,
                'subtotal' => 0,
            ]);

            if (!empty($itemData['measurement_ids'])) {
                $orderItem->measurements()->attach($itemData['measurement_ids']);
            }

            if (!empty($itemData['design_option_ids'])) {
                $orderItem->designOptions()->attach($itemData['design_option_ids']);
            }
        }

        // 5. تطبيق الكوبون
        if ($couponId) {
            $this->couponService->applyCouponToOrder(
                $couponValidation['coupon'],
                $user,
                $order->id,
                $discountAmount
            );
        }

        DB::commit();
        $order->refresh();
        Log::critical('ORDER AFTER COMMIT', $order->toArray());

        $order->load(['user', 'address.city', 'items.design', 'items.measurements', 'items.designOptions', 'coupon']);

        return $order;

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Order creation failed: ' . $e->getMessage());
        throw $e;
    }
}

    /**
     * Get orders for a specific user with filters
     */
    public function getOrdersForUser($userId, array $filters = [])
    {
        $query = Order::with(['address.city', 'items.design.images', 'coupon'])
            ->where('user_id', $userId);

        // Search by order number
        if (!empty($filters['search'])) {
            $query->where('order_number', 'like', '%' . $filters['search'] . '%');
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Get orders for admin with search and filters
     */
    public function getOrdersForAdmin(array $filters = [])
    {
        $query = Order::with(['user', 'address.city', 'items.design', 'coupon']);

        // Search by order number or user name
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Filter by date range
        if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
            $query->dateRange($filters['date_from'] ?? null, $filters['date_to'] ?? null);
        }

        // Filter by price range
        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            $query->priceRange($filters['min_price'] ?? null, $filters['max_price'] ?? null);
        }

        // Filter by user
        if (!empty($filters['user_id'])) {
            $query->forUser($filters['user_id']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->sort($sortBy, $sortOrder);

        // Pagination
        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Get order by ID with all relationships
     * Authorization should be checked in Controller using Policy
     */
    public function getOrderById($orderId)
    {
        return Order::with([
    'user',
    'address.city',
    'items.design.images',
    'items.measurements',
    'items.designOptions',
    'coupon'
])->find($orderId);
    }

    /**
     * Cancel order (soft delete + status change)
     * Authorization should be checked in Controller using Policy
     */
    /**
 * Cancel order (soft delete + status change + refund if wallet payment)
 */
public function cancelOrder(Order $order)
{
    DB::beginTransaction();

    try {
        // Refund coupon usage if coupon was used
        if ($order->coupon_id) {
            $coupon = $order->coupon;
            $coupon->decrementUsage();

            $coupon->usages()
                ->where('user_id', $order->user_id)
                ->where('order_id', $order->id)
                ->delete();
        }

        // Refund to wallet if paid from wallet
        if ($order->payment_method === 'wallet' && $order->payment_status === 'paid') {
            $walletService = app(WalletService::class);
            $wallet = $order->user->wallet;

            if ($wallet) {
                $refundResult = $walletService->refundToWallet(
                    $wallet,
                    (float)$order->total_amount,
                    $order->id,
                    app()->getLocale() === 'ar'
                        ? "استرجاع مبلغ الطلب الملغي #{$order->order_number}"
                        : "Refund for cancelled order #{$order->order_number}"
                );

                if ($refundResult['success']) {
                    // تغيير payment_status لـ pending
                    $order->payment_status = 'pending';
                    Log::info("Order #{$order->order_number} refunded successfully. Amount: {$order->total_amount}");
                }
            }
        }

        // Update status to cancelled
        $order->status = 'cancelled';
        $order->save();

        // ✅ شلنا الـ soft delete - خلي الطلب موجود بس حالته cancelled
        // $order->delete();

        DB::commit();

        return $order;

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Order cancellation failed: ' . $e->getMessage());
        throw $e;
    }
}
    /**
     * Update order status
     * Authorization should be checked in Controller using Policy
     */
    public function updateOrderStatus(Order $order, string $newStatus)
    {
        DB::beginTransaction();

        try {
            $order->status = $newStatus;
            $order->save();

            DB::commit();

            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order status update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get order statistics for admin
     */
    public function getStatistics(array $filters = [])
    {
        $query = Order::query();

        // Apply date range filter if provided
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Basic statistics
        $stats = [
            'total_orders' => (clone $query)->count(),
            'pending_orders' => (clone $query)->where('status', 'pending')->count(),
            'processing_orders' => (clone $query)->where('status', 'processing')->count(),
            'completed_orders' => (clone $query)->where('status', 'completed')->count(),
            'cancelled_orders' => (clone $query)->where('status', 'cancelled')->count(),
        ];

        // Revenue statistics (only completed orders)
        $completedQuery = (clone $query)->where('status', 'completed');
        $stats['total_revenue'] = $completedQuery->sum('total_amount');
        $stats['average_order_value'] = $completedQuery->avg('total_amount');

        // Orders by status (breakdown)
        $stats['orders_by_status'] = Order::select('status', DB::raw('count(*) as count'))
            ->when(!empty($filters['date_from']), function ($q) use ($filters) {
                $q->whereDate('created_at', '>=', $filters['date_from']);
            })
            ->when(!empty($filters['date_to']), function ($q) use ($filters) {
                $q->whereDate('created_at', '<=', $filters['date_to']);
            })
            ->groupBy('status')
            ->get();

        // Top users by order count
        $stats['top_users'] = Order::select('user_id', DB::raw('count(*) as orders_count'), DB::raw('sum(total_amount) as total_spent'))
            ->with('user:id,name,email')
            ->when(!empty($filters['date_from']), function ($q) use ($filters) {
                $q->whereDate('created_at', '>=', $filters['date_from']);
            })
            ->when(!empty($filters['date_to']), function ($q) use ($filters) {
                $q->whereDate('created_at', '<=', $filters['date_to']);
            })
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Orders by date (last 30 days if no filter)
        if (empty($filters['date_from']) && empty($filters['date_to'])) {
            $stats['orders_by_date'] = Order::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as count'),
                    DB::raw('sum(total_amount) as revenue')
                )
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
        }

        return $stats;
    }

    /**
     * Calculate order total amount
     */
    protected function calculateTotal(Order $order)
    {
        return $order->items->sum('subtotal');
    }

    /**
     * Check if order can be cancelled
     */
    public function canCancel(Order $order, $userId): bool
    {
        return $order->user_id === $userId && $order->status === 'pending';
    }

    /**
     * Check if order status can be updated
     */
    public function canUpdateStatus(Order $order): bool
    {
        return !in_array($order->status, ['completed', 'cancelled']);
    }

    /**
     * Validate status transition
     */
    public function isValidStatusTransition(string $currentStatus, string $newStatus): bool
    {
        $validTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        return in_array($newStatus, $validTransitions[$currentStatus] ?? []);
    }
}
