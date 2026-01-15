<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Services\OrderService;
use App\Http\Requests\ProcessPaymentRequest;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $orderService;

    public function __construct(PaymentService $paymentService, OrderService $orderService)
    {
        $this->paymentService = $paymentService;
        $this->orderService = $orderService;
    }

    /**
     * Process payment for an order
     * POST /api/orders/{id}/payment
     */
    public function processPayment($id, ProcessPaymentRequest $request): JsonResponse
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

            // Check authorization (user owns the order)
            if ($order->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => app()->getLocale() === 'ar'
                        ? 'غير مصرح لك بالدفع لهذا الطلب'
                        : 'You are not authorized to pay for this order',
                ], 403);
            }

            // Check if order is already paid
            if ($order->isPaid()) {
                return response()->json([
                    'success' => false,
                    'message' => app()->getLocale() === 'ar'
                        ? 'الطلب مدفوع بالفعل'
                        : 'Order is already paid',
                ], 400);
            }

            // Check if order is cancelled
            if ($order->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => app()->getLocale() === 'ar'
                        ? 'لا يمكن الدفع لطلب ملغي'
                        : 'Cannot pay for cancelled order',
                ], 400);
            }

            // Process payment
            $result = $this->paymentService->processPayment(
                $order,
                $request->payment_method,
                $request->all()
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
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
                    ? 'فشل معالجة الدفع'
                    : 'Failed to process payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available payment methods
     * GET /api/payment/methods
     */
    public function paymentMethods(): JsonResponse
    {
        try {
            $methods = $this->paymentService->getAvailablePaymentMethods();

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم جلب طرق الدفع المتاحة'
                    : 'Available payment methods retrieved',
                'data' => [
                    'methods' => $methods,
                    'descriptions' => [
                        'stripe' => app()->getLocale() === 'ar'
                            ? 'الدفع ببطاقة الائتمان عبر Stripe'
                            : 'Credit card payment via Stripe',
                        'wallet' => app()->getLocale() === 'ar'
                            ? 'الدفع من رصيد المحفظة'
                            : 'Pay from wallet balance',
                        'cod' => app()->getLocale() === 'ar'
                            ? 'الدفع عند الاستلام'
                            : 'Cash on delivery',
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment methods',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
