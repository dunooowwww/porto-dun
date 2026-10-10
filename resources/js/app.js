import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

/**
 * Navbar (PRD F2): pill mengecil jadi tombol bulat saat scroll, scrollspy, menu layar penuh.
 * Halaman tetap bisa dipakai tanpa JavaScript karena semua menu berupa link anchor biasa.
 */
Alpine.data('navbar', (sectionIds = []) => ({
    scrolled: false,
    open: false,
    // Kosong sampai ada section yang terlihat (halaman detail project tidak punya section ini).
    active: null,

    init() {
        const onScroll = () => {
            this.scrolled = window.scrollY > 120;
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });

        // Section dianggap aktif saat melewati garis tengah layar.
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        this.active = entry.target.id;
                    }
                });
            },
            { rootMargin: '-45% 0px -50% 0px' },
        );

        sectionIds
            .map((id) => document.getElementById(id))
            .filter(Boolean)
            .forEach((section) => observer.observe(section));
    },

    isActive(id) {
        return this.active === id;
    },

    close() {
        this.open = false;
    },
}));

/**
 * Form kontak Box, Box (PRD F8). Tanpa JS form tetap terkirim biasa (POST + redirect);
 * dengan JS dikirim lewat fetch supaya tidak reload. Validasi tetap di server.
 */
Alpine.data('contactForm', () => ({
    sending: false,
    sent: false,
    submitted: false,
    successMessage: '',
    errors: {},

    error(field) {
        return this.errors[field]?.[0] ?? null;
    },

    async submit(form) {
        this.sending = true;
        this.submitted = true;
        this.errors = {};

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            const data = await response.json().catch(() => ({}));

            if (response.ok) {
                this.successMessage = data.message;
                this.sent = true;
                form.reset();
            } else if (response.status === 422) {
                this.errors = data.errors ?? {};
                this.$nextTick(() => form.querySelector('[aria-invalid="true"]')?.focus());
            } else if (response.status === 419) {
                this.errors = { message: ['Sesi halaman sudah kedaluwarsa. Muat ulang halaman lalu kirim lagi.'] };
            } else {
                this.errors = { message: ['Pesan gagal terkirim. Coba lagi sebentar lagi.'] };
            }
        } catch {
            this.errors = { message: ['Koneksi bermasalah. Cek internet lalu coba lagi.'] };
        } finally {
            this.sending = false;
        }
    },

    reset() {
        this.sent = false;
        this.errors = {};
    },
}));

/**
 * Maskot pojok (PRD F9): bubble chat berganti sesuai section yang terlihat.
 * Sengaja selalu tampil (tidak bisa ditutup) atas permintaan pemilik situs.
 */
Alpine.data('mascot', (messages = {}) => ({
    message: messages.hero ?? Object.values(messages)[0] ?? '',

    init() {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && messages[entry.target.id]) {
                        this.message = messages[entry.target.id];
                    }
                });
            },
            { rootMargin: '-45% 0px -50% 0px' },
        );

        Object.keys(messages)
            .map((id) => document.getElementById(id))
            .filter(Boolean)
            .forEach((section) => observer.observe(section));
    },
}));

/**
 * Tab kategori di chart Race Strategy. Pola tab ARIA: panah kiri/kanan & Home/End berpindah tab,
 * hanya tab aktif yang masuk urutan Tab. Tanpa JS semua panel tetap tampil bertumpuk.
 */
Alpine.data('raceTabs', (keys = [], initial = null) => ({
    active: initial ?? keys[0] ?? null,

    isActive(key) {
        return this.active === key;
    },

    select(key, focus = false) {
        this.active = key;

        if (focus) {
            this.$nextTick(() => document.getElementById(`race-tab-${key}`)?.focus());
        }
    },

    /** Buka detail stint (<dialog> bawaan browser; fokus kembali ke tombol saat ditutup). */
    openStint(id) {
        document.getElementById(id)?.showModal();
    },

    onKeydown(event) {
        const index = keys.indexOf(this.active);
        const moves = {
            ArrowRight: (index + 1) % keys.length,
            ArrowLeft: (index - 1 + keys.length) % keys.length,
            Home: 0,
            End: keys.length - 1,
        };

        if (event.key in moves) {
            event.preventDefault();
            this.select(keys[moves[event.key]], true);
        }
    },
}));

const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/**
 * Mobil kecil di lintasan Race Progress (design.md §5): bergerak mengikuti scroll halaman.
 * Desktop: sepanjang garis horizontal. HP: turun sepanjang garis vertikal (diputar lewat CSS).
 * Dengan reduced-motion mobil diam di garis start.
 */
