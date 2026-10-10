{{-- Maskot pojok (PRD F9): bubble berganti per section yang terlihat, bisa disembunyikan. Butuh JS. --}}
@props(['messages' => []])

@if (count($messages) > 0)
    <aside aria-label="Maskot {{ setting('display_name') }}"
           class="fixed right-4 bottom-4 z-40 flex items-end gap-2 md:right-8 md:bottom-8"
           x-data="mascot(@js($messages))"
           x-show="!hidden"
           x-cloak>
        <div class="relative mb-6 max-w-44 rounded-card border border-line bg-card px-4 py-3 md:max-w-64">
            <p class="text-sm text-ink" x-text="message"></p>
            <button type="button"
                    class="absolute -top-3 -left-3 flex size-8 items-center justify-center rounded-pill border border-line bg-card text-muted hover:border-ink hover:text-ink"
                    @click="hide()"
                    aria-label="Sembunyikan maskot">
                <x-site.icon name="x" class="size-3.5" />
            </button>
        </div>
        <img src="{{ asset('images/mascot-head.webp') }}" width="287" height="278" loading="lazy" decoding="async"
             alt="Kepala maskot {{ setting('display_name') }} bertopi #{{ setting('racing_number', 12) }}"
             class="size-14 shrink-0 rounded-pill border border-line bg-card object-contain p-1 md:size-20">
    </aside>
@endif
