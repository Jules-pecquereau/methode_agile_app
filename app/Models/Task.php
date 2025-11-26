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
 * @property bool $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereExpectedMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Task extends Model
{
    protected $fillable = [
        'name',
        'description',
        'expected_minutes',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'expected_minutes' => 'integer',
    ];

    public $timestamps = true;

    /**
     * @return BelongsToMany<Team, $this>
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
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
}
