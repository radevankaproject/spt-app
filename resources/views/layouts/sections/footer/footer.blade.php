@php
$containerFooter =
isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
? 'container-xxl'
: 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme border-top">
    <div class="{{ $containerFooter }}">
        <div class="footer-container d-flex align-items-center justify-content-between py-3 flex-column flex-md-row gap-3">
            <!-- Left Side: Copyright -->
            <div class="text-body text-center text-md-start" style="font-size: 0.9rem;">
                <span class="fw-medium">© <script>document.write(new Date().getFullYear());</script></span>
                <span class="text-muted d-none d-sm-inline-block mx-1">|</span> 
                <span class="d-block d-sm-inline-block mt-1 mt-sm-0">
                    Made with <i class="ti tabler-heart-filled text-danger"></i> by 
                    <a href="#" target="_blank" class="footer-link fw-bold text-primary">Tim IT UPT Perparkiran</a>
                </span>
            </div>
            
            <!-- Right Side: Version & Load Time -->
            <div class="d-flex align-items-center flex-wrap justify-content-center justify-content-md-end gap-3 text-body">
                <!-- Page Load Time -->
                <div class="d-flex align-items-center text-muted small" title="Waktu pemuatan halaman">
                    <i class="ti tabler-bolt me-1 fs-6"></i> 
                    <span id="page-load-time" class="fw-medium"></span>
                </div>
                
                <!-- Version Badge -->
                <div>
                    <a href="javascript:void(0);" id="changelog-link" class="badge bg-label-primary rounded-pill px-3 py-2 text-decoration-none shadow-sm" data-bs-toggle="modal" data-bs-target="#changelogModal" style="transition: all 0.2s ease; cursor: pointer;">
                        <i class="ti tabler-rocket me-1"></i>
                        {{ isset($latestAppVersion) && $latestAppVersion ? $latestAppVersion->version : 'v1.0.0' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- / Footer -->

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
