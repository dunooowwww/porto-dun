{{-- Detail project / case study (PRD F7): Masalah → Peran & proses → Hasil → Galeri. --}}
@php
    $garageUrl = url('/').'#garage';
    $labelParts = array_filter([
        $isDraft ? 'Draft' : ($position ? 'P'.$position : null),
        $project->year,
    ]);
    $sections = array_filter([
        'Masalah' => filled($project->problem) ? 'problem' : null,
        'Peran & proses' => filled($project->process) || filled($project->role) ? 'process' : null,
        'Hasil' => filled($project->result) ? 'result' : null,
        'Galeri' => $project->images->isNotEmpty() ? 'gallery' : null,
    ]);
@endphp

<x-layouts.app :title="$project->title"
               :description="$project->excerpt"
               :image="$project->thumbnail_url"
               type="article"
               :noindex="$isDraft">
    <x-slot:header>
        <x-site.navbar />
    </x-slot:header>

    <article class="mx-auto max-w-site px-4 pt-28 pb-24 md:px-8 md:pt-36 md:pb-36">
        <header>
            <a href="{{ $garageUrl }}" class="group label-mono inline-flex items-center gap-2 text-muted hover:text-ink">
                <x-site.icon name="arrow-left" class="size-4" />
                <span class="link-grow">Kembali ke Garage</span>
            </a>

            @if ($isDraft)
                <p class="mt-6 rounded-chip border border-line bg-card px-4 py-3 text-sm text-muted">
                    <span class="label-mono text-red-dark">Draft</span> · Halaman ini hanya terlihat olehmu karena sedang login sebagai admin.
                </p>
            @endif

            @if ($labelParts)
                <p class="label-mono mt-10 text-red-dark">{{ implode(' · ', $labelParts) }}</p>
            @endif
            <h1 class="mt-4 max-w-4xl text-[clamp(2.5rem,8vw,5rem)] font-black">{{ $project->title }}</h1>
            @if ($project->excerpt)
                <p class="mt-6 max-w-2xl text-xl text-muted">{{ $project->excerpt }}</p>
            @endif

            <dl class="mt-10 grid gap-6 border-y border-line py-6 sm:grid-cols-[1fr_auto_1.2fr] sm:gap-10">
                <div>
                    <dt class="label-mono text-muted">Peran</dt>
                    <dd class="mt-2 text-ink">{{ $project->role ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="label-mono text-muted">Tahun</dt>
                    <dd class="mt-2 font-mono text-ink">{{ $project->year ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="label-mono text-muted">Tech stack</dt>
                    <dd class="mt-2">
                        @if (filled($project->tech_stack))
                            <ul class="flex flex-wrap gap-1.5">
                                @foreach ($project->tech_stack as $tech)
                                    <li class="label-mono rounded-chip border border-line bg-card px-2.5 py-1 text-ink">{{ $tech }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </dd>
                </div>
            </dl>

            @if ($project->demo_url || $project->github_url)
                <div class="mt-8 flex flex-wrap gap-3">
                    @if ($project->demo_url)
                        <a href="{{ $project->demo_url }}" class="btn-primary" target="_blank" rel="noopener noreferrer">
                            <span class="btn-dot" aria-hidden="true"></span>
                            Lihat demo
                            <x-site.icon name="external-link" class="size-4" />
                        </a>
                    @endif
                    @if ($project->github_url)
                        <a href="{{ $project->github_url }}" class="btn-secondary" target="_blank" rel="noopener noreferrer">
                            <x-site.icon name="code" class="size-4" />
                            Kode di GitHub
                        </a>
                    @endif
                </div>
            @endif
        </header>

        @if ($project->thumbnail_url)
            @php $thumbSize = upload_image_size($project->thumbnail); @endphp
            <img src="{{ $project->thumbnail_url }}"
                 @if ($thumbSrcset = upload_srcset($project->thumbnail)) srcset="{{ $thumbSrcset }}" sizes="(min-width: 1200px) 1136px, calc(100vw - 32px)" @endif
                 @if ($thumbSize) width="{{ $thumbSize[0] }}" height="{{ $thumbSize[1] }}" @endif
                 fetchpriority="high"
                 alt="Tampilan project {{ $project->title }}"
                 class="mt-12 w-full rounded-card border border-line bg-card object-cover">
        @endif

        @if (count($sections) === 0)
            <p class="mt-12 rounded-card border border-line bg-card p-8 text-muted">
                Case study project ini belum diisi.
            </p>
        @endif

        @foreach ($sections as $heading => $key)
            <section class="mt-12 grid gap-4 border-t border-line pt-10 md:mt-16 md:grid-cols-[13rem_minmax(0,1fr)] md:gap-12"
                     aria-labelledby="bagian-{{ $key }}">
                <h2 id="bagian-{{ $key }}" class="label-mono text-muted">
                    {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }} · {{ $heading }}
                </h2>

                <div class="max-w-3xl">
                    @switch($key)
                        @case('problem')
                            <p class="text-lg text-ink">{!! nl2br(e($project->problem)) !!}</p>
                            @break

                        @case('process')
                            @if (filled($project->role))
                                <p class="text-lg font-semibold text-ink">{!! nl2br(e($project->role)) !!}</p>
                            @endif
                            @if (filled($project->process))
                                <div @class(['prose-case', 'mt-4' => filled($project->role)])>
                                    {!! str($project->process)->sanitizeHtml() !!}
                                </div>
                            @endif
                            @break

                        @case('result')
                            <p class="text-lg text-ink">{!! nl2br(e($project->result)) !!}</p>
                            @break

                        @case('gallery')
                            <div class="grid gap-4 sm:grid-cols-2">
                                @foreach ($project->images as $image)
                                    @php $size = upload_image_size($image->path); @endphp
                                    <figure>
                                        <img src="{{ $image->url }}"
                                             @if ($gallerySrcset = upload_srcset($image->path)) srcset="{{ $gallerySrcset }}" sizes="(min-width: 768px) 440px, calc(100vw - 32px)" @endif
                                             @if ($size) width="{{ $size[0] }}" height="{{ $size[1] }}" @endif
                                             loading="lazy" decoding="async"
                                             alt="{{ $image->caption ?: 'Screenshot '.$loop->iteration.' project '.$project->title }}"
                                             class="w-full rounded-card border border-line bg-card object-cover">
                                        @if ($image->caption)
                                            <figcaption class="mt-2 font-mono text-xs text-muted">{{ $image->caption }}</figcaption>
                                        @endif
                                    </figure>
                                @endforeach
                            </div>
                            @break
                    @endswitch
                </div>
            </section>
        @endforeach

        {{-- Navigasi project sebelumnya / berikutnya (urutan Garage) --}}
        <nav aria-label="Project lain" class="mt-16 border-t border-line pt-10 md:mt-24">
            @if ($previous || $next)
                <div class="grid gap-4 sm:grid-cols-2">
                    @if ($previous)
                        <a href="{{ route('projects.show', $previous['slug']) }}"
                           class="group rounded-card border border-line bg-card p-6 transition duration-300 ease-out-race hover:-translate-y-1 hover:border-muted">
                            <span class="label-mono flex items-center gap-2 text-muted">
                                <x-site.icon name="arrow-left" class="size-4" /> Sebelumnya · P{{ $previous['position'] }}
                            </span>
                            <span class="mt-3 block font-display text-xl font-extrabold tracking-display text-ink">{{ $previous['title'] }}</span>
                        </a>
                    @else
                        <span class="hidden sm:block"></span>
                    @endif
                    @if ($next)
                        <a href="{{ route('projects.show', $next['slug']) }}"
                           class="group rounded-card border border-line bg-card p-6 text-right transition duration-300 ease-out-race hover:-translate-y-1 hover:border-muted">
                            <span class="label-mono flex items-center justify-end gap-2 text-muted">
                                Berikutnya · P{{ $next['position'] }} <x-site.icon name="arrow-right" class="size-4" />
                            </span>
                            <span class="mt-3 block font-display text-xl font-extrabold tracking-display text-ink">{{ $next['title'] }}</span>
                        </a>
                    @endif
                </div>
            @endif

            <div class="mt-8 flex justify-center">
                <a href="{{ $garageUrl }}" class="btn-secondary">
                    <x-site.icon name="layout-list" class="size-4" />
                    Kembali ke Garage
                </a>
            </div>
        </nav>
    </article>
</x-layouts.app>
