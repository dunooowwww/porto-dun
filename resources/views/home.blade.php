<x-layouts.app>
    <x-slot:header>
        <x-site.navbar />
    </x-slot:header>

    <x-home.hero />
    <x-home.pit-wall :cards="$cards" />

    {{-- Dibangun di M5 --}}
    <x-home.section-stub id="race" :title="setting('race_title', 'Race Progress')" :intro="setting('race_intro')" />
    <x-home.section-stub id="garage" :title="setting('garage_title', 'Garage')" :intro="setting('garage_intro')" />
    {{-- Dibangun di M6 --}}
    <x-home.section-stub id="box-box" :title="setting('contact_title', 'Box, Box')" :intro="setting('contact_intro')" />
</x-layouts.app>
