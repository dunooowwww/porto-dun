{{-- Race Strategy (PRD F5, revisi): pengalaman dipisah jadi tab "Main race · Profesional" & "Sprint · Organisasi".
     Desktop: chart ala grafik strategi F1 (sumbu tahun + bar per stint). HP: daftar kartu vertikal. --}}
@props(['tabs' => []])

@php
    $keys = array_column($tabs, 'key');
    $stintCount = fn (int $n) => $n.' '.($n === 1 ? 'stint' : 'stints');
@endphp

<section id="race" aria-labelledby="race-judul" class="scroll-mt-24">
    <div class="mx-auto max-w-site px-4 py-16 md:px-8 md:py-24">
        <div class="flex items-end justify-between gap-8">
            <x-home.section-heading id="race-judul" :title="setting('race_title', 'Race Progress')" :intro="setting('race_intro')" />
            <img src="{{ asset('images/race.webp') }}" width="263" height="311" loading="lazy" decoding="async"
                 alt="Maskot {{ setting('display_name') }} berdiri membelakangi kamera, menghadap lintasan balap"
                 class="hidden w-36 shrink-0 md:block lg:w-44">
        </div>

        @if (count($tabs) === 0)
            <p class="mt-10 rounded-card border border-line bg-card p-8 text-muted">
                Belum ada stint yang tercatat.
            </p>
        @else
            <div class="mt-12 rounded-card border border-line bg-card p-5 md:p-8"
                 x-data="raceTabs(@js($keys), @js($keys[0]))"
                 data-reveal>
                {{-- Tab kategori (tab tanpa data tidak dirender) --}}
                <div role="tablist" aria-label="Kategori pengalaman"
                     class="grid {{ count($tabs) > 1 ? 'grid-cols-2' : 'grid-cols-1' }} gap-1 rounded-pill border border-line bg-bg p-1 sm:flex sm:w-fit"
                     @keydown="onKeydown($event)">
                    @foreach ($tabs as $i => $tab)
                        <button type="button" role="tab"
                                id="race-tab-{{ $tab['key'] }}"
                                aria-controls="race-panel-{{ $tab['key'] }}"
                                aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                                tabindex="{{ $i === 0 ? '0' : '-1' }}"
                                :aria-selected="isActive('{{ $tab['key'] }}').toString()"
                                :tabindex="isActive('{{ $tab['key'] }}') ? 0 : -1"
                                @click="select('{{ $tab['key'] }}')"
                                class="relative flex flex-col items-center gap-0.5 rounded-pill px-4 pt-2 pb-3 text-muted transition-colors hover:text-ink sm:flex-row sm:gap-1.5 sm:py-2.5"
                                :class="isActive('{{ $tab['key'] }}') && 'bg-card text-ink!'">
                            {{-- HP: dua baris (MAIN RACE / Profesional); layar lebar: satu baris dengan titik pemisah --}}
                            <span class="label-mono">{{ $tab['race'] }}</span>
                            <span class="hidden text-sm font-medium sm:inline" aria-hidden="true">·</span>
                            <span class="text-sm font-medium">{{ $tab['label'] }}</span>
                            {{-- Garis merah penanda tab aktif --}}
                            <span class="absolute inset-x-4 bottom-1 h-0.5 rounded-pill bg-red"
                                  x-show="isActive('{{ $tab['key'] }}')"
                                  @if ($i > 0) x-cloak @endif
                                  aria-hidden="true"></span>
                        </button>
                    @endforeach
                </div>

                @foreach ($tabs as $tab)
                    @php
                        $years = $tab['years'];
                        $yearSpan = count($years);
                        $yearLabel = $yearSpan > 1 ? $years[0].'–'.end($years) : (string) $years[0];
                    @endphp

                    {{-- Tanpa JS semua panel tampil bertumpuk (x-show baru bekerja setelah Alpine jalan). --}}
                    <div role="tabpanel" id="race-panel-{{ $tab['key'] }}" aria-labelledby="race-tab-{{ $tab['key'] }}"
                         tabindex="0" class="mt-8 rounded-chip"
                         x-show="isActive('{{ $tab['key'] }}')">
                        <p class="label-mono text-muted">
                            <span class="text-ink">{{ $stintCount(count($tab['stints'])) }}</span> · {{ $tab['race'] }} · {{ $yearLabel }}
                        </p>

                        {{-- Desktop: chart strategi --}}
                        <div class="mt-6 hidden md:block">
                            {{-- Kolom sama dengan baris stint: info | lintasan | "Detail" --}}
                            <div class="grid grid-cols-[16rem_minmax(0,1fr)_auto] gap-x-8">
                                <span></span>
                                {{-- Sumbu tahun + mobil kecil yang melaju mengikuti scroll --}}
                                <div class="relative h-9" x-data="raceCar" aria-hidden="true">
                                    <span class="absolute inset-x-0 bottom-2 h-px bg-line"></span>
                                    @foreach ($years as $y => $year)
                                        <span class="label-mono absolute top-0 text-muted" style="left: {{ $y / $yearSpan * 100 }}%">{{ $year }}</span>
                                        <span class="absolute bottom-1 h-2 w-px bg-line" style="left: {{ $y / $yearSpan * 100 }}%"></span>
                                    @endforeach
                                    <x-home.race-car class="bottom-[1.5px] left-0" />
                                </div>
                                <span class="label-mono invisible flex items-center gap-1.5" aria-hidden="true">Detail<span class="size-3.5"></span></span>
                            </div>

                            <ol>
                                @foreach ($tab['stints'] as $stint)
                                    <li class="group relative grid grid-cols-[16rem_minmax(0,1fr)_auto] items-center gap-x-8 border-t border-line py-4 first:mt-2">
                                        <div class="min-w-0">
                                            <p class="label-mono text-red-dark">
                                                Stint {{ str_pad((string) $stint['number'], 2, '0', STR_PAD_LEFT) }}
                                                @if ($stint['type'])
                                                    <span class="text-muted">· {{ $stint['type'] }}</span>
                                                @endif
                                            </p>
                                            <h3 class="mt-2 line-clamp-2 text-lg">
                                                {{-- Tombol melebar ke seluruh baris (after:inset-0), jadi satu baris = satu area klik. --}}
                                                <button type="button" class="text-left after:absolute after:inset-0 after:rounded-chip"
                                                        aria-haspopup="dialog" aria-controls="{{ $stint['dialog'] }}"
                                                        @click="openStint('{{ $stint['dialog'] }}')">{{ $stint['organization'] }}</button>
                                            </h3>
                                            <p class="line-clamp-2 text-sm text-ink">{{ $stint['role'] }}</p>
                                            <p class="mt-1 font-mono text-xs text-muted">
                                                {{ $stint['period'] }} @if ($stint['duration'])<span aria-hidden="true">·</span> {{ $stint['duration'] }}@endif
                                            </p>
                                            @if ($stint['achievement'])
                                                <p class="mt-2 line-clamp-2 text-sm text-muted">{{ $stint['achievement'] }}</p>
                                            @endif
                                        </div>

                                        <div class="relative h-8" aria-hidden="true">
                                            @foreach ($years as $y => $year)
                                                @if ($y > 0)
                                                    <span class="absolute inset-y-0 w-px bg-line" style="left: {{ $y / $yearSpan * 100 }}%"></span>
                                                @endif
                                            @endforeach
                                            <span class="absolute top-1/2 h-2.5 -translate-y-1/2 rounded-pill bg-ink"
                                                  style="left: {{ $stint['left'] }}%; width: {{ $stint['width'] }}%">
                                                @if ($stint['current'])
                                                    {{-- Masih berjalan --}}
                                                    <span class="absolute top-1/2 -right-1.5 size-3 -translate-y-1/2 rounded-pill bg-red ring-2 ring-card"></span>
                                                @endif
                                            </span>
                                        </div>

                                        <span class="label-mono flex items-center gap-1.5 text-muted transition-colors group-hover:text-ink" aria-hidden="true">
                                            <span class="link-grow">Detail</span>
                                            <x-site.icon name="arrow-up-right" class="size-3.5" />
                                        </span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>

                        {{-- HP: daftar kartu vertikal dengan lintasan --}}
                        <ol x-data="raceCar" class="relative mt-6 md:hidden">
                            <x-home.race-car class="top-[5px] -left-[8.5px] rotate-90" />

                            @foreach ($tab['stints'] as $stint)
                                <li class="relative pb-6 pl-10 last:pb-0">
                                    <span class="absolute top-0 bottom-0 left-[7px] w-px bg-line" aria-hidden="true"></span>
                                    <span class="absolute top-1 left-0 size-4 rounded-pill bg-red ring-4 ring-card" aria-hidden="true"></span>

                                    <article class="group relative rounded-card border border-line bg-bg p-5 transition duration-300 ease-out-race hover:border-muted">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <p class="label-mono text-red-dark">Stint {{ str_pad((string) $stint['number'], 2, '0', STR_PAD_LEFT) }}</p>
                                            @if ($stint['type'])
                                                <span class="label-mono rounded-chip border border-line bg-card px-2.5 py-1 text-muted">{{ $stint['type'] }}</span>
                                            @endif
                                        </div>
                                        <h3 class="mt-4 text-xl">
                                            <button type="button" class="text-left after:absolute after:inset-0 after:rounded-card"
                                                    aria-haspopup="dialog" aria-controls="{{ $stint['dialog'] }}"
                                                    @click="openStint('{{ $stint['dialog'] }}')">{{ $stint['organization'] }}</button>
                                        </h3>
                                        <p class="mt-1 text-ink">{{ $stint['role'] }}</p>
                                        <p class="mt-3 font-mono text-xs text-muted">
                                            {{ $stint['period'] }} @if ($stint['duration'])<span aria-hidden="true">·</span> {{ $stint['duration'] }}@endif
                                        </p>
                                        @if ($stint['achievement'])
                                            <p class="mt-4 line-clamp-3 border-t border-line pt-4 text-sm text-muted">{{ $stint['achievement'] }}</p>
                                        @endif
                                        <p class="label-mono mt-4 flex items-center gap-1.5 text-ink" aria-hidden="true">
                                            <span class="link-grow">Lihat detail</span>
                                            <x-site.icon name="arrow-up-right" class="size-3.5" />
                                        </p>
                                    </article>
                                </li>
                            @endforeach
                        </ol>

                        {{-- Detail tiap stint: <dialog> bawaan browser (Esc menutup, fokus terkunci di dalam modal). --}}
                        @foreach ($tab['stints'] as $stint)
                            <x-home.stint-dialog :stint="$stint" :category="$tab['label']" />
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
