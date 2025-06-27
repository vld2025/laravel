<x-filament-panels::page.simple>
    <x-slot name="heading">
        {{-- TITOLO VUOTO - NO "ACCEDI" --}}
    </x-slot>

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page.simple>
