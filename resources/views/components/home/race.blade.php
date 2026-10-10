{{-- Race Progress (PRD F5): lintasan horizontal di desktop, vertikal di HP, checkpoint bulat merah. --}}
@props(['timeline' => []])

<section id="race" aria-labelledby="race-judul" class="scroll-mt-24">
    <div class="mx-auto max-w-site px-4 py-24 md:px-8 md:py-36">
        <div class="flex items-end justify-between gap-8">
            <x-home.section-heading id="race-judul" :title="setting('race_title', 'Race Progress')" :intro="setting('race_intro')" />
            <img src="{{ asset('images/race.webp') }}" width="263" height="311" loading="lazy" decoding="async"
                 alt="Maskot {{ setting('display_name') }} berdiri membelakangi kamera, menghadap lintasan balap"
                 class="hidden w-36 shrink-0 md:block lg:w-44">
        </div>

        @if (count($timeline) === 0)
            <p class="mt-10 rounded-card border border-line bg-card p-8 text-muted">
                Belum ada lap yang tercatat.
            </p>
        @else
            @php
                // Lebih dari 4 lap: lebar kartu tetap supaya kartu berikutnya "mengintip" di tepi kanan
                // dan pengunjung tahu lintasan bisa digeser. Scroll hanya di dalam lintasan, bukan halaman.
                $scrollable = count($timeline) > 4;
            @endphp

            @if ($scrollable)
                <p class="label-mono mt-10 hidden items-center gap-2 text-muted md:flex" aria-hidden="true">
                    Geser untuk lap berikutnya <x-site.icon name="arrow-right" class="size-4" />
                </p>
            @endif

            <ol x-data="raceCar" @class(['relative md:flex md:snap-x md:overflow-x-auto md:pb-4', 'mt-12' => ! $scrollable, 'mt-12 md:mt-4' => $scrollable])
                @if ($scrollable) tabindex="0" aria-label="Timeline, bisa digeser ke samping" @endif>
                {{-- Mobil kecil di lintasan; posisinya mengikuti scroll (Alpine raceCar). Diputar 90° di HP. --}}
                <span x-ref="car" class="pointer-events-none absolute top-[5px] -left-[8.5px] z-10 block h-3.5 w-8 max-md:rotate-90 md:top-0 md:left-0" aria-hidden="true">
                    <svg viewBox="0 0 32 14" class="size-full">
                        <rect x="0" y="2" width="3" height="10" rx="1" class="fill-ink" />
                        <rect x="5" y="0" width="6" height="3" rx="1" class="fill-ink" />
                        <rect x="5" y="11" width="6" height="3" rx="1" class="fill-ink" />
                        <rect x="19" y="0" width="6" height="3" rx="1" class="fill-ink" />
                        <rect x="19" y="11" width="6" height="3" rx="1" class="fill-ink" />
                        <rect x="3" y="3.5" width="24" height="7" rx="3.5" class="fill-ink" />
                        <rect x="8" y="6" width="13" height="2" rx="1" class="fill-red" />
                        <rect x="28" y="1" width="3" height="12" rx="1" class="fill-ink" />
                    </svg>
                </span>

                @foreach ($timeline as $lap)
                    <li data-reveal style="--reveal-delay: {{ min($loop->iteration, 6) * 60 }}ms" @class([
                        'relative pb-8 pl-10 last:pb-0 md:snap-start md:pt-12 md:pr-4 md:pb-0 md:pl-0',
                        'md:min-w-64 md:flex-1' => ! $scrollable,
                        'md:w-[17rem] md:flex-none' => $scrollable,
                    ])>
                        {{-- Garis lintasan: vertikal di HP, horizontal di desktop --}}
                        <span class="absolute top-0 bottom-0 left-[7px] w-px bg-line md:top-[7px] md:right-0 md:bottom-auto md:left-0 md:h-px md:w-auto" aria-hidden="true"></span>
                        {{-- Checkpoint --}}
                        <span class="absolute top-1 left-0 size-4 rounded-pill bg-red ring-4 ring-bg md:top-0" aria-hidden="true"></span>

                        <article class="rounded-card border border-line bg-card p-5 md:p-6 transition duration-300 ease-out-race hover:-translate-y-1 hover:border-muted">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="label-mono text-red-dark">LAP {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                                @if ($lap['type'])
                                    <span class="label-mono rounded-chip border border-line bg-bg px-2.5 py-1 text-muted">{{ $lap['type'] }}</span>
                                @endif
                            </div>

                            <h3 class="mt-4 text-xl md:text-2xl">{{ $lap['organization'] }}</h3>
                            <p class="mt-1 text-ink">{{ $lap['role'] }}</p>

                            <p class="mt-3 font-mono text-xs text-muted">
                                @if ($lap['period'])
                                    {{ $lap['period'] }}
                                    @if ($lap['duration'])
                                        <span aria-hidden="true">·</span> {{ $lap['duration'] }}
                                    @endif
                                @else
                                    [ISI NANTI]
                                @endif
                            </p>

                            @if ($lap['achievement'])
                                <p class="mt-4 border-t border-line pt-4 text-sm text-muted">{{ $lap['achievement'] }}</p>
                            @endif
                        </article>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</section>
