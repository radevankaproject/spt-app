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
                <div class="alert alert-primary d-flex mb-4 border-0 shadow-sm rounded-3" role="alert">
                    <span class="alert-icon align-middle me-3 mt-1">
                      <i class="ti tabler-info-circle fs-4"></i>
                    </span>
                    <div style="font-size: 0.9rem; line-height: 1.5;">
                        Semua catatan perubahan (History Log) dari aplikasi <strong>Sistem Perjanjian Kerja Sama Perparkiran (SPKP)</strong> dicatat di bawah ini. Dokumen ini merangkum seluruh perjalanan evolusi aplikasi dari inisialisasi awal hingga versi mutakhir.
                    </div>
                </div>
                <div id="changelog-content" data-loaded="false">
                    <div class="text-center py-4" id="changelog-loading">
                        <div class="m3-wavy-wrapper mx-auto" style="width: 80px; height: 80px;">
                            <svg class="m3-wavy-svg" viewBox="0 0 120 120">
                                <path id="modal-wavy-track" fill="none" stroke="rgba(102,108,255,0.15)" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                                <path id="modal-wavy-progress" fill="none" stroke="#666cff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="m3-wavy-center">
                                <i class="ti tabler-rocket text-primary" style="font-size: 2rem; animation: pulse-logo 2s ease-in-out infinite;"></i>
                            </div>
                        </div>
                        <p class="text-muted mt-2 small fw-medium">Memuat versi...</p>
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

        // Setup Modal Wavy Loader Path
        const modalTrack = document.getElementById('modal-wavy-track');
        const modalProg = document.getElementById('modal-wavy-progress');
        if (modalTrack && modalProg && typeof generateWavyCirclePath === 'function') {
            const modalPathData = generateWavyCirclePath(60, 60, 45, 2, 16);
            modalTrack.setAttribute('d', modalPathData);
            modalProg.setAttribute('d', modalPathData);
        }

        // Fetch Changelog Data
        const changelogModal = document.getElementById('changelogModal');
        const changelogContent = document.getElementById('changelog-content');
        
        if (changelogModal && changelogContent) {
            changelogModal.addEventListener('show.bs.modal', function () {
                if (changelogContent.dataset.loaded === "true") return;

                fetch('{{ route('app.versions') }}')
                    .then(response => response.json())
                    .then(data => {
                        let html = '';
                        if (data.length > 0) {
                            html += '<div class="accordion mt-3" id="changelogAccordion">';
                            data.forEach((version, index) => {
                                const dateObj = new Date(version.release_date);
                                const formattedDate = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                                const isFirst = index === 0;
                                const showClass = isFirst ? 'show' : '';
                                const collapsedClass = isFirst ? '' : 'collapsed';
                                
                                html += `
                                <div class="accordion-item card shadow-none border mb-2">
                                    <h2 class="accordion-header" id="heading${index}">
                                        <button type="button" class="accordion-button ${collapsedClass} d-flex align-items-center bg-lighter" data-bs-toggle="collapse" data-bs-target="#accordion${index}" aria-expanded="${isFirst}" aria-controls="accordion${index}">
                                            <span class="fw-bold text-primary me-2">${version.version}</span>
                                            <span class="badge bg-label-secondary small">${formattedDate}</span>
                                        </button>
                                    </h2>
                                    <div id="accordion${index}" class="accordion-collapse collapse ${showClass}" data-bs-parent="#changelogAccordion" aria-labelledby="heading${index}">
                                        <div class="accordion-body pt-3 pb-3">
                                            <div class="changelog-content text-dark" style="font-size: 0.95rem;">
                                                ${version.changelog}
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                            });
                            html += '</div>';
                        } else {
                            html = `<div class="text-center text-muted py-4"><i class="ti tabler-box fs-1 mb-2 opacity-50"></i><br>Belum ada catatan versi.</div>`;
                        }
                        
                        changelogContent.innerHTML = html;
                        changelogContent.dataset.loaded = "true";
                    })
                    .catch(error => {
                        changelogContent.innerHTML = `<div class="text-center text-danger py-4"><i class="ti tabler-alert-circle fs-1 mb-2"></i><br>Gagal memuat data versi.</div>`;
                    });
            });
        }
    });
</script>
<style>
    .changelog-content ul { padding-left: 1.5rem; margin-bottom: 1rem; }
    .changelog-content li { margin-bottom: 0.25rem; line-height: 1.6; }
    .changelog-content strong { color: #566a7f; }
</style>
