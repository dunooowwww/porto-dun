{{-- Loading screen "Lights Out" (PRD F1) — overlay. Tersembunyi kecuali <html> punya kelas .lights-on. --}}
<div class="lights-out fixed inset-0 z-[70] cursor-pointer flex-col items-center justify-center gap-8 bg-bg" aria-hidden="true">
    <div class="flex gap-2 rounded-pill bg-ink p-3 sm:gap-3 sm:p-4">
        @for ($i = 0; $i < 5; $i++)
            <span class="lamp size-8 rounded-pill bg-muted sm:size-12" style="--i: {{ $i }}"></span>
        @endfor
    </div>
    <div class="text-center">
        <p class="label-mono text-ink">Lights out · #{{ setting('racing_number', 12) }}</p>
        <p class="label-mono mt-2 text-muted">Klik atau tekan Esc untuk lewati</p>
    </div>
</div>
