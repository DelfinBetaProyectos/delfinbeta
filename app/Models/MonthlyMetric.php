<?php

namespace App\Models;

use Database\Factories\MonthlyMetricFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Plan figures the site cannot measure, entered by hand at the monthly review.
 * `month` is the first day of the month the figures belong to, within its plan.
 *
 * @property int $id
 * @property int $growth_plan_id
 * @property Carbon $month
 * @property int $publications
 * @property int $linkedin_followers
 * @property int $talks
 * @property int $applications
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['growth_plan_id', 'month', 'publications', 'linkedin_followers', 'talks', 'applications'])]
class MonthlyMetric extends Model
{
    /** @use HasFactory<MonthlyMetricFactory> */
    use HasFactory;

    /**
     * The figures entered by hand, in the order the dashboard shows them.
     */
    public const FIGURES = ['publications', 'linkedin_followers', 'talks', 'applications'];

    /**
     * The plan the figures count toward.
     *
     * @return BelongsTo<GrowthPlan, $this>
     */
    public function growthPlan(): BelongsTo
    {
        return $this->belongsTo(GrowthPlan::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'month' => 'date',
            'publications' => 'integer',
            'linkedin_followers' => 'integer',
            'talks' => 'integer',
            'applications' => 'integer',
        ];
    }
}
