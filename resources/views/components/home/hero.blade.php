{{-- Hero (PRD F3). Semua teks dari Pengaturan Situs → tab Profil. --}}
@php
    $name = setting('display_name');
    $greeting = setting('hero_greeting');
    $number = setting('racing_number', 12);
    $eyebrow = collect([setting('role'), setting('city')])->filter()->implode(' · ');
    $techs = collect(setting('hero_tech_icons', []))->filter(fn ($tech) => filled($tech['name'] ?? null));
    $hasCv = filled(setting('cv_path'));

    // Jika nama ada di dalam sapaan ("Halo, aku Dun"), nama itulah yang diberi garis merah + #12.
    $nameInGreeting = filled($name) && filled($greeting) && str_contains($greeting, $name);
    $before = $nameInGreeting ? \Illuminate\Support\Str::before($greeting, $name) : $greeting;
    $after = $nameInGreeting ? \Illuminate\Support\Str::after($greeting, $name) : null;
@endphp

<section id="home" aria-labelledby="hero-judul" class="scroll-mt-0">
    <div class="mx-auto grid max-w-site items-center gap-10 px-4 pt-28 pb-16 md:grid-cols-[1.3fr_1fr] md:gap-8 md:px-8 md:pt-40 md:pb-24">
        <div>
            @if ($eyebrow)
                <p class="label-mono text-muted">{{ $eyebrow }}</p>
            @endif

            <h1 id="hero-judul" class="mt-5 text-[clamp(3rem,11vw,6rem)] font-black">
                @if ($nameInGreeting)
                    {{ $before }}<x-home.hero-name :name="$name" :number="$number" />{{ $after }}
                @else
                    @if ($greeting)
                        <span class="block">{{ $greeting }}</span>
                    @endif
                    <x-home.hero-name :name="$name" :number="$number" />
                @endif
            </h1>

            @if (setting('hero_tagline'))
                <p class="mt-8 max-w-2xl font-display text-2xl leading-snug font-bold tracking-display text-ink md:text-3xl">
                    {{ setting('hero_tagline') }}
                </p>
            @endif

            @if (setting('hero_description'))
                <p class="mt-4 max-w-xl text-lg text-muted">
                    {{ setting('hero_description') }}
                </p>
            @endif

            @if ($techs->isNotEmpty())
                <ul class="mt-8 flex flex-wrap gap-2" aria-label="Tech yang aku pakai">
                    @foreach ($techs as $tech)
                        <li class="label-mono flex items-center gap-2 rounded-chip border border-line bg-card px-3 py-2 text-ink">
                            @if (filled($tech['icon'] ?? null))
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
             class="w-52 justify-self-start sm:w-60 md:w-72 md:justify-self-end">
    </div>
</section>
