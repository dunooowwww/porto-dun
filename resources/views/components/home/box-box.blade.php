{{-- Box, Box (PRD F8): link kontak + form. Form jalan tanpa JS (POST biasa) dan dengan Alpine (fetch, tanpa reload). --}}
@php
    $strip = fn (?string $url) => $url ? rtrim(preg_replace('#^https?://(www\.)?#', '', $url), '/') : null;
    $links = collect([
        ['label' => 'Email', 'icon' => 'mail', 'href' => setting('contact_email') ? 'mailto:'.setting('contact_email') : null, 'text' => setting('contact_email')],
        ['label' => 'WhatsApp', 'icon' => 'message-circle', 'href' => setting('contact_whatsapp') ? 'https://wa.me/'.setting('contact_whatsapp') : null, 'text' => setting('contact_whatsapp') ? '+'.setting('contact_whatsapp') : null],
        ['label' => 'LinkedIn', 'icon' => 'briefcase', 'href' => setting('contact_linkedin'), 'text' => $strip(setting('contact_linkedin'))],
        ['label' => 'GitHub', 'icon' => 'code', 'href' => setting('contact_github'), 'text' => $strip(setting('contact_github'))],
        ['label' => 'Instagram', 'icon' => 'camera', 'href' => setting('contact_instagram'), 'text' => $strip(setting('contact_instagram'))],
    ])->filter(fn ($link) => filled($link['href']));
    $sent = session('contact_sent');
    $field = 'w-full rounded-chip border border-line bg-bg px-4 py-3 text-ink placeholder:text-muted focus:border-ink';
@endphp

<section id="box-box" aria-labelledby="box-box-judul" class="scroll-mt-24">
    <div class="mx-auto grid max-w-site gap-12 px-4 py-24 md:grid-cols-[1fr_1.1fr] md:gap-16 md:px-8 md:py-36">
        <div data-reveal>
            <x-home.section-heading id="box-box-judul" :title="setting('contact_title', 'Box, Box')" :intro="setting('contact_intro')" />

            @if (setting('contact_quote'))
                <p class="mt-6 font-display text-xl font-bold tracking-display text-ink italic">{{ setting('contact_quote') }}</p>
            @endif

            @if ($links->isNotEmpty())
                <ul class="mt-10 border-t border-line">
                    @foreach ($links as $link)
                        <li class="border-b border-line">
                            <a href="{{ $link['href'] }}"
                               @if (! str_starts_with($link['href'], 'mailto:')) target="_blank" rel="noopener noreferrer" @endif
                               class="group flex items-center gap-4 py-4 text-ink">
                                <x-site.icon :name="$link['icon']" class="size-5 shrink-0 text-muted" />
                                <span class="label-mono w-24 shrink-0 text-muted">{{ $link['label'] }}</span>
                                <span class="link-grow min-w-0 truncate font-medium">{{ $link['text'] }}</span>
                                <x-site.icon name="arrow-up-right" class="ml-auto size-4 shrink-0 text-muted group-hover:text-red-dark" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="rounded-card border border-line bg-card p-6 md:p-10" x-data="contactForm" data-reveal style="--reveal-delay: 60ms">
            @if ($sent)
                {{-- Sukses tanpa JS (setelah redirect) --}}
                <x-home.contact-success :message="$sent">
                    {{-- Query membuat browser benar-benar memuat ulang (bukan cuma lompat ke anchor yang sama). --}}
                    <a href="{{ url('/') }}?form=baru#box-box" class="btn-secondary">Kirim pesan lain</a>
                </x-home.contact-success>
            @else
                <div x-show="sent" x-cloak>
                    <x-home.contact-success>
                        <button type="button" class="btn-secondary" @click="reset()">Kirim pesan lain</button>
                    </x-home.contact-success>
                </div>

                <form method="POST" action="{{ route('contact.store') }}" x-show="!sent" @submit.prevent="submit($el)">
                    @csrf
                    <h3 class="text-2xl">Kirim pesan</h3>
                    <p class="mt-2 text-sm text-muted">Semua kolom wajib diisi.</p>

                    @foreach ([
                        ['name', 'Nama', 'text', 'name'],
                        ['email', 'Email', 'email', 'email'],
                    ] as [$name, $label, $type, $autocomplete])
                        <div class="mt-6">
                            <label for="kontak-{{ $name }}" class="label-mono text-muted">{{ $label }}</label>
                            <input id="kontak-{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}"
                                   autocomplete="{{ $autocomplete }}" required maxlength="{{ $name === 'name' ? 100 : 150 }}"
                                   class="{{ $field }} mt-2"
                                   @error($name) aria-invalid="true" @enderror
                                   :aria-invalid="error('{{ $name }}') ? 'true' : null"
                                   aria-describedby="kontak-{{ $name }}-error">
                            <x-home.field-error :name="$name" />
                        </div>
                    @endforeach

                    <div class="mt-6">
                        <label for="kontak-message" class="label-mono text-muted">Pesan</label>
                        <textarea id="kontak-message" name="message" rows="5" required minlength="10" maxlength="2000"
                                  class="{{ $field }} mt-2 resize-y"
                                  @error('message') aria-invalid="true" @enderror
                                  :aria-invalid="error('message') ? 'true' : null"
                                  aria-describedby="kontak-message-error">{{ old('message') }}</textarea>
                        <x-home.field-error name="message" />
                    </div>

                    {{-- Honeypot: disembunyikan dari manusia & pembaca layar, biasanya diisi bot. --}}
                    <div class="absolute -left-[9999px] h-px w-px overflow-hidden" aria-hidden="true">
                        <label for="kontak-website">Website</label>
                        <input id="kontak-website" name="{{ \App\Http\Requests\StoreContactMessageRequest::HONEYPOT }}" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <button type="submit" class="btn-primary mt-8 disabled:opacity-60" :disabled="sending">
                        <span class="btn-dot" aria-hidden="true"></span>
                        <span x-text="sending ? 'Mengirim…' : 'Kirim pesan'">Kirim pesan</span>
                        <x-site.icon name="send" class="size-4" />
                    </button>
                </form>
            @endif
        </div>
    </div>
</section>
