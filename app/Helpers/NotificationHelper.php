<?php

namespace App\Helpers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\Messaging\NotFound;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    private static $messaging = null;

    // Notification types
    const TYPE_HOLIDAY = 'holiday';
    const TYPE_LEAD = 'lead';
    const TYPE_TASK = 'task';
    const TYPE_OFFER = 'offer';
    const TYPE_SALARY_SLIP = 'salary_slip';
    const TYPE_GENERAL = 'general';

    private static function getMessaging()
    {
        if (self::$messaging === null) {
            $factory = (new Factory)
                ->withServiceAccount(config('firebase.projects.app.credentials.file'));
            self::$messaging = $factory->createMessaging();
        }

        return self::$messaging;
    }

    /**
     * Send notification for Holiday
     */
    public static function sendHolidayNotification($userId, $holidayId, $title, $message, $attachment = null)
    {
        return self::createAndSendNotification([
            'user_id' => $userId,
            'holiday_id' => $holidayId,
            'title' => $title,
            'message' => $message,
            'attachment' => $attachment,
            'type' => self::TYPE_HOLIDAY,
        ]);
    }

    /**
     * Send notification for Lead
     */
    public static function sendLeadNotification($userId, $leadId, $title, $message, $attachment = null)
    {
        return self::createAndSendNotification([
            'user_id' => $userId,
            'lead_id' => $leadId,
            'title' => $title,
            'message' => $message,
            'attachment' => $attachment,
            'type' => self::TYPE_LEAD,
        ]);
    }

    /**
     * Send notification for Task
     */
    public static function sendTaskNotification($userId, $taskId, $title, $message, $attachment = null)
    {
        return self::createAndSendNotification([
            'user_id' => $userId,
            'task_id' => $taskId,
            'title' => $title,
            'message' => $message,
            'attachment' => $attachment,
            'type' => self::TYPE_TASK,
        ]);
    }

    /**
     * Send notification for Offer
     */
    public static function sendOfferNotification($userId, $offerId, $title, $message, $attachment = null)
    {
        return self::createAndSendNotification([
            'user_id' => $userId,
            'offer_id' => $offerId,
            'title' => $title,
            'message' => $message,
            'attachment' => $attachment,
            'type' => self::TYPE_OFFER,
        ]);
    }

    /**
     * Send notification for Salary Slip
     */
    public static function sendSalarySlipNotification($userId, $salarySlipId, $title, $message, $attachment = null)
    {
        return self::createAndSendNotification([
            'user_id' => $userId,
            'salary_slip_id' => $salarySlipId,
            'title' => $title,
            'message' => $message,
            'attachment' => $attachment,
            'type' => self::TYPE_SALARY_SLIP,
        ]);
    }

    /**
     * Send general notification (no specific entity)
     */
    public static function sendGeneralNotification($userId, $title, $message, $attachment = null)
    {
        return self::createAndSendNotification([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'attachment' => $attachment,
            'type' => self::TYPE_GENERAL,
        ]);
    }

    /**
     * Core method to create notification in DB and send push
     */
    private static function createAndSendNotification(array $data)
    {
        try {
            // Create notification in database
            $notification = Notification::create([
                'user_id' => $data['user_id'],
                'holiday_id' => $data['holiday_id'] ?? null,
                'lead_id' => $data['lead_id'] ?? null,
                'task_id' => $data['task_id'] ?? null,
                'offer_id' => $data['offer_id'] ?? null,
                'salary_slip_id' => $data['salary_slip_id'] ?? null,
                'message' => $data['message'],
                'attachment' => $data['attachment'] ?? null,
                'is_read' => 0,
            ]);

            // Send push notification
            self::sendPushNotification(
                $data['user_id'],
                $data['title'],
                $data['message'],
                $data['type'],
                $notification->id,
                $data
            );

            return [
                'success' => true,
                'notification_id' => $notification->id,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to create notification: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send push notification via FCM
     */
    private static function sendPushNotification($userId, $title, $message, $type, $notificationId, $extraData = [])
    {
        $user = User::find($userId);

        if (!$user || !$user->fcm_token) {
            Log::info("User {$userId} has no FCM token, skipping push notification");
            return false;
        }

        try {
            // Prepare FCM data payload
            $fcmData = [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'notification_id' => (string) $notificationId,
                'type' => (string) $type,
            ];

            // Add specific IDs based on type
            if (isset($extraData['holiday_id'])) {
                $fcmData['holiday_id'] = (string) $extraData['holiday_id'];
            }
            if (isset($extraData['lead_id'])) {
                $fcmData['lead_id'] = (string) $extraData['lead_id'];
            }
            if (isset($extraData['task_id'])) {
                $fcmData['task_id'] = (string) $extraData['task_id'];
            }
            if (isset($extraData['offer_id'])) {
                $fcmData['offer_id'] = (string) $extraData['offer_id'];
            }
            if (isset($extraData['salary_slip_id'])) {
                $fcmData['salary_slip_id'] = (string) $extraData['salary_slip_id'];
            }
            if (isset($extraData['attachment'])) {
                $fcmData['attachment'] = (string) $extraData['attachment'];
            }

            $fcmMessage = CloudMessage::withTarget('token', $user->fcm_token)
                ->withNotification([
                    'title' => $title,
                    'body' => $message,
                ])
                ->withData($fcmData);

            self::getMessaging()->send($fcmMessage);

            Log::info("Push notification sent to user {$userId} for {$type}");
            return true;

        } catch (NotFound $e) {
            // Token is invalid - clear it
            $user->update(['fcm_token' => null]);
            Log::warning("Cleared invalid FCM token for user {$userId}");
            return false;

        } catch (\Throwable $e) {
            Log::error("FCM send error for user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark notification as read
     */
    public static function markAsRead($notificationId)
    {
        try {
            $notification = Notification::find($notificationId);
            if ($notification) {
                $notification->update(['is_read' => 1]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsRead($userId)
    {
        try {
            Notification::where('user_id', $userId)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread notification count for a user
     */
    public static function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', 0)
            ->count();
    }

    /**
     * Bulk send notifications to multiple users
     */
    public static function sendBulkNotification(array $userIds, $title, $message, $type = self::TYPE_GENERAL, $extraData = [])
    {
        $results = [];

        foreach ($userIds as $userId) {
            $data = array_merge([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
            ], $extraData);

            $results[$userId] = self::createAndSendNotification($data);
        }

        return $results;
    }
}
