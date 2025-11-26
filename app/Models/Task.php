<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $expected_minutes
 * @property \Illuminate\Support\Carbon|null $start_at
 * @property bool $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereExpectedMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Task extends Model
{
    protected $fillable = [
        'name',
        'description',
        'expected_minutes',
        'active',
        'start_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'expected_minutes' => 'integer',
        'start_at' => 'datetime',
    ];

    public $timestamps = true;

    /**
     * @return BelongsToMany<Team, $this>
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withPivot(['start_date', 'end_date']);
    }

    public function canBeActive(): bool
    {
        return $this->teams()->count() > 0;
    }

    public function getCreatedAtAttribute(mixed $value): Carbon
    {
        /** @var Carbon $date */
        $date = Carbon::parse($value);
        $date->locale('fr_FR');

        return $date;
    }

    public function getUpdatedAtAttribute(mixed $value): Carbon
    {
        /** @var Carbon $date */
        $date = Carbon::parse($value);
        $date->locale('fr_FR');

        return $date;
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
