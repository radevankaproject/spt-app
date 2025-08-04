<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl">
        <div class="footer-container d-flex align-items-center justify-content-between py-3 flex-md-row flex-column">
            <div class="text-body mb-2 mb-md-0">
                ©
                <script>
                    document.write(new Date().getFullYear());
                </script>
                , made with ❤️ by <a href="#" target="_blank" class="footer-link">Tim IT UPT Perparkiran</a>
            </div>
            <div class="d-none d-lg-inline-block">
                {{-- ✅ PERUBAHAN DI SINI --}}
                <span class="text-body">
                    <a href="javascript:void(0);" id="changelog-link" class="footer-link ms-4" data-bs-toggle="modal"
                        data-bs-target="#changelogModal">
                        {{ $latestAppVersion->version ?? '1.0.0' }}
                    </a>
                </span>
                <span class="text-body">
                    | Page Loaded in: <span id="page-load-time" class="fw-medium"></span>
            </div>
        </div>
    </div>
</footer>
<div class="modal fade" id="changelogModal" tabindex="-1" aria-labelledby="changelogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changelogModalLabel">Histori & Catatan Perubahan Aplikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="changelog-content">
                    {{-- Konten akan diisi oleh JavaScript --}}
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('load', function() {
        const loadTimeElement = document.getElementById('page-load-time');

        // Ambil waktu sekarang relatif terhadap time origin
        const now = performance.now();
        const loadTime = now; // karena now - 0 (start time) = now

        let displayTime;
        if (loadTime >= 1000) {
            displayTime = (loadTime / 1000).toFixed(2) + ' detik';
        } else if (loadTime >= 1) {
            displayTime = loadTime.toFixed(2) + ' milidetik';
        } else {
            displayTime = (loadTime * 1000).toFixed(2) + ' mikrodetik';
        }

        if (loadTimeElement) {
            loadTimeElement.textContent = displayTime;
        }
    });
</script>
