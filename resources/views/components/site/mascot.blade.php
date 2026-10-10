{{-- Maskot pojok (PRD F9): bubble berganti per section yang terlihat. Selalu tampil (tanpa tombol tutup). Butuh JS. --}}
@props(['messages' => []])

@if (count($messages) > 0)
    <aside aria-label="Maskot {{ setting('display_name') }}"
           class="fixed right-4 bottom-4 z-40 flex items-end gap-2 md:right-8 md:bottom-8"
           x-data="mascot(@js($messages))"
           x-cloak>
        <div class="relative mb-6 max-w-44 rounded-card border border-line bg-card px-4 py-3 md:max-w-64">
            <p class="text-sm text-ink" x-text="message"></p>
        </div>
        <img src="{{ asset('images/mascot-head-160.webp') }}" width="160" height="155" loading="lazy" decoding="async"
             alt="Kepala maskot {{ setting('display_name') }} bertopi #{{ setting('racing_number', 12) }}"
             class="size-14 shrink-0 rounded-pill border border-line bg-card object-contain p-1 md:size-20">
    </aside>
@endif
