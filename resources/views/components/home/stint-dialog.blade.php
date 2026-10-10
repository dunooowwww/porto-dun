{{-- Modal detail satu stint di Race Progress. Dibuka lewat raceTabs.openStint(id).
     Klik di luar kotak (area backdrop = elemen <dialog> itu sendiri) juga menutup. --}}
@props(['stint', 'category' => null])

<dialog id="{{ $stint['dialog'] }}" aria-labelledby="{{ $stint['dialog'] }}-judul"
        class="m-auto w-[calc(100%-2rem)] max-w-xl rounded-card border border-line bg-card p-0 text-ink backdrop:bg-ink/40"
        @click.self="$el.close()">
    <div class="p-6 md:p-8">
        <div class="flex items-start justify-between gap-4">
            <p class="label-mono text-red-dark">
                Stint {{ str_pad((string) $stint['number'], 2, '0', STR_PAD_LEFT) }}
                @if ($category)
                    <span class="text-muted">· {{ $category }}</span>
                @endif
            </p>
            <form method="dialog">
                <button type="submit" class="-mt-2 -mr-2 rounded-pill p-2 text-muted transition-colors hover:text-ink" aria-label="Tutup detail">
                    <x-site.icon name="x" class="size-5" />
                </button>
            </form>
        </div>

        <div class="mt-4 flex items-start gap-4">
            @if ($stint['logo'])
                <img src="{{ $stint['logo'] }}" alt="" width="48" height="48" loading="lazy"
                     class="size-12 shrink-0 rounded-chip border border-line object-contain">
            @endif
            <div class="min-w-0">
                <h3 id="{{ $stint['dialog'] }}-judul" class="text-2xl md:text-3xl">{{ $stint['organization'] }}</h3>
                <p class="mt-2 text-lg text-ink">{{ $stint['role'] }}</p>
            </div>
        </div>

        <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-line pt-6">
            <div>
                <dt class="label-mono text-muted">Periode</dt>
                <dd class="mt-1 font-mono text-sm text-ink">{{ $stint['period'] }}</dd>
            </div>
            <div>
                <dt class="label-mono text-muted">Durasi</dt>
                <dd class="mt-1 font-mono text-sm text-ink">
                    {{ $stint['duration'] ?: '—' }}
                    @if ($stint['current'])
                        <span class="ml-1 inline-flex items-center gap-1.5 text-red-dark"><span class="size-2 rounded-pill bg-red" aria-hidden="true"></span>berjalan</span>
                    @endif
                </dd>
            </div>
            @if ($stint['type'])
                <div>
                    <dt class="label-mono text-muted">Jenis</dt>
                    <dd class="mt-1 text-sm text-ink">{{ $stint['type'] }}</dd>
                </div>
            @endif
        </dl>

        @if ($stint['achievement'])
            <div class="mt-6 border-t border-line pt-6">
                <p class="label-mono text-muted">Pencapaian</p>
                <p class="mt-2 text-ink">{!! nl2br(e($stint['achievement'])) !!}</p>
            </div>
        @endif
    </div>
</dialog>
