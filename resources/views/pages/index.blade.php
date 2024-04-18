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
            </div>
            <div>
                <button type="submit">Create Invites &gt;</button>
            </div>
        </form>
    </div>
    @endvolt
</x-layouts.app>
