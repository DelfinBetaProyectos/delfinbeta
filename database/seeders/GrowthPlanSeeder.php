<?php

namespace Database\Seeders;

use App\Models\GrowthPlan;
use Illuminate\Database\Seeder;

class GrowthPlanSeeder extends Seeder
{
    /**
     * The first 90-day personal brand plan, which started on 17 Oct 2026.
     */
    public function run(): void
    {
        GrowthPlan::query()->firstOrCreate(['starts_on' => '2026-10-17'], [
            'name' => 'Plan 1',
            'days' => 90,
            'visits_per_month' => 500,
            'araguaneycode_clicks' => 50,
            'business_conversations' => 8,
            'publications' => 24,
            'linkedin_followers' => 300,
            'talks' => 1,
            'applications' => 15,
        ]);
    }
}
