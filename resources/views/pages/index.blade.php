<?php

use App\Livewire\Forms\CreateEvent;
use function Livewire\Volt\{form};

form(CreateEvent::class);

$save = function () {
    $this->form->store();
}

?>

<x-layouts.app>
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
            <div>
                <button type="submit">Start Inviting &gt;</button>
            </div>
        </form>
    </div>
    @script
    <script>
        document.addEventListener('livewire:init', function () {

        })

        Livewire.hook('component.init', ({ component, cleanup }) => {
            console.log()
            component.$wire.form.timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        })
        document.getElementById('timeZoneInput').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
    </script>
    @endscript
    @endvolt
</x-layouts.app>
