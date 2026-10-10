import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

/**
 * Navbar (PRD F2): pill mengecil jadi tombol bulat saat scroll, scrollspy, menu layar penuh.
 * Halaman tetap bisa dipakai tanpa JavaScript karena semua menu berupa link anchor biasa.
 */
Alpine.data('navbar', (sectionIds = []) => ({
    scrolled: false,
    open: false,
    active: sectionIds[0] ?? null,

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

Alpine.plugin(focus);
window.Alpine = Alpine;
Alpine.start();
