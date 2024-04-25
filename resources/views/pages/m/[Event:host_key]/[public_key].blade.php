<?php
use function Livewire\Volt\{form};

\Laravel\Folio\middleware(\App\Http\Middleware\HostKeyPublicKeyMatch::class);

form(\App\Livewire\Forms\EditEvent::class);

$save = function () {
    $this->form->store();
};

$update = function ($event, $name, $value) {
    $event->{$name} = $value;
    $event->save();

    return $event;
};

$event = request()->route()->parameter('event');

\Livewire\Volt\state(
    [
        'event' => fn () => $event,
        'privateUrl' => fn () => $event->getHostUrl(),
        'publicUrl' => fn () => $event->getPublicUrl(),
        'going' => fn() => $event->rsvps()->whereGoing()->get(),
        'notGoing' => fn() => $event->rsvps()->whereNotGoing()->get(),
    ]
);

\Livewire\Volt\mount(function () {
    $event = request()->route()->parameter('event');
    $this->form->name = $event->name;
    $this->form->description = $event->description;
    $this->form->setEvent($event);
    $this->form->refreshInvitationText();

    if ($event->hasStartDayTime()) {
        $this->form->startDay = $event->start_day->format('Y-m-d');
        $this->form->startTime = $event->start_time->format('H:i');

        $this->form->eventDuration = $event->durationInHours();
    }

    $this->form->eventId = $event->getKey();
});

\Livewire\Volt\updated(
    [
        'form.name' => function ($val) use ($update) {
            $event = $update($this->event, 'name', $val);
            $this->form->setEvent($event);
            $this->form->refreshInvitationText();
        },
        'form.description' => function ($val) use ($update) {
            $event = $update($this->event, 'description', $val);
            $this->form->setEvent($event);
            $this->form->refreshInvitationText();
        },
    ]
);

?>

<x-layouts.app>
    @volt
    <div>
        <div>
            <div>
                <div wire:loading>
                    Saving...
                </div>
                <form wire:submit="save">
                    <div>
                        <label for="event-name">What's the event?</label>
                        <input name="name" id="event-name" wire:model="form.name" autocomplete="false" required>
                        @error('form.name') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                        @if($event->hasStartDayTime())
                            <p>
                                <a href="{{ route('event.ical', ['event' => $event->public_key]) }}?host_key={{ $event->host_key }}">Add to your Calendar</a>
                            </p>
                        @endif
                    </div>
                    <div>
                        <div class="grid">
                            <div class="col">
                                <label for="start-day">When is your event?</label>
                                <input type="date" id="start-day" name="start_day" placeholder="Date" wire:model="form.startDay" min="{{ now()->format('Y-m-d') }}" />
                                @error('form.startDay') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                            </div>
                            <div class="col">
                                <label for="start-time">When should people arrive form?</label>
                                <input type="time" id="start-time" placeholder="Set a Time" wire:model="form.startTime" name="start_time" />
                                @error('form.startTime') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                            </div>
                            <div class="col">
                                <label for="event-duration">How many hours is it on for?</label>
                                <input type="number" wire:model="form.eventDuration" id="event-duration">
                                @error('form.eventDuration') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="event-description">Description</label>
                        <textarea id="event-description" wire:model.blur="form.description" placeholder="RSVP by, Google Maps link, etc."></textarea>
                        @error('form.description') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                        <p style="font-size: 11px">Markdown text supported by the Description field - <a href="https://commonmark.org/help/" target="_blank" rel="nofollow noopener">How to use markdown to format your text</a></p>
                    </div>
                    <div>
                        <button type="submit">Save</button>
                    </div>
                </form>
            </div>
            <div>
                <label>This is your private event management link - you can get back to this page any time using this link:</label>
                <div>
                    <input id="host-url" type="text" value="{{ preg_replace("/https?:\/\//", '', $privateUrl) }}" style="display: inline-block">
                    <button class="copy-btn" data-clipboard-text="{{ $privateUrl }}">Copy host link</button>
                </div>
            </div>
        </div>
        <div>
            <hr>
            <h2>Shareables</h2>
            <div class="grid">
                <div class="col">
                    <label>This is your public invitation link.<br>Share this with your guests:</label>
                    <div>
                        <input id="public-url" type="text" value="{{ preg_replace("/https?:\/\//", '', $publicUrl) }}">
                        <button class="copy-btn" data-clipboard-text="{{ $publicUrl }}">Copy invite link</button>
                    </div>
                </div>
                <div class="col">
                    <label>Suggested invitation:</label>
                    <textarea id="invitation-text" wire:model="form.invitationText"></textarea>
                    <button class="copy-btn" data-clipboard-target="#invitation-text">Copy Invite</button>
                </div>
            </div>
            <hr>
        </div>
        <div>
            <div>
                <details open>
                    <summary>Going  ({{ $going->count() }})</summary>
                    <ul>
                        @foreach($going as $rsvp)
                            <li wire:key="{{ $rsvp->getKey() }}">
                                {{ $rsvp->name }} <em style="font-size: 11px">(Responded on {{ $rsvp->created_at->format('jS M y - H:i') }})</em>
                            </li>
                        @endforeach
                    </ul>
                </details>
            </div>
            <div>
                <details>
                    <summary>Not Going ({{ $notGoing->count() }})</summary>
                    <ul>
                        @foreach($notGoing as $rsvp)
                            <li wire:key="{{ $rsvp->getKey() }}">
                                {{ $rsvp->name }} <em style="font-size: 11px">(Responded on {{ $rsvp->created_at->format('jS M y - H:i') }})</em>
                            </li>
                        @endforeach
                    </ul>
                </details>
            </div>
        </div>
    </div>
    @endvolt
</x-layouts.app>
