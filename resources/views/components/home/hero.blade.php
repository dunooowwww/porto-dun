{{-- Hero (PRD F3). Semua teks dari Pengaturan Situs → tab Profil. --}}
@php
    $name = setting('display_name');
    $greeting = setting('hero_greeting');
    $number = setting('racing_number', 12);
    $eyebrow = collect([setting('role'), setting('city')])->filter()->implode(' · ');
    $techs = collect(setting('hero_tech_icons', []))->filter(fn ($tech) => filled($tech['name'] ?? null));
    $hasCv = filled(setting('cv_path'));

    // Nama hanya tertulis sekali: bagian sapaan yang diberi garis merah + #12 (lihat App\Support\HeroName).
    $parts = \App\Support\HeroName::split($greeting, $name);
@endphp

<section id="home" aria-labelledby="hero-judul" class="scroll-mt-0">
    {{-- Teks melebar, gambar berlebar tetap di kanan supaya jaraknya dekat (tidak ada kolom kosong di tengah). --}}
    <div class="mx-auto grid max-w-site items-center gap-10 px-4 pt-28 pb-16 md:grid-cols-[minmax(0,1fr)_auto] md:gap-12 md:px-8 md:pt-36 md:pb-24 lg:gap-16">
        <div class="min-w-0">
            @if ($eyebrow)
                <p class="label-mono text-muted">{{ $eyebrow }}</p>
            @endif

            <h1 id="hero-judul" class="mt-5 text-[clamp(2.75rem,5.2vw,4rem)] font-black">
                {{ $parts['before'] }}<x-home.hero-name :name="$parts['name']" :number="$number" />{{ $parts['after'] }}
            </h1>

            @if (setting('hero_tagline'))
                <p class="mt-4 max-w-3xl font-display text-[clamp(1.75rem,3.6vw,3rem)] leading-[1.1] font-bold tracking-display text-muted">
                    {{ setting('hero_tagline') }}
                </p>
            @endif

            @if (setting('hero_description'))
                <p class="mt-6 max-w-2xl text-lg text-muted">
                    {{ setting('hero_description') }}
                </p>
            @endif

            @if ($techs->isNotEmpty())
                <ul class="mt-8 flex flex-wrap gap-2" aria-label="Tech yang aku pakai">
                    @foreach ($techs as $tech)
                        <li class="label-mono flex items-center gap-2 rounded-chip border border-line bg-card px-3 py-2 text-ink">
                            @if (\App\Support\Icon::allowedForContent($tech['icon'] ?? null))
                                <x-site.icon :name="$tech['icon']" class="size-4" />
                            @endif
                            {{ $tech['name'] }}
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-10 flex flex-wrap items-center gap-3">
                <a href="#garage" class="btn-primary">
                    <span class="btn-dot" aria-hidden="true"></span>
                    Lihat Garage
                    <x-site.icon name="arrow-right" class="size-4" />
                </a>
                @if ($hasCv)
                    <a href="{{ route('cv') }}" class="btn-secondary" target="_blank" rel="noopener">
                        <x-site.icon name="download" class="size-4" />
                        Download CV
                    </a>
                @endif
            </div>

            @if (setting('hero_scroll_text'))
                <p class="label-mono mt-14 text-muted">{{ setting('hero_scroll_text') }}</p>
            @endif
        </div>

        <img src="{{ asset('images/hero.webp') }}"
             width="214" height="273"
             fetchpriority="high"
             alt="Maskot {{ $name }}: karakter anime berhoodie putih dan topi hitam #{{ $number }}, duduk bersila memegang laptop"
             class="w-52 justify-self-start sm:w-60 md:w-56 md:justify-self-end lg:w-64">
    </div>
</section>
