<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Helpers\NotificationHelper;
use App\Mail\BirthdayWishMail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class BirthdayNotificationService
{
    public function sendTodayBirthdays(): void
    {
        $today = Carbon::now()->format('m-d');

        Log::info('🎂 Birthday job started', ['date' => $today]);

        $users = User::whereNotNull('dob')
            ->whereRaw("DATE_FORMAT(dob, '%m-%d') = ?", [$today])
            ->get();

        foreach ($users as $user) {

            // Prevent duplicate notifications (per day)
            $alreadySent = Notification::where('user_id', $user->id)
                ->where('message', 'LIKE', '%Happy Birthday%')
                ->whereDate('created_at', today())
                ->exists();

            if ($alreadySent) {
                Log::info('⏭ Birthday already sent', [
                    'user_id' => $user->id
                ]);
                continue;
            }

            Log::info('🎉 Sending birthday notification', [
                'user_id' => $user->id,
                'name' => $user->name
            ]);

            NotificationHelper::sendGeneralNotification(
                $user->id,
                'Happy Birthday 🎂',
                "🎉 Happy Birthday, {$user->name}! Wishing you a great year ahead!",
                null
            );

              // 📧 Email Notification
            if ($user->email) {
               try {
    Mail::to($user->email)->send(new BirthdayWishMail($user));

    Log::info('📧 Birthday email sent successfully', [
        'user_id' => $user->id,
        'email'   => $user->email
    ]);

} catch (\Throwable $e) {

    Log::error('❌ Birthday email failed', [
        'user_id' => $user->id,
        'email'   => $user->email,
        'error'   => $e->getMessage()
    ]);
}

            }
        }

        Log::info('✅ Birthday job completed');
    }
}
