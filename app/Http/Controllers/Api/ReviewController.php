<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // التحقق من أن الطلب يخص اليوزر
        $order = Order::findOrFail($request->order_id);

        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only review your own orders'
            ], 403);
        }

        // التحقق من أن الطلب مكتمل
        if ($order->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'You can only review completed orders'
            ], 400);
        }

        // التحقق من عدم وجود review سابق
        if ($order->review) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this order'
            ], 400);
        }

        // إنشاء الـ Review
        $review = Review::create([
            'user_id' => auth()->id(),
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review created successfully',
            'data' => $review
        ], 201);
    }

    /**
     * Get user's reviews
     */
    public function index()
    {
        $reviews = Review::where('user_id', auth()->id())
            ->with(['order'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Get single review
     */
    public function show($id)
    {
        $review = Review::where('user_id', auth()->id())
            ->with(['order'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $review
        ]);
    }
}
