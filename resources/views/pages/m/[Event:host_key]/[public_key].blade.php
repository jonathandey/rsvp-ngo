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
                    <input name="name" id="event-name" wire:model="form.name">
                    @error('form.name') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="event-description">Description</label>
                    <textarea id="event-description" wire:model="form.description"></textarea>
                    @error('form.description') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                </div>
                <div>
                    <button type="submit">Save</button>
                </div>
            </form>
        </div>
        @endvolt
    </div>
    <div>
        <hr>
        <div>
            <label>This is your public invitation link - share this with your guests:</label>
            <input id="public-url" type="text" value="{{ $publicUrl }}"> <button class="btn" data-clipboard-text="{{ $publicUrl }}">[copy]</button>
        </div>
        <div>
            <label>This is your private event management link - save this to update event details and view responses:</label>
            <input type="text" value="{{ $privateUrl }}"> <span class="clipboard-copy">[copy]</span>
        </div>
        <hr>
    </div>
    <div>
        <div>
            <details open>
                <summary>Going  ({{ $going->count() }})</summary>
                <ul>
                    @foreach($going as $attendee)
                        <li wire:key="{{ $attendee->getKey() }}">{{ $attendee->name }}</li>
                    @endforeach
                </ul>
            </details>
        </div>
        <div>
            <details>
                <summary>Not Going ({{ $notGoing->count() }})</summary>
                <ul>
                    @foreach($notGoing as $invitee)
                        <li wire:key="{{ $attendee->getKey() }}">{{ $invitee->name }}</li>
                    @endforeach
                </ul>
            </details>
        </div>
    </div>
</x-layouts.app>
