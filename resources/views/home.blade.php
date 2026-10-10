<x-layouts.app>
    <x-slot:header>
        <x-site.navbar />
    </x-slot:header>

    <x-home.hero />
    <x-home.pit-wall :cards="$cards" />

    <x-home.race :timeline="$timeline" />
    <x-home.garage :projects="$projects" />
    {{-- Dibangun di M6 --}}
    <x-home.section-stub id="box-box" :title="setting('contact_title', 'Box, Box')" :intro="setting('contact_intro')" />
</x-layouts.app>
