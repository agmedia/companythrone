<?php

namespace App\Models\Back\Marketing;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BannerSchedule extends Model
{
    protected $table = 'banner_schedules';

    protected $fillable = [
        'banner_id',
        'start_date',
        'end_date',
        'position',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'position'   => 'integer',
    ];

    /**
     * Relacija: pripada banneru.
     */
    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class);
    }

    /**
     * Scope: raspored koji vrijedi za zadani datum (default danas).
     */
    public function scopeForDate(Builder $query, ?Carbon $date = null): Builder
    {
        $date ??= Carbon::today();

        return $query
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date);
    }

    /**
     * Scope: rasporedi koji se preklapaju s [from, to] intervalom.
     */
    public function scopeBetween(Builder $query, Carbon $from, Carbon $to): Builder
    {
        // overlap logika: start <= to AND end >= from
        return $query
            ->whereDate('start_date', '<=', $to)
            ->whereDate('end_date', '>=', $from);
    }

    /**
     * Scope: pozicija (slot na naslovnici).
     */
    public function scopeAtPosition(Builder $query, int $position): Builder
    {
        return $query->where('position', $position);
    }

    /**
     * Scope: trenutno aktivni (po datumu).
     */
    public function scopeRunning(Builder $query, ?Carbon $date = null): Builder
    {
        return $this->scopeForDate($query, $date);
    }

    /**
     * Scope: samo rasporedi čiji je banner "active".
     */
    public function scopeWithActiveBanner(Builder $query): Builder
    {
        return $query->whereHas('banner', fn (Builder $b) => $b->where('status', 'active'));
    }

    /**
     * Je li raspored aktivan za zadani datum (default danas)?
     */
    public function isRunning(?Carbon $date = null): bool
    {
        $date ??= Carbon::today();
        return $this->start_date->lte($date) && $this->end_date->gte($date);
    }

    /**
     * Computed atribut: is_active (true/false).
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->isRunning();
    }

    /**
     * Preostali dani do kraja trajanja (može biti i 0).
     */
    public function getRemainingDaysAttribute(): int
    {
        $today = Carbon::today();
        return max(0, $today->diffInDays($this->end_date, false));
    }

    /**
     * Zadani poredak – po poziciji, pa najnoviji start.
     */
    public function scopeDefaultOrder(Builder $query): Builder
    {
        return $query->orderBy('position')->orderByDesc('start_date');
    }
}
