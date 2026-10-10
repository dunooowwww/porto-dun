{{-- Pesan error field: dari server (tanpa JS) atau dari respons fetch (Alpine). --}}
@props(['name'])
<div id="kontak-{{ $name }}-error">
    @error($name)
        <p class="mt-2 text-sm text-red-dark" x-show="!submitted">{{ $message }}</p>
    @enderror
    <p class="mt-2 text-sm text-red-dark" x-show="error('{{ $name }}')" x-text="error('{{ $name }}')" x-cloak></p>
</div>
