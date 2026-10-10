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
 * Maskot pojok (PRD F9): bubble chat berganti sesuai section yang terlihat. Bisa disembunyikan;
 * pilihan disimpan di localStorage (dibungkus try/catch karena bisa diblokir browser).
 */
const MASCOT_KEY = 'dun12-maskot-disembunyikan';

Alpine.data('mascot', (messages = {}) => ({
    hidden: false,
    message: messages.hero ?? Object.values(messages)[0] ?? '',

    init() {
        try {
            this.hidden = window.localStorage.getItem(MASCOT_KEY) === '1';
        } catch {
            this.hidden = false;
        }

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

    hide() {
        this.hidden = true;
        try {
            window.localStorage.setItem(MASCOT_KEY, '1');
        } catch {
            // Penyimpanan diblokir: maskot tetap tersembunyi sampai halaman dimuat ulang.
        }
    },
}));

Alpine.plugin(focus);
window.Alpine = Alpine;
Alpine.start();
