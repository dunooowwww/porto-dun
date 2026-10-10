{{-- 404 (PRD F10): "DNF – Did Not Find". Judul & teks dari Pengaturan Situs → SEO & Footer. --}}
@php
    $title = setting('not_found_title', 'DNF – Did Not Find');
@endphp

<x-layouts.app :title="$title" :description="setting('not_found_text')" :noindex="true">
    <x-slot:header>
        <x-site.navbar />
    </x-slot:header>

    <section class="mx-auto grid max-w-site items-center gap-12 px-4 pt-28 pb-24 md:grid-cols-[1fr_1.1fr] md:px-8 md:pt-40 md:pb-36"
             aria-labelledby="dnf-judul">
        <div>
            <p class="label-mono text-red-dark">404 · DNF</p>
            <h1 id="dnf-judul" class="mt-4 text-[clamp(2.5rem,8vw,4.5rem)] font-black">{{ $title }}</h1>
            @if (setting('not_found_text'))
                <p class="mt-6 max-w-md text-lg text-muted">{{ setting('not_found_text') }}</p>
            @endif
            <a href="{{ url('/') }}" class="btn-primary mt-10">
                <span class="btn-dot" aria-hidden="true"></span>
                Balik ke pit
                <x-site.icon name="arrow-right" class="size-4" />
            </a>
        </div>

        <img src="{{ asset('images/dnf.webp') }}" width="595" height="220" fetchpriority="high"
             alt="Maskot {{ setting('display_name') }} duduk di samping mobil balap #{{ setting('racing_number', 12) }} yang mogok sambil menggaruk kepala"
             class="w-full max-w-xl justify-self-start md:justify-self-end">
    </section>
</x-layouts.app>
