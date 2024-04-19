<?php

namespace App\Livewire\Forms;

use App\Models\Event;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateEvent extends Form
{
    #[Validate('required|max:255|min:3')]
    public $name = '';

    #[Validate('required|timezone:all')]
    public $timeZone = 'UTC';

    public function store()
    {
        $this->validate();

        $event = Event::create(
            [
                'name' => $this->name,
                'time_zone' => $this->timeZone,
            ]
        );

        return redirect()->to('/m/' . $event->host_key . '/' . $event->public_key);
    }
}
