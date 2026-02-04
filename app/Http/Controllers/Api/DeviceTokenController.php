<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'device_type' => 'sometimes|string|in:web,android,ios',
            'device_name' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $request->user();

            $deviceToken = DeviceToken::where('user_id', $user->id)
                ->where('token', $request->token)
                ->first();

            if ($deviceToken) {
                $deviceToken->updateLastUsed();

                return response()->json([
                    'success' => true,
                    'message' => 'Device token updated successfully',
                    'data' => $deviceToken,
                ]);
            }

            $deviceToken = DeviceToken::create([
                'user_id' => $user->id,
                'token' => $request->token,
                'device_type' => $request->device_type ?? 'web',
                'device_name' => $request->device_name,
                'last_used_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Device token registered successfully',
                'data' => $deviceToken,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register device token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $tokens = DeviceToken::where('user_id', $request->user()->id)
                ->orderBy('last_used_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tokens,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch device tokens',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, $token)
    {
        try {
            $deleted = DeviceToken::where('user_id', $request->user()->id)
                ->where('token', $token)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device token not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Device token deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete device token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
