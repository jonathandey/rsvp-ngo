<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rsvp extends Model
{
    use HasFactory;

    protected $casts = [
        'going' => 'boolean',
    ];

    protected $guarded = [];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function amGoing(): self
    {
        $this->going = true;

        return $this;
    }

    public function amNotGoing(): self
    {
        $this->going = false;

        return $this;
    }

    public function scopeWhereGoing($query)
    {
        $query->where('going', true);
    }

    public function scopeWhereNotGoing($query)
    {
        $query->where('going', false);
    }
}
