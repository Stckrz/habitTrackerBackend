<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @method static self create(array $attributes = [])
 * @method static \App\Models\Habit create(array $attributes = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Habit where(string $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Habit orderBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Habit get(array $columns = ['*'])
 */
class Habit extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'frequency',
        'interval',
        'start_date',
        'end_date',
        'goal_count',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Get all of the logs for this habit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    //returns the latest log for this habit
    public function latestLog(): HasOne
    {
        return $this->hasOne(HabitLog::class)
            ->latestOfMany('performed_at');
    }

}
