<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property CarbonInterface $start_day
 * @property CarbonInterface $start_time
 */
class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        // 'start_day' => 'date',
        'start_time' => 'datetime',
        'end_day' => 'date',
        'end_time' => 'datetime',
    ];

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

    protected function startDay(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value, new \DateTimeZone($this->time_zone)) : null,
            set: fn ($value) => Carbon::parse($value)->startOfDay()->format('Y-m-d H:i:s')
        );
    }

    protected function endDay(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value, new \DateTimeZone($this->time_zone)) : null,
            set: fn ($value) => Carbon::parse($value)->startOfDay()->format('Y-m-d H:i:s')
        );
    }

    public function getPublicUrl(): string
    {
        return url('/e/' . $this->public_key);
    }

    public function getHostUrl(): string
    {
        return url('/m/' . $this->host_key . '/' . $this->public_key);
    }

    public function hasStartDayTime(): bool
    {
        return $this->start_day && $this->start_time;
    }

    public function startDateTime(): CarbonInterface
    {
        $date =  $this->start_day->clone();
        $date->addHour($this->start_time->hour);
        $date->addMinute($this->start_time->minute);
        $date->timezone($this->time_zone);

        return $date;
    }

    public function endDateTime(): CarbonInterface
    {
        $date =  $this->end_day->clone();
        $date->addHour($this->end_time->hour);
        $date->addMinute($this->end_time->minute);
        $date->timezone($this->time_zone);

        return $date;
    }

    public function calculateEndDateTime(int $hours): CarbonInterface
    {
        $date =  $this->startDateTime()->clone();

        return $date->addHour($hours);
    }

    public function durationInHours(): int
    {
        return $this->startDateTime()->diffInHours($this->endDateTime());
    }

    public function ended(): bool
    {
        return $this->endDateTime()->isPast();
    }
}
