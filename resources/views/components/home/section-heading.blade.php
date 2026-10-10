{{-- Judul section rata kiri: H2 besar + intro. --}}
@props(['id', 'title', 'intro' => null])
<div class="max-w-2xl" data-reveal>
    <h2 id="{{ $id }}" class="text-[clamp(2.5rem,7vw,4.5rem)]">{{ $title }}</h2>
    @if ($intro)
        <p class="mt-4 text-lg text-muted">{{ $intro }}</p>
    @endif
</div>
