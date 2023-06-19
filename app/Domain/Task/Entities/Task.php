<?php

namespace App\Domain\Task\Entities;

use App\Domain\Shared\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperTask
 */
class Task extends Model
{
    use UsesUuid;

    public const UPDATED_AT = null;

    protected $table = 'task';
    protected $fillable = [
        'id',
        'title',
        'description',
        'parent_id',
        'user_id',
        'status',
        'priority',
        'created_at',
        'completed_at'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    /**
     * @return BelongsTo
     */
    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function subTasks(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    /**
     * @param Builder $query
     * @param string $title
     * @return Builder
     */
    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->whereFullText('title', $title);
    }

    /**
     * @param Builder $query
     * @param int $priorityFrom
     * @return Builder
     */
    public function scopePriorityFrom(Builder $query, int $priorityFrom): Builder
    {
        return $query->where('priority', '>=', $priorityFrom);
    }

    /**
     * @param Builder $query
     * @param int $priorityTo
     * @return Builder
     */
    public function scopePriorityTo(Builder $query, int $priorityTo): Builder
    {
        return $query->where('priority', '<=', $priorityTo);
    }

    /**
     * @param Builder $query
     * @param string $status
     * @return Builder
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
