<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'habit_id',
        'performed_at',
        'count',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];
}
