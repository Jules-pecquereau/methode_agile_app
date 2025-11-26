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
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'expected_minutes' => 'integer',
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
}
