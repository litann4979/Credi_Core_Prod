<?php

namespace Database\Seeders;

use App\Models\OfficeRule;
use Illuminate\Database\Seeder;

class OfficeRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OfficeRule::firstOrCreate(
            ['id' => 1],
            [
                'office_start_time' => '09:30:00',
                'office_end_time' => '18:30:00',
                'lunch_start' => '13:00:00',
                'lunch_end' => '13:30:00',
                'lunch_allowed_minutes' => 30,
                'break_start' => '16:00:00',
                'break_end' => '16:30:00',
                'break_allowed_minutes' => 15,
                'work_allowed_minutes' => 20,
                'geofence_radius' => 50,
                'default_score' => 50,
                'target_mark' => 10,
                'lead_mark' => 5,
                'personal_lead_count' => 10,
                'late_penalty' => 5,
                'late_15min_penalty' => 10,
                'unauthorized_outside_penalty' => 10,
                'unauthorized_penalty_window_minutes' => 15,
                'extra_break_penalty' => 10,
                'extra_lunch_penalty' => 10,
                'early_checkout_penalty' => 10,
                'work_delay_penalty' => 10,
                'late_15min_enabled' => true,
                'per_minute_deduction_enabled' => false,
                'penalty_per_minute' => 1,
                'allow_admin_override' => true,
            ]
        );
    }
}
