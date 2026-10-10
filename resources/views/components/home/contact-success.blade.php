{{-- Pesan sukses form kontak dengan bendera checkered (animasi menyusul di M7). --}}
@props(['message' => null])
<div role="status" class="flex flex-col items-start">
    <div class="flex items-end gap-1" aria-hidden="true">
        <span class="h-14 w-1 rounded-pill bg-ink"></span>
        <span class="checkered mb-6 h-8 w-12 border border-ink [--checker:8px]"></span>
    </div>
    <p class="label-mono mt-6 text-red-dark">Box, box · diterima</p>
    <p class="mt-3 font-display text-2xl font-extrabold tracking-display text-ink"
       @if (! $message) x-text="successMessage" @endif>{{ $message ?? \App\Http\Controllers\ContactMessageController::SUCCESS_MESSAGE }}</p>
    <div class="mt-8">{{ $slot }}</div>
</div>
