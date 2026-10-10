{{-- Pit Wall (PRD F4): bento grid, ukuran kartu tidak seragam (2/3 + 1/3, lalu 1/3 + 2/3). --}}
@props(['cards' => []])

@php
    // Lebar kartu (dari 3 kolom) per jumlah kartu, supaya grid selalu penuh tanpa lubang.
    $spans = match (count($cards)) {
        1 => [3],
        2 => [2, 1],
        3 => [2, 1, 3],
        default => [2, 1, 1, 2],
    };
    $spanClass = [1 => 'md:col-span-1', 2 => 'md:col-span-2', 3 => 'md:col-span-3'];
@endphp

{{-- Section berselang-seling: latar putih (card) ↔ off-white (bg); kartu di dalamnya kebalikannya. --}}
<section id="pit-wall" aria-labelledby="pit-wall-judul" class="scroll-mt-24 border-y border-line bg-card">
    <div class="mx-auto max-w-site px-4 py-16 md:px-8 md:py-24">
        <x-home.section-heading id="pit-wall-judul" :title="setting('pit_wall_title', 'Pit Wall')" :intro="setting('pit_wall_intro')" />

        @if (count($cards) === 0)
            <p class="mt-10 rounded-card border border-line bg-bg p-8 text-muted">
                Kartu Pit Wall belum diisi.
            </p>
        @else
            <div class="mt-12 grid gap-4 md:grid-cols-3">
                @foreach ($cards as $i => $card)
                    @php $wide = ($spans[$i] ?? 1) > 1; @endphp
                    {{-- Kartu berlapis: luar = judul, dalam = bingkai abu + panel putih berisi teks & maskot.
                         Saat di-hover (mouse), kartu miring dan panel dalam "terangkat" keluar (lihat .tilt di app.css). --}}
                    <article data-reveal data-tilt style="--reveal-delay: {{ min($i + 1, 6) * 60 }}ms" @class([
                        'group flex flex-col gap-5 rounded-card border border-line bg-bg p-3 transition duration-300 ease-out-race hover:border-red/40',
                        $spanClass[$spans[$i] ?? 1],
                    ])>
                        <header class="px-3 pt-3 md:px-5 md:pt-5">
                            <p class="label-mono text-red-dark">{{ $card['sector'] }}</p>
                            <h3 class="mt-3 text-2xl md:text-3xl">{{ $card['title'] }}</h3>
                        </header>

                        <div data-tilt-frame class="flex grow rounded-card bg-line/60 p-1.5">
                            {{-- Panel padat ala portoanam, maskot besar menempel di dasar panel:
                                 kartu lebar (mulai sm) → teks kiri-bawah, maskot absolute di kanan;
                                 kartu sempit & semua kartu di HP → teks di atas, maskot di bawahnya (rata kanan). --}}
                            <div data-tilt-layer @class([
                                'relative flex min-h-60 grow flex-col overflow-hidden rounded-card border border-line bg-card p-5 md:min-h-64 md:p-6',
                                'sm:justify-end' => $wide,
                            ])>
                                <p @class([
                                    'relative z-10 text-sm leading-relaxed font-semibold text-ink md:text-[0.9375rem]',
                                    'max-w-xs sm:max-w-[55%] md:max-w-sm' => $wide,
                                    'max-w-xs' => ! $wide,
                                ])>
                                    <span class="marker">{{ $card['body'] }}</span>
                                </p>

                                @if ($card['image'])
                                    <img src="{{ $card['image'] }}"
                                         @if ($card['srcset']) srcset="{{ $card['srcset'] }}" sizes="16rem" @endif
                                         @if ($card['size']) width="{{ $card['size'][0] }}" height="{{ $card['size'][1] }}" @endif
                                         loading="lazy"
                                         decoding="async"
                                         alt="Maskot {{ setting('display_name') }} dalam pose {{ $card['title'] }}"
                                         data-tilt-depth
                                         @class([
                                             'pointer-events-none mt-auto -mr-1 -mb-5 h-48 w-auto self-end object-contain object-right-bottom pt-4 md:-mb-6 md:h-56',
                                             // Mulai sm (kartu lebar benar-benar lebar): maskot absolute di kanan, setinggi panel.
                                             'sm:absolute sm:right-6 sm:bottom-0 sm:m-0 sm:h-[92%] sm:max-h-[17.5rem] sm:max-w-[45%] sm:pt-0 md:m-0 md:h-[92%]' => $wide,
                                         ])>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
