{{-- Loading screen "Lights Out" (PRD F1) — bagian <head>. Sekali per sesi, dilewati jika reduced-motion.
     Sengaja inline & kecil: berjalan sebelum halaman digambar dan tidak bergantung pada bundle JS.
     Jika skrip ini gagal, kelas .lights-on tidak pernah dipasang sehingga overlay tidak muncul. --}}
<script>
    (function () {
        try {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            if (window.sessionStorage.getItem('dun12-lights-out')) return;
            window.sessionStorage.setItem('dun12-lights-out', '1');

            var root = document.documentElement;
            var done = function () { root.classList.remove('lights-on'); };
            root.classList.add('lights-on');

            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') done(); });
            document.addEventListener('click', function (e) {
                if (e.target.closest && e.target.closest('.lights-out')) done();
            });
            setTimeout(done, 1700);
        } catch (e) {
            document.documentElement.classList.remove('lights-on');
        }
    })();
</script>
