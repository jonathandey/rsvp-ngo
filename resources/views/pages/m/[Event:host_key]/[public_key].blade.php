<?php
use function Livewire\Volt\{form};

form(\App\Livewire\Forms\EditEvent::class);

$save = function () {
    $this->form->store();
};

\Livewire\Volt\mount(function () {
    $event = request()->route()->parameter('event');
    $this->form->name = $event->name;
    $this->form->description = $event->description;
    $this->form->setEvent($event);

    if ($event->hasStartDayTime()) {
        $this->form->startDay = $event->start_day->format('Y-m-d');
        $this->form->startTime = $event->start_time->format('H:i');

        $this->form->eventDuration = $event->durationInHours();
    }

    $this->form->eventId = $event->getKey();
});

\Laravel\Folio\render(function (\Illuminate\View\View $view, \App\Models\Event $event) {
    $view->with('publicUrl', $event->getPublicUrl());
    $view->with('privateUrl', $event->getHostUrl());
    $view->with('going', $event->rsvps()->whereGoing()->get());
    $view->with('notGoing', $event->rsvps()->whereNotGoing()->get());
});

?>

<x-layouts.app>
    <div>
        @volt
        <div>
            <div wire:loading>
                Saving...
            </div>
            <form wire:submit="save">
                <div>
                    <label for="event-name">Name</label>
                    <input name="name" id="event-name" wire:model="form.name" autocomplete="false" required>
                    @error('form.name') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                    @if($form->event->hasStartDayTime())
                        <p>
                            <a href="{{ route('event.ical', ['event' => $form->event->public_key]) }}?host_key={{ $form->event->host_key }}">Add to your Calendar</a>
                        </p>
                    @endif
                </div>
                <div>
                    <div class="grid">
                        <div class="col">
                            <label for="start-day">Starts from:</label>
                            <input type="date" id="start-day" name="start_day" wire:model="form.startDay" min="{{ now()->format('Y-m-d') }}" />
                            @error('form.startDay') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                        </div>
                        <div class="col">
                            <label for="start-time">&nbsp;</label>
                            <input type="time" id="start-time" wire:model="form.startTime" name="start_time" />
                            @error('form.startTime') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                        </div>
                        <div class="col">
                            <label for="event-duration">Duration (hours)</label>
                            <input type="text" wire:model="form.eventDuration" id="event-duration">
                            @error('form.eventDuration') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div>
                    <label for="event-description">Description</label>
                    <textarea id="event-description" wire:model="form.description" placeholder="RSVP by, Google Maps link, etc."></textarea>
                    @error('form.description') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                </div>
                <div>
                    <button type="submit">Save</button>
                </div>
            </form>
        </div>
        @script
        <script>
            Livewire.hook('component.init', ({ component, cleanup }) => {
                var today = new Date();
                var HH = String(today.getHours()).padStart(2, '0');
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = today.getFullYear();

                if (! component.$wire.form.startDay) {
                    component.$wire.form.startDay = yyyy + '-' + mm + '-' + dd;
                }
                if (! component.$wire.form.startTime) {
                    component.$wire.form.startTime = HH + ":" + "00";
                }
            })
        </script>
        @endscript
        @endvolt
        <div>
            <label>This is your private event management link - you can get back to this page any time using this link:</label>
            <div>
                <input id="host-url" type="text" value="{{ $privateUrl }}" style="display: inline-block">
                <button class="copy-btn" data-clipboard-target="#host-url">Copy host link</button>
            </div>
        </div>
    </div>
    <div>
        <hr>
        <h2>Shareables</h2>
        <div>
            <label>This is your public invitation link - share this with your guests:</label>
            <div>
                <input id="public-url" type="text" value="{{ $publicUrl }}" style="display: inline-block">
                <button class="copy-btn" data-clipboard-target="#public-url">Copy public link</button>
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
</x-layouts.app>
