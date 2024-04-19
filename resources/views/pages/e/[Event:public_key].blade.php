<?php
use function Livewire\Volt\{form};

form(\App\Livewire\Forms\Rsvp::class);

$going = function () {
    $this->form->going();
};

$notGoing = function () {
    $this->form->notGoing();
};

\Livewire\Volt\mount(function () {
    $event = request()->route()->parameter('event');
    $this->form->eventPublicKey = $event->public_key;
    $this->form->going = $event->rsvps()->whereGoing()->get();
    $this->form->notGoing = $event->rsvps()->whereNotGoing()->get();
    $this->form->submitted = false;
});

\Laravel\Folio\render(function (\Illuminate\View\View $view, \App\Models\Event $event) {
});

?>

<x-layouts.app>
    <div>
        <div>
            <h1>{{ $event->name }}</h1>
            @if($event->description)
            <div>
                {{ nl2br($event->description) }}
            </div>
            @endif
        </div>
        <div>
            <hr>
            <h2>RSVP</h2>
            @volt
            <div>
                @if(! $form->submitted)
                <div wire:loading>
                    Sending your RSVP...
                </div>
                <form>
                    @error('form.eventPublicKey') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                    <div class="grid-center grid">
                        <div class="col-12">
                            <label for="guest-name" style="font-size: 24px">My name is...</label>
                        </div>
                        <div class="col-12">
                            <input type="text" id="guest-name" name="name" wire:model="form.name" style="width: 50%" placeholder="John Smith">
                            @error('form.name') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid-2_xs-1">
                        <div class="col">
                            <button type="button" wire:click.prevent="going">and I am going</button>
                        </div>
                        <div class="col">
                            <button type="button" wire:click.prevent="notGoing">and I am NOT going</button>
                        </div>
                    </div>
                    <input type="hidden" name="event_key" wire:model="form.eventPublicKey">
                </form>
                @else
                    <h3 style="text-align: center">{{ $form->message }}</h3>
                @endif
                <div>
                    <div>
                        <details open>
                            <summary>Going  ({{ $form->going->count() }})</summary>
                            <ul>
                                @foreach($form->going as $attendee)
                                    <li wire:key="{{ $attendee->getKey() }}">{{ $attendee->name }}</li>
                                @endforeach
                            </ul>
                        </details>
                    </div>
                    <div>
                        <details>
                            <summary>Not Going ({{ $form->notGoing->count() }})</summary>
                            <ul>
                                @foreach($form->notGoing as $invitee)
                                    <li wire:key="{{ $invitee->getKey() }}">{{ $invitee->name }}</li>
                                @endforeach
                            </ul>
                        </details>
                    </div>
                </div>
            </div>
            @endvolt
        </div>
    </div>
</x-layouts.app>
