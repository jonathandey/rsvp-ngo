<?php

namespace App\Livewire\Forms;

use App\Models\Event;
use App\Models\Rsvp as RsvpModel;
use Livewire\Attributes\Validate;
use Livewire\Form;

class Rsvp extends Form
{
    #[Validate('required|max:512|min:2')]
    public $name = '';

    #[Validate('required|exists:events,public_key')]
    public $eventPublicKey = '';

    public $going = [];

    public $notGoing = [];

    public $submitted = false;

    public $message = '';

    public function going(): void
    {
        $this->validate();

        $event = Event::wherePublicKey($this->eventPublicKey)->first();

        $rsvp = $event->rsvps()->make(
            [
                'name' => $this->name,
            ]
        );

        $rsvp->amGoing()->save();

        $this->going[] = $rsvp;

        $this->submitted = true;
        $this->message = "You're going to " . $event->name ."!";


        $this->reset('name');
    }

    public function notGoing(): void
    {
        $this->validate();

        $event = Event::wherePublicKey($this->eventPublicKey)->first();

        $rsvp = $event->rsvps()->make(
            [
                'name' => $this->name,
            ]
        );

        $rsvp->amNotGoing()->save();

        $this->notGoing[] = $rsvp;

        $this->submitted = true;
        $this->message = "Thanks! Maybe next time...";

        $this->reset('name');
    }
}
