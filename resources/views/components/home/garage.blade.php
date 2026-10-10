{{-- Garage (PRD F6): daftar project ala timing screen — posisi, nama, stack, tahun, "View Lap →". --}}
@props(['projects' => []])

@php
    // Kolom desktop: POS | thumbnail | project | stack | tahun | aksi
    $columns = 'md:grid md:grid-cols-[3.5rem_5.5rem_minmax(0,1fr)_13rem_3.5rem_7rem] md:items-center md:gap-6';
@endphp

<section id="garage" aria-labelledby="garage-judul" class="scroll-mt-24">
    <div class="mx-auto max-w-site px-4 py-24 md:px-8 md:py-36">
        <x-home.section-heading id="garage-judul" :title="setting('garage_title', 'Garage')" :intro="setting('garage_intro')" />

        @if (count($projects) === 0)
            <p class="mt-10 rounded-card border border-line bg-card p-8 text-muted">
                Belum ada project yang terbit.
            </p>
        @else
            <div class="mt-12 overflow-hidden rounded-card border border-line bg-card">
                {{-- Header kolom timing screen (dekoratif, hanya desktop) --}}
                <div class="label-mono hidden border-b border-line px-6 py-4 text-muted {{ $columns }}" aria-hidden="true">
                    <span>Pos</span>
                    <span></span>
                    <span>Project</span>
                    <span>Stack</span>
                    <span>Tahun</span>
                    <span></span>
                </div>

                <ol>
                    @foreach ($projects as $project)
                        <li data-reveal style="--reveal-delay: {{ min($loop->iteration, 6) * 60 }}ms" class="group relative border-b border-line px-5 py-6 transition-colors last:border-b-0 hover:bg-bg md:px-6 md:py-5 {{ $columns }}">
                            <div class="flex items-center justify-between md:block">
                                <span class="font-mono text-2xl font-bold text-ink md:text-xl">P{{ $project['position'] }}</span>
                                <span class="label-mono text-muted md:hidden">{{ $project['year'] ?? '—' }}</span>
                            </div>

                            <div class="hidden md:block">
                                @if ($project['thumbnail'])
                                    <img src="{{ $project['thumbnail'] }}"
                                         @if ($project['srcset']) srcset="{{ $project['srcset'] }}" sizes="88px" @endif
                                         @if ($project['size']) width="{{ $project['size'][0] }}" height="{{ $project['size'][1] }}" @endif
                                         loading="lazy" decoding="async"
                                         alt="Thumbnail project {{ $project['title'] }}"
                                         class="aspect-[4/3] w-full rounded-chip border border-line object-cover">
                                @else
                                    <div class="flex aspect-[4/3] w-full items-center justify-center rounded-chip border border-line bg-bg text-muted">
                                        <x-site.icon name="photo" class="size-5" />
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3 min-w-0 md:mt-0">
                                <h3 class="text-xl md:text-2xl">{{ $project['title'] }}</h3>
                                @if ($project['excerpt'])
                                    <p class="mt-1 text-sm text-muted md:line-clamp-2">{{ $project['excerpt'] }}</p>
                                @endif
                            </div>

                            <ul class="mt-4 flex flex-wrap gap-1.5 md:mt-0" aria-label="Tech stack">
                                @forelse (array_slice($project['tech'], 0, 4) as $tech)
                                    <li class="label-mono rounded-chip border border-line px-2 py-1 text-[11px] text-muted">{{ $tech }}</li>
                                @empty
                                    <li class="font-mono text-xs text-muted">[ISI NANTI]</li>
                                @endforelse
                                @if (count($project['tech']) > 4)
                                    <li class="label-mono px-1 py-1 text-[11px] text-muted">+{{ count($project['tech']) - 4 }}</li>
                                @endif
                            </ul>

                            <span class="label-mono hidden text-muted md:block">{{ $project['year'] ?? '—' }}</span>

                            {{-- Satu link per baris; after:inset-0 membuat seluruh baris bisa diklik --}}
                            <a href="{{ route('projects.show', $project['slug']) }}"
                               class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-ink after:absolute after:inset-0 hover:text-red-dark md:mt-0 md:justify-self-end"
                               aria-label="View Lap: {{ $project['title'] }}">
                                <span class="link-grow">View Lap</span>
                                <x-site.icon name="arrow-right" class="size-4" />
                            </a>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif
    </div>
</section>
