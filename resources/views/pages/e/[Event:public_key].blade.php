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

    $this->form->eventEnded = $event->ended();
});

\Laravel\Folio\render(function (\Illuminate\View\View $view, \App\Models\Event $event) {

});

?>

<x-layouts.app>
    <div>
        <div>
            <h1>{{ $event->name }}</h1>
            @if($event->ended())
                <h4 style="color: orangered;">This event was in the past and is now concluded.</h4>
            @elseif($event->hasStartDayTime())
                <p>
                    From <time datetime="{{ $event->startDateTime()->format('H:i') }}">{{ $event->startDateTime()->format('H:i') }}</time> on the <time datetime="{{ $event->startDateTime()->format('Y-m-d') }}">{{ $event->startDateTime()->format('jS M y') }}</time> <em>({{ $event->time_zone }})</em> for about <time datetime="PT{{ $event->durationInHours() }}H00M">{{ $event->durationInHours() }} hours</time>
                    &bull; <a href="{{ route('event.ical', ['event' => $event->public_key]) }}">Add to your Calendar</a>
                </p>
            @endif
            @if($event->description)
            <div>
                <x-markdown>{{ $event->description }}</x-markdown>
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
                            <input type="text" id="guest-name" name="name" wire:model="form.name" style="width: 50%" placeholder="John Smith"@if($form->eventEnded) disabled="disabled"@endif>
                            @error('form.name') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid-2_xs-1">
                        <div class="col">
                            <button type="button" wire:click.prevent="going"@if($form->eventEnded) disabled="disabled"@endif>and I am going</button>
                        </div>
                        <div class="col">
                            <button type="button" wire:click.prevent="notGoing"@if($form->eventEnded) disabled="disabled"@endif>and I am NOT going</button>
                        </div>
                    </div>
                    <input type="hidden" name="event_key" wire:model="form.eventPublicKey">
                </form>
                @else
                    <x-markdown>{{ $form->message }}</x-markdown>
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