Alpine.data('raceCar', () => ({
    init() {
        const track = this.$el;
        const car = this.$refs.car;

        if (!car || prefersReducedMotion()) {
            return;
        }

        const desktop = window.matchMedia('(min-width: 768px)');
        let ticking = false;

        const update = () => {
            ticking = false;
            const rect = track.getBoundingClientRect();
            const viewport = window.innerHeight;
            // 0 saat lintasan mulai terlihat dari bawah, 1 saat lintasan hampir selesai dilewati.
            const progress = Math.min(1, Math.max(0, (viewport * 0.85 - rect.top) / (rect.height + viewport * 0.35)));

            if (desktop.matches) {
                // Tetap di area lintasan yang terlihat walau lintasan sedang digeser ke samping.
                const distance = track.clientWidth - car.offsetWidth;
                car.style.translate = `${track.scrollLeft + progress * distance}px 0`;
            } else {
                const distance = track.offsetHeight - car.offsetWidth;
                car.style.translate = `0 ${progress * distance}px`;
            }
        };

        const schedule = () => {
            if (!ticking) {
                ticking = true;
                window.requestAnimationFrame(update);
            }
        };

        window.addEventListener('scroll', schedule, { passive: true });
        window.addEventListener('resize', schedule);
        track.addEventListener('scroll', schedule, { passive: true });
        update();
    },
}));

/**
 * Animasi masuk section (design.md §6): elemen [data-reveal] masuk sedikit dari kiri saat terlihat.
 * Kelas .reveal-ready baru dipasang di sini, jadi tanpa JS (atau dengan reduced-motion) konten tetap tampil.
 */
function initReveal() {
    const elements = document.querySelectorAll('[data-reveal]');

    if (elements.length === 0 || prefersReducedMotion() || !('IntersectionObserver' in window)) {
        return;
    }

    document.documentElement.classList.add('reveal-ready');

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                const el = entry.target;
                observer.unobserve(el);
                el.classList.add('is-visible');
                // Setelah selesai, lepas atribut supaya delay stagger tidak memperlambat hover kartu.
                // Abaikan transitionend dari elemen anak (event-nya ikut "menggelembung" ke atas).
                const finish = (event) => {
                    if (event.target !== el) {
                        return;
                    }
                    el.removeEventListener('transitionend', finish);
                    el.removeAttribute('data-reveal');
                    el.classList.remove('is-visible');
                };
                el.addEventListener('transitionend', finish);
            });
        },
        { rootMargin: '0px 0px -10% 0px' },
    );

    elements.forEach((el) => observer.observe(el));
}

initReveal();

/**
 * Kartu miring mengikuti kursor (efek 3D tilt) untuk elemen [data-tilt], misalnya kartu Pit Wall.
 * Elemen anak [data-tilt-depth] (gambar maskot) ikut bergeser sedikit → kesan parallax.
 * Hanya untuk perangkat dengan mouse (bukan layar sentuh) dan tanpa reduced-motion.
 * Posisi ditulis ke CSS variable; transform-nya ada di app.css (.tilt).
 */
function initTilt() {
    const cards = document.querySelectorAll('[data-tilt]');
    const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    if (cards.length === 0 || !canHover || prefersReducedMotion()) {
        return;
    }

    const MAX_DEG = 5;
    const MAX_SHIFT = 8; // px, untuk gambar [data-tilt-depth]

    cards.forEach((card) => {
        let frame = null;

        card.classList.add('tilt');

        card.addEventListener('pointermove', (event) => {
            if (frame) {
                return;
            }
            frame = requestAnimationFrame(() => {
                frame = null;
                const rect = card.getBoundingClientRect();
                // -0.5 … 0.5 dari tengah kartu
                const x = (event.clientX - rect.left) / rect.width - 0.5;
                const y = (event.clientY - rect.top) / rect.height - 0.5;
                card.classList.add('is-tilting');
                card.style.setProperty('--tilt-x', `${(-y * MAX_DEG * 2).toFixed(2)}deg`);
                card.style.setProperty('--tilt-y', `${(x * MAX_DEG * 2).toFixed(2)}deg`);
                card.style.setProperty('--shift-x', `${(x * MAX_SHIFT * 2).toFixed(1)}px`);
                card.style.setProperty('--shift-y', `${(y * MAX_SHIFT * 2).toFixed(1)}px`);
            });
        });

        card.addEventListener('pointerleave', () => {
            if (frame) {
                cancelAnimationFrame(frame);
                frame = null;
            }
            card.classList.remove('is-tilting');
            ['--tilt-x', '--tilt-y', '--shift-x', '--shift-y'].forEach((name) => card.style.removeProperty(name));
        });
    });
}

initTilt();

Alpine.plugin(focus);
window.Alpine = Alpine;
Alpine.start();
