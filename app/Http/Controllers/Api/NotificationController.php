<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $type = $request->get('type');

            $query = $request->user()->notifications();

            if ($type === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($type === 'unread') {
                $query->whereNull('read_at');
            }

            $notifications = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $notifications,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function unreadCount(Request $request)
    {
        try {
            $count = $request->user()->unreadNotifications()->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $count,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get unread count',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsRead(Request $request, $id)
    {
        try {
            $notification = $request->user()
                ->notifications()
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found',
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'data' => $notification,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAllAsRead(Request $request)
    {
        try {
            $request->user()->unreadNotifications->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $notification = $request->user()
                ->notifications()
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found',
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
