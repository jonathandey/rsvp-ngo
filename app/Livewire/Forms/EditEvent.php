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

    #[Validate('max:1000|min:2')]
    public $description = '';

    #[Locked]
    public $eventId;

    public function store()
    {
        $this->validate();

        $event = Event::findOrFail($this->eventId);

        $event->fill(
            $this->only('name', 'description')
        );

        $event->save();
    }
}
