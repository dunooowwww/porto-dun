{{-- Navbar (PRD F2 / design.md §4–5). Rencana: pill tengah (desktop) → tombol bulat saat scroll; HP selalu tombol bulat + menu penuh. --}}
@php
    $sections = \App\Support\Sections::NAV;
    $home = url('/');
@endphp

<header x-data="navbar(@js(array_keys($sections)))">
    {{-- Desktop: pill melayang di tengah atas --}}
    <nav aria-label="Navigasi utama"
         class="fixed top-4 left-1/2 z-50 hidden -translate-x-1/2 md:block"
         x-show="!scrolled">
        <ul class="flex items-center gap-1 rounded-pill border border-line bg-card p-1.5">
            <li>
                <a href="{{ $home }}#home" class="label-mono flex h-10 items-center rounded-pill px-4 text-red-dark">
                    #{{ setting('racing_number', 12) }}
                </a>
            </li>
            @foreach ($sections as $id => $label)
                <li>
                    <a href="{{ $home }}#{{ $id }}"
                       class="flex h-10 items-center gap-2 rounded-pill px-4 text-sm font-medium text-muted transition-colors hover:text-ink"
                       :class="isActive('{{ $id }}') && 'text-red-dark! bg-bg'"
                       :aria-current="isActive('{{ $id }}') ? 'location' : null">
                        <span class="size-1.5 rounded-pill bg-red" x-show="isActive('{{ $id }}')" x-cloak aria-hidden="true"></span>
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    {{-- Tombol bulat ☰: selalu di HP, di desktop muncul setelah scroll --}}
    <button type="button"
            class="fixed top-4 right-4 z-50 flex size-12 items-center justify-center rounded-pill border border-line bg-card text-ink transition-colors hover:border-ink md:hidden md:right-8"
            :class="scrolled && 'md:flex!'"
            @click="open = true"
            :aria-expanded="open.toString()"
            aria-controls="menu-navigasi"
            aria-label="Buka menu navigasi">
        <x-site.icon name="menu-2" class="size-5" />
    </button>

    {{-- Menu layar penuh --}}
    <div id="menu-navigasi"
         class="fixed inset-0 z-[55] flex flex-col bg-bg px-4 py-4 md:px-8"
         role="dialog"
         aria-modal="true"
         aria-label="Menu navigasi"
         x-show="open"
         x-cloak
         x-trap.inert.noscroll="open"
         @keydown.escape.window="close()">
        <div class="mx-auto flex w-full max-w-site items-center justify-between">
            <span class="label-mono text-red-dark">#{{ setting('racing_number', 12) }} · Menu</span>
            <button type="button"
                    class="flex size-12 items-center justify-center rounded-pill border border-line bg-card text-ink hover:border-ink"
                    @click="close()"
                    aria-label="Tutup menu navigasi">
                <x-site.icon name="x" class="size-5" />
            </button>
        </div>

        <nav aria-label="Navigasi utama (menu)" class="mx-auto mt-12 w-full max-w-site">
            <ul class="flex flex-col">
                @foreach ($sections as $id => $label)
                    <li class="border-b border-line">
                        <a href="{{ $home }}#{{ $id }}"
                           @click="close()"
                           class="flex items-center justify-between py-5 font-display text-4xl font-extrabold tracking-display text-ink sm:text-5xl"
                           :class="isActive('{{ $id }}') && 'text-red-dark!'"
                           :aria-current="isActive('{{ $id }}') ? 'location' : null">
                            {{ $label }}
                            <span class="label-mono text-muted">0{{ $loop->iteration }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</header>
