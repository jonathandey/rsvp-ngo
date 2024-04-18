<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'id',
        'host_key',
    ];

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            $event->public_key = Str::random(8);
            $event->host_key = Str::random(8);
        });
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function scopeWherePublicKey($query, $publicKey)
    {
        $query->where('public_key', $publicKey);
    }

    public function getPublicUrl(): string
    {
        return url('/e/' . $this->public_key);
    }

    public function getHostUrl(): string
    {
        return url('/m/' . $this->host_key . '/' . $this->public_key);
    }
}
