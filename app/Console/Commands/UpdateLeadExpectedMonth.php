<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\LeadHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateLeadExpectedMonth extends Command
{
    protected $signature = 'leads:update-expected-month';
    protected $description = 'Update expected_month for leads in personal_lead status to the current month if outdated';

   public function handle()
{
    Log::info('========== STARTING leads:update-expected-month COMMAND ==========');
    Log::info('Command started at: ' . now()->toDateTimeString());

    $currentMonth = Carbon::now()->format('F');
    $currentYear = Carbon::now()->year;

    $this->info("Current month: {$currentMonth}");
    Log::info("Current month: {$currentMonth}");

    try {
        DB::beginTransaction();

        $leads = Lead::whereIn('status', ['personal_lead','authorized','login','approved'])
            ->whereNotIn('lead_type', ['creditcard_loan'])
            ->where('status', '!=', 'future_lead')
            ->where('expected_month', '!=', $currentMonth)
            ->get();

        $this->info("Found {$leads->count()} leads to process");
        Log::info("Found {$leads->count()} leads to process");

        $updatedCount = 0;

        foreach ($leads as $lead) {
            $monthNumber = array_search($lead->expected_month, [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ]) + 1;

            if ($monthNumber && Carbon::createFromDate($currentYear, $monthNumber, 1)->isPast()) {
                $lead->update(['expected_month' => $currentMonth]);

                LeadHistory::create([
                    'lead_id' => $lead->id,
                    'user_id' => null,
                    'action' => 'month_updated',
                    'status' => $lead->status,
                    'comments' => "Expected month automatically updated to current month due to no changes in status.",
                ]);

                $this->info("Updated lead ID {$lead->id} expected_month to {$currentMonth}");
                Log::info("Updated lead ID {$lead->id} expected_month to {$currentMonth}");
                $updatedCount++;
            }
        }

        DB::commit();

        $this->info("Successfully updated {$updatedCount} leads");
        Log::info("Successfully updated {$updatedCount} leads");
        Log::info('========== COMMAND COMPLETED SUCCESSFULLY ==========');

        return 0;
    } catch (Exception $e) {
        DB::rollBack();
        $this->error('ERROR: ' . $e->getMessage());
        Log::error('Command failed: ' . $e->getMessage());
        Log::error('========== COMMAND FAILED ==========');
        return 1;
    }
}
}
