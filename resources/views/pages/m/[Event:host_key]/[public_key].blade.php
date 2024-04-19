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
