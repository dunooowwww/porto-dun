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

<section id="pit-wall" aria-labelledby="pit-wall-judul" class="scroll-mt-24">
    <div class="mx-auto max-w-site px-4 py-24 md:px-8 md:py-36">
        <x-home.section-heading id="pit-wall-judul" :title="setting('pit_wall_title', 'Pit Wall')" :intro="setting('pit_wall_intro')" />

        @if (count($cards) === 0)
            <p class="mt-10 rounded-card border border-line bg-card p-8 text-muted">
                Kartu Pit Wall belum diisi.
            </p>
        @else
            <div class="mt-12 grid gap-4 md:grid-cols-3">
                @foreach ($cards as $i => $card)
                    @php $wide = ($spans[$i] ?? 1) > 1; @endphp
                    <article @class([
                        'flex flex-col justify-between gap-6 overflow-hidden md:min-h-72 rounded-card border border-line bg-card p-6 md:p-8',
                        $spanClass[$spans[$i] ?? 1],
                        'sm:flex-row sm:items-start' => $wide,
                    ])>
                        <div class="max-w-md">
                            <p class="label-mono text-red-dark">{{ $card['sector'] }}</p>
                            <h3 class="mt-4 text-2xl md:text-3xl">{{ $card['title'] }}</h3>
                            <p class="mt-3 text-muted">{{ $card['body'] }}</p>
                        </div>

                        @if ($card['image'])
                            <img src="{{ $card['image'] }}"
                                 @if ($card['size']) width="{{ $card['size'][0] }}" height="{{ $card['size'][1] }}" @endif
                                 loading="lazy"
                                 decoding="async"
                                 alt="Maskot {{ setting('display_name') }} dalam pose {{ $card['title'] }}"
                                 class="h-40 w-auto self-end object-contain md:h-48">
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
