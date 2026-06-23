<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = ['name', 'priority_id', 'due_date'];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    /**
     * Will be used to sort the tasks by task name, time, or
     * priority conditionally.
     */
    public function scopeHandleSort(Builder $query, string $column)
    {
        $query
            ->when($column === 'name', function ($query) {
                $query->orderBy('name');
            })
            ->when($column === 'time', function ($query) {
                $query->latest();
            })
            ->when($column === 'priority', function ($query) {
                $query->orderByRaw('CASE WHEN priority_id IS NULL THEN 1 ELSE 0 END, 
        priority_id ASC');
            });
    }

    /**
     * Used for filtering tasks by date.
     */
    public function scopeHandleFilter(Builder $query, ?string $dueDate)
    {
        $query
            ->when($dueDate === 'today', function ($query) {
                $from = now()->startOfDay();
                $to = $from->copy()->endOfDay();
                $query->whereBetween('due_date', [$from, $to])
                    ->orWhereNull('due_date');
            })
            ->when($dueDate === 'next3d', function ($query) {
                $from = now()->startOfDay();
                $to = now()->addDays(3)->endOfDay();
                $query->whereBetween('due_date', [$from, $to]);
            })
            ->when($dueDate === 'nextweek', function ($query) {
                $from = now()->startOfDay();
                $to = now()->addWeek()->endOfDay();
                $query->whereBetween('due_date', [$from, $to]);
            })
            ->when($dueDate === 'overdue', function ($query) {
                $query->where('due_date', '<', now()->startOfDay());
            });
    }
}
