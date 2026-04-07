<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BirthdayNotificationService;

class SendBirthdayNotifications extends Command
{
    protected $signature = 'notifications:send-birthdays';
    protected $description = 'Send automatic birthday notifications to users';

    public function handle(BirthdayNotificationService $service): int
    {
        $this->info('Sending birthday notifications...');
        $service->sendTodayBirthdays();
        $this->info('Done.');

        return Command::SUCCESS;
    }
}
