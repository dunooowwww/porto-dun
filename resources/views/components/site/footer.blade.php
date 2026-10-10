{{-- Footer (PRD F10): garis checkered tipis, nama, teks footer, sosmed, menu, © tahun otomatis. --}}
@php
    $name = setting('full_name');
    $name = filled($name) && $name !== '[ISI NANTI]' ? $name : setting('display_name');
    $social = collect([
        'Email' => setting('contact_email') ? 'mailto:'.setting('contact_email') : null,
        'WhatsApp' => setting('contact_whatsapp') ? 'https://wa.me/'.setting('contact_whatsapp') : null,
        'LinkedIn' => setting('contact_linkedin'),
        'GitHub' => setting('contact_github'),
        'Instagram' => setting('contact_instagram'),
    ])->filter();
@endphp

<footer class="mt-auto">
    <div class="checkered h-1.5 [--checker:3px]" aria-hidden="true"></div>
    <div class="border-t border-line bg-card">
        <div class="mx-auto grid max-w-site gap-10 px-4 py-14 md:grid-cols-[1.4fr_1fr_1fr] md:px-8">
            <div>
                <p class="font-display text-2xl font-extrabold tracking-display text-ink">
                    {{ setting('display_name') }} <span class="label-mono align-middle text-red-dark">#{{ setting('racing_number', 12) }}</span>
                </p>
                @if (setting('footer_text'))
                    <p class="mt-3 max-w-sm text-muted">{{ setting('footer_text') }}</p>
                @endif
            </div>

            <nav aria-label="Menu footer">
                <p class="label-mono text-muted">Menu</p>
                <ul class="mt-4 space-y-2">
                    @foreach (\App\Support\Sections::NAV as $id => $label)
                        <li><a href="{{ url('/') }}#{{ $id }}" class="link-grow text-ink">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </nav>

            @if ($social->isNotEmpty())
                <div>
                    <p class="label-mono text-muted">Sosial</p>
                    <ul class="mt-4 space-y-2">
                        @foreach ($social as $label => $href)
                            <li>
                                <a href="{{ $href }}" class="link-grow text-ink"
                                   @if (! str_starts_with($href, 'mailto:')) target="_blank" rel="noopener noreferrer" @endif>{{ $label }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="mx-auto max-w-site border-t border-line px-4 py-6 md:px-8">
            <p class="font-mono text-xs text-muted">© {{ now()->year }} {{ $name }}</p>
        </div>
    </div>
</footer>
