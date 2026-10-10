{{-- Kerangka sementara untuk section yang dibangun di M5/M6 (Race, Garage, Box Box).
     Judul & intro sudah dari database, supaya anchor navbar & scrollspy bisa dites. --}}
@props(['id', 'title', 'intro' => null])
<section id="{{ $id }}" aria-labelledby="{{ $id }}-judul" class="scroll-mt-24">
    <div class="mx-auto min-h-[60vh] max-w-site border-t border-line px-4 py-24 md:px-8 md:py-36">
        <x-home.section-heading :id="$id.'-judul'" :title="$title" :intro="$intro" />
    </div>
</section>
