<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
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
     * Create a new order
     * POST /api/orders
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            // Authorization checked in Request + Policy
            $this->authorize('create', \App\Models\Order::class);

            // Create order
            $order = $this->orderService->createOrder(
                $request->validated(),
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم إنشاء الطلب بنجاح'
                    : 'Order created successfully',
                'data' => new OrderResource($order),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'فشل إنشاء الطلب'
                    : 'Failed to create order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's orders with filters
     * GET /api/orders/my-orders
     */
    public function myOrders(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'search',
                'status',
                'date_from',
                'date_to',
                'sort_by',
                'sort_order',
                'per_page'
            ]);

            $orders = $this->orderService->getOrdersForUser(auth()->id(), $filters);

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
     * Get order details
     * GET /api/orders/{id}
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

            // Check authorization
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
                    ? 'غير مصرح لك بعرض هذا الطلب'
                    : 'You are not authorized to view this order',
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
     * Cancel order
     * POST /api/orders/{id}/cancel
     */
    public function cancel($id): JsonResponse
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
            $this->authorize('cancel', $order);

            // Cancel order
            $this->orderService->cancelOrder($order);

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم إلغاء الطلب بنجاح'
                    : 'Order cancelled successfully',
            ]);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'لا يمكنك إلغاء هذا الطلب'
                    : 'You cannot cancel this order',
            ], 403);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'فشل إلغاء الطلب'
                    : 'Failed to cancel order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
