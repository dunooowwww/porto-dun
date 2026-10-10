{{-- Mobil kecil di lintasan (design.md §5). Digerakkan Alpine raceCar lewat properti CSS translate. --}}
<span x-ref="car" {{ $attributes->merge(['class' => 'pointer-events-none absolute z-10 block h-3.5 w-8']) }} aria-hidden="true">
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
