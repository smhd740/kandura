<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\Admin\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Get all orders with filters (Admin)
     * GET /api/admin/orders
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Check authorization
            $this->authorize('viewAny', \App\Models\Order::class);

            $filters = $request->only([
                'search',
                'status',
                'date_from',
                'date_to',
                'min_price',
                'max_price',
                'user_id',
                'sort_by',
                'sort_order',
                'per_page'
            ]);

            $orders = $this->orderService->getOrdersForAdmin($filters);

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم جلب الطلبات بنجاح'
                    : 'Orders retrieved successfully',
                'data' => OrderResource::collection($orders),
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'فشل جلب الطلبات'
                    : 'Failed to retrieve orders',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order details (Admin)
     * GET /api/admin/orders/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            // Get order
            $order = $this->orderService->getOrderById($id);

            // Check if exists
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => app()->getLocale() === 'ar'
                        ? 'الطلب غير موجود'
                        : 'Order not found',
                ], 404);
            }

            // Check authorization (admin can view all)
            $this->authorize('view', $order);

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم جلب تفاصيل الطلب بنجاح'
                    : 'Order details retrieved successfully',
                'data' => new OrderResource($order),
            ]);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'غير مصرح لك'
                    : 'Unauthorized',
            ], 403);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'فشل جلب تفاصيل الطلب'
                    : 'Failed to retrieve order details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update order status (Admin)
     * PATCH /api/admin/orders/{id}/status
     */
    public function updateStatus($id, UpdateOrderStatusRequest $request): JsonResponse
    {
        try {
            // Get order
            $order = $this->orderService->getOrderById($id);

            // Check if exists
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => app()->getLocale() === 'ar'
                        ? 'الطلب غير موجود'
                        : 'Order not found',
                ], 404);
            }

            // Check authorization
            $this->authorize('updateStatus', $order);

            // Update status
            $order = $this->orderService->updateOrderStatus($order, $request->status);

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم تحديث حالة الطلب بنجاح'
                    : 'Order status updated successfully',
                'data' => new OrderResource($order->fresh([
                    'user',
                    'address.city',
                    'items.design',
                    'items.measurements',
                    'items.designOptions'
                ])),
            ]);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'غير مصرح لك بتحديث الحالة'
                    : 'You are not authorized to update status',
            ], 403);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'فشل تحديث حالة الطلب'
                    : 'Failed to update order status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order statistics (Admin)
     * GET /api/admin/order-statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            // Check authorization
            $this->authorize('viewStatistics', \App\Models\Order::class);

            $filters = $request->only(['date_from', 'date_to']);

            $stats = $this->orderService->getStatistics($filters);

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم جلب الإحصائيات بنجاح'
                    : 'Statistics retrieved successfully',
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'فشل جلب الإحصائيات'
                    : 'Failed to retrieve statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
