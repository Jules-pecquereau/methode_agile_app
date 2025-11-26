<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Task extends Model
{
    protected $fillable = [
        'name',
        'description',
        'expected_minutes',
        'active',
        'start_at'
    ];

    protected $casts = [
        'active' => 'boolean',
        'expected_minutes' => 'integer',
        'start_at' => 'datetime',
    ];

    // S'assurer que les timestamps sont activés
    public $timestamps = true;

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withPivot(['start_date', 'end_date']);
    }

    public function canBeActive(): bool
    {
        return $this->teams()->count() > 0;
    }

    // Accesseur pour formater les dates en français
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->locale('fr_FR');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->locale('fr_FR');
    }

    /**
     * Calcule les segments de temps occupés par la tâche, en excluant les week-ends.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTimeSegments()
    {
        if (!$this->start_at || !$this->expected_minutes) {
            return collect();
        }

        $segments = collect();
        $remainingMinutes = $this->expected_minutes;
        $currentStart = $this->start_at->copy();

        while ($remainingMinutes > 0) {
            // Si on tombe sur un week-end, on avance au lundi suivant 00:00
            if ($currentStart->isWeekend()) {
                $currentStart->next(Carbon::MONDAY)->startOfDay();
            }

            // Trouver le prochain samedi 00:00 (début du week-end)
            $nextWeekend = $currentStart->copy()->next(Carbon::SATURDAY)->startOfDay();

            // Calculer le temps disponible avant le week-end
            $minutesUntilWeekend = $currentStart->diffInMinutes($nextWeekend, false);

            // Si le temps restant tient avant le week-end
            if ($remainingMinutes <= $minutesUntilWeekend) {
                $end = $currentStart->copy()->addMinutes($remainingMinutes);
                $segments->push([
                    'start' => $currentStart->copy(),
                    'end' => $end->copy(),
                ]);
                $remainingMinutes = 0;
            } else {
                // La tâche dépasse le week-end, on coupe au vendredi soir
                $segments->push([
                    'start' => $currentStart->copy(),
                    'end' => $nextWeekend->copy(),
                ]);

                $remainingMinutes -= $minutesUntilWeekend;
                // On reprend le lundi suivant
                $currentStart = $nextWeekend->copy()->addDays(2);
            }
        }

        return $segments;
    }
}
