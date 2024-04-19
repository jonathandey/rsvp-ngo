<?php

namespace App\Livewire\Forms;

use App\Models\Event;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EditEvent extends Form
{
    #[Validate('required|max:255|min:3')]
    public $name = '';

    #[Validate('max:1000')]
    public $description = '';

    #[Validate('date|nullable|required_with:startTime,eventDuration')]
    public $startDay;

    #[Validate('date_format:H:i|nullable|required_with:startDay,eventDuration')]
    public $startTime;

    #[Validate('integer|nullable|required_with:startDay,startTime')]
    public $eventDuration;

    #[Locked]
    public $eventId;

    public Event $event;

    public function store()
    {
        $this->validate();

        $event = Event::findOrFail($this->eventId);

        $event->name = $this->name;
        $event->description = $this->description;

        if ($this->startDay) {
            $event->start_day = $this->startDay;

        }

        if ($this->startTime) {
            $event->start_time = $this->startTime;
        }

        if ($this->eventDuration) {
            $endDateTime = $event->calculateEndDateTime($this->eventDuration);
            $event->end_day = $endDateTime->format('Y-m-d');
            $event->end_time = $endDateTime->format('H:i');
        }

        $this->setEvent($event);

        $event->save();
    }

    public function setEvent(Event $event)
    {
        $this->event = $event;
    }
}
