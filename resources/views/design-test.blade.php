{{-- Halaman tes M1: cek font, warna, radius, dan Alpine. Diganti halaman Home di M4. --}}
@php
    $colors = [
        ['bg', '#F7F7F5', 'bg-bg', 'Background halaman'],
        ['card', '#FFFFFF', 'bg-card', 'Kartu, form, navbar'],
        ['ink', '#111111', 'bg-ink', 'Heading, teks utama, tombol utama'],
        ['muted', '#6B6B6B', 'bg-muted', 'Teks sekunder'],
        ['line', '#E6E6E3', 'bg-line', 'Border tipis 1px'],
        ['red', '#E10600', 'bg-red', 'Aksen / sinyal'],
        ['red-dark', '#B80500', 'bg-red-dark', 'Teks kecil merah, hover'],
    ];
@endphp

<x-layouts.app title="Tes Desain – Dun #12" description="Halaman tes token desain portofolio Dun #12.">
    <main class="mx-auto max-w-site px-4 py-16 md:px-8 md:py-24">

        {{-- Contoh hero: teks rata kiri, karakter di kanan --}}
        <section class="grid items-center gap-12 md:grid-cols-[1.4fr_1fr]" aria-labelledby="judul-tes">
            <div>
                <p class="label-mono text-red-dark">Lights out · Tes desain M1</p>
                <h1 id="judul-tes" class="mt-4 text-[clamp(3rem,9vw,6rem)] font-black">
                    Halo, aku
                    <span class="relative inline-block">
                        Dun
                        <span class="absolute inset-x-0 -bottom-1 h-1.5 rounded-pill bg-red" aria-hidden="true"></span>
                    </span>
                    <span class="label-mono align-top text-red-dark">#12</span>
                </h1>
                <p class="mt-6 max-w-xl text-lg text-ink">
                    Mahasiswa SI di siang hari, ngulik AI sampai <em class="font-display font-extrabold italic tracking-display">lights out</em>.
                </p>
                <p class="mt-3 max-w-xl text-muted">
                    Paragraf ini memakai Inter 16–18px dengan line-height 1.6. Teks sekunder memakai warna
                    <span class="font-mono text-sm">muted</span>. Isi asli nanti diambil dari konten.md lewat database.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3" x-data="{ cek: false }">
                    <a href="#token" class="btn-primary">
                        <span class="btn-dot" aria-hidden="true"></span>
                        Lihat Garage
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                    <button type="button" class="btn-secondary" @click="cek = !cek" :aria-pressed="cek.toString()">
                        Tes Alpine.js
                    </button>
                    <p class="label-mono text-muted" x-show="cek" x-cloak>Alpine jalan · Box, box</p>
                </div>
            </div>

            <img src="{{ asset('images/hero.webp') }}" width="214" height="273"
                 alt="Maskot Dun memakai hoodie putih dan topi hitam #12, duduk bersila sambil memegang laptop"
                 class="mx-auto w-48 md:w-auto">
        </section>

        {{-- Skala heading --}}
        <section class="mt-24 border-t border-line pt-12" aria-labelledby="judul-skala">
            <p class="label-mono text-muted">Tipografi</p>
            <h2 id="judul-skala" class="mt-3 text-4xl md:text-5xl">H2 · Archivo ExtraBold</h2>
            <h3 class="mt-4 text-2xl md:text-3xl">H3 · Archivo, tracking rapat</h3>
            <p class="mt-4 font-mono text-sm text-muted">Body: Inter · Label: JetBrains Mono</p>

            <div class="mt-6 flex flex-wrap gap-4">
                @foreach (['SECTOR 1', 'P1', 'LAP 03', '#12'] as $label)
                    <span class="label-mono rounded-chip border border-line bg-card px-3 py-1.5 text-ink">{{ $label }}</span>
                @endforeach
            </div>
        </section>

        {{-- Contoh kartu dengan label telemetri --}}
        <section class="mt-16 grid gap-4 md:grid-cols-3" aria-label="Contoh kartu">
            <article class="rounded-card border border-line bg-card p-6 md:col-span-2">
                <p class="label-mono text-red-dark">Sector 1</p>
                <h3 class="mt-3 text-2xl">Kartu radius 24px</h3>
                <p class="mt-2 text-muted">Putih di atas off-white, border 1px warna line, tanpa shadow tebal.</p>
            </article>
            <article class="flex items-end justify-between rounded-card border border-line bg-card p-6">
                <div>
                    <p class="label-mono text-muted">Sector 2</p>
                    <h3 class="mt-3 text-2xl">1/3</h3>
                </div>
                <img src="{{ asset('images/builder.webp') }}" width="238" height="274" loading="lazy"
                     alt="Maskot Dun berlutut memperbaiki model mobil balap dengan obeng" class="w-20">
            </article>
        </section>

        {{-- Token warna --}}
        <section id="token" class="mt-16" aria-labelledby="judul-warna">
            <p class="label-mono text-muted">Token warna</p>
            <h2 id="judul-warna" class="mt-3 text-3xl">Hanya 7 warna</h2>
            <ul class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($colors as [$name, $hex, $class, $use])
                    <li class="rounded-card border border-line bg-card p-3">
                        <div class="{{ $class }} h-16 rounded-chip border border-line"></div>
                        <p class="label-mono mt-3 text-ink">{{ $name }}</p>
                        <p class="font-mono text-xs text-muted">{{ $hex }}</p>
                        <p class="mt-1 text-sm text-muted">{{ $use }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    </main>
</x-layouts.app>
