<?php

use App\Livewire\Forms\CreateEvent;
use function Livewire\Volt\{form};

form(CreateEvent::class);

$save = function () {
    $this->form->store();
}

?>

<x-layouts.app>
    <p>
        RSVPnGo is a <strong>free</strong> to use event RSVP app. There are <strong><em>no signups required</em></strong>. <br>
        As an event host, you can manage your event and send an invite link out via WhatsApp or SMS.
    </p>
    <p>
        To start give your event a name...
    </p>
    @volt
    <div>
        <form wire:submit="save">
            <div class="grid">
                <div class="col">
                    <label for="event-name" >
                        Event name:
                    </label>
                    <input type="text" name="name" wire:model="form.name">
                    @error('form.name') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
                </div>
                <input type="hidden" id="timeZoneInput" name="time_zone" wire:model="form.timeZone">
                @error('form.timeZone') <span class="error" style="color: darkred">{{ $message }}</span> @enderror
            </div>
            <x-turnstile wire:model="form.captcha" />
            <div>
                <button type="submit"@if(! $form->name && ! $form->captcha) disabled="disabled"@endif>Start Inviting &gt;</button>
            </div>
        </form>
    </div>
    @script
    <script>
        Livewire.hook('component.init', ({ component, cleanup }) => {
            component.$wire.form.timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        })
    </script>
    @endscript
    @endvolt
</x-layouts.app>
