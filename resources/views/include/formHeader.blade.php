@props(['title', 'subtitle' => null, 'showSave' => true])

<x-page-header :title="$title" :subtitle="$subtitle">
    <x-slot:actions>
        <x-button variant="ghost" :href="route('dashboard')" icon="home" class="portal-btn--icon-only" aria-label="Dashboard" />
        @if ($showSave)
            <x-button variant="secondary" type="button" id="backButton">Cancel</x-button>
            <x-button variant="primary" type="submit" id="submitComplaint" data-dismiss="modal">Save</x-button>
        @else
            {{ $actions ?? '' }}
        @endif
    </x-slot:actions>
</x-page-header>
