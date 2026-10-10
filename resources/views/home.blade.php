<x-layouts.app>
    <x-slot:head>
        <x-site.lights-script />
    </x-slot:head>

    <x-slot:overlay>
        <x-site.lights-out />
    </x-slot:overlay>

    <x-slot:header>
        <x-site.navbar />
    </x-slot:header>

    <x-home.hero />
    <x-home.pit-wall :cards="$cards" />

    <x-home.race :tabs="$race" />
    <x-home.garage :projects="$projects" />
    <x-home.box-box />

    <x-slot:floating>
        <x-site.mascot :messages="$mascot" />
    </x-slot:floating>
</x-layouts.app>
