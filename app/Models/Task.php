<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
class Task extends Model
{
    use HasFactory , SoftDeletes;
       //
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'priority',
        'status',
        'user_id',
        'is_readed'
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_OVERDUE = 'overdue';

    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_LOW = 'low';

    /**
     * Get the user that owns the Task
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * mark as Send 
     */
    public function scopeSent(Builder $query)
    {
        return $query->where('is_sent',true);
    }
    /**
     * mark as not Sent 
     */
    public function scopeNotSent(Builder $query)
    {
        return $query->where('is_sent',false);
    }

    public function isWithinFinal24Hours()
    {
        return now()->lessThan($this->due_date) &&
               $this->due_date->diffInHours(now()) <= 24;
    }
    
    /**
     * Get Task By User Id
     */
    public function scopeForUser(Builder $query): Builder
    {
        return $query->where('user_id',auth()->user()->id);
    }

     /**
     * Filter By Status 
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
       return $query->when(!empty($status), fn($q) => $q->where('status', $status));
    }

    
     /**
     * Filter By Priority
     */
    
    public function scopePriority(Builder $query , ?string $priority)
    {
           return $query->when(!empty($priority), fn($q) => $q->where('priority', $priority));
    }

    
    /**
     * Filter tasks by date from and to
     */
    public function scopeDueBetween(Builder $query, ?string $from, ?string $to): Builder
    {
         return $query->when(!empty($from), fn($q) => $q->where('due_date', '>=', $from))
                ->when(!empty($to), fn($q) => $q->where('due_date', '<=', $to));
    }

    /**
     * Order tasks by priority (high to low by default)
     */
    public function scopeOrderByPriority(Builder $query, ?string $direction = 'asc'): Builder
    {
        $order = match ($direction) {
            'desc' => "CASE priority 
                        WHEN 'high' THEN 1 
                        WHEN 'medium' THEN 2 
                        WHEN 'low' THEN 3 
                    END DESC",
            default => "CASE priority 
                       WHEN 'high' THEN 1 
                       WHEN 'medium' THEN 2 
                       WHEN 'low' THEN 3 
                   END ASC"
        };

        return $query->orderByRaw($order);
    }

    /**
     * Order tasks by field
     */
    public function scopeOrderByField(Builder $query, ?string $field, ?string $direction = 'asc'): Builder
    {
        $validFields = ['due_date', 'priority'];
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';

        return $query->when($field && in_array($field, $validFields), 
            fn($q) => $field == 'priority' 
                ? $q->orderByPriority($direction)
                : $q->orderBy($field, $direction)
        );
    }

    /**
     * Filter tasks by search term
     */
    public function scopeSearch(Builder $query, ?string $term)
    {
        return $query->when(!empty($term), function ($q) use ($term) {
        $q->where(function ($query) use ($term) {
            $query->where('title', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
        });
    });
    }
}
