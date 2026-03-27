{{-- resources/views/layouts/partials/_alerts.blade.php --}}

<style>
    /* ✅ 1. DYNAMIC ISLAND (SUCCESS) */
    .dynamic-island-wrapper {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10000;
        display: flex;
        justify-content: center;
        pointer-events: none; /* Agar klik tembus ke bawah saat tersembunyi */
    }

    .dynamic-island {
        background: #000000;
        color: #ffffff;
        border-radius: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 25px;
        height: 48px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        /* Animasi ala Apple Spring */
        transform: scale(0.6) translateY(-30px);
        opacity: 0;
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        pointer-events: auto;
    }

    .dynamic-island.island-show {
        transform: scale(1) translateY(0);
        opacity: 1;
    }

    .dynamic-island i {
        color: #34d399; /* Hijau Success */
        font-size: 22px;
        margin-right: 12px;
    }

    .dynamic-island span {
        font-weight: 500;
        font-size: 14px;
        letter-spacing: 0.3px;
    }

    /* ✅ 2. CUSTOM TOASTR (ERROR / VALIDATION) */
    .custom-toastr-wrapper {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .custom-toastr {
        background: #ffffff;
        width: 360px;
        border-radius: 12px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        border-left: 6px solid #ef4444; /* Merah Danger */
        overflow: hidden;
        animation: toastrSlideIn 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }

    .custom-toastr .toastr-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px;
        background: #fef2f2;
        border-bottom: 1px solid #fee2e2;
    }

    .custom-toastr .toastr-title {
        font-weight: 700;
        color: #b91c1c;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-size: 15px;
    }

    .custom-toastr .toastr-body {
        padding: 15px 18px;
        font-size: 14px;
        color: #4b5563;
        line-height: 1.5;
    }

    @keyframes toastrSlideIn {
        from { transform: translateX(120%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>

{{-- ========================================== --}}
{{-- 🟢 RENDER DYNAMIC ISLAND (SUCCESS)         --}}
{{-- ========================================== --}}
@if (session('success'))
    <div class="dynamic-island-wrapper">
        <div id="dynamic-island" class="dynamic-island">
            <i class="ri icon-base ri-checkbox-circle-fill ri-22px"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const island = document.getElementById('dynamic-island');
            // Munculkan dengan delay sedikit agar animasi terlihat
            setTimeout(() => {
                island.classList.add('island-show');
            }, 100);

            // Hilangkan setelah 3.5 detik
            setTimeout(() => {
                island.classList.remove('island-show');
                setTimeout(() => island.parentElement.remove(), 500); // Bersihkan DOM
            }, 3500);
        });
    </script>
@endif


{{-- ========================================== --}}
{{-- 🔴 RENDER TOASTR (ERROR / VALIDATION)      --}}
{{-- ========================================== --}}
@if (session('error') || $errors->any())
    <div class="custom-toastr-wrapper">
        <div class="custom-toastr">
            <div class="toastr-header">
                <div class="toastr-title">
                    <i class="ri icon-base ri-error-warning-fill ri-22px"></i>
                    Peringatan Sistem
                </div>
                {{-- Tombol Close: Menghapus div toastr ini saat diklik --}}
                <button type="button" class="btn-close" onclick="this.closest('.custom-toastr').remove()"></button>
            </div>
            <div class="toastr-body">
                {{-- Pesan Error Session --}}
                @if (session('error'))
                    <div class="mb-2 fw-bold text-danger">{!! session('error') !!}</div>
                @endif

                {{-- Pesan Validasi Form --}}
                @if ($errors->any())
                    <p class="mb-1 fw-bold">Periksa kembali data Anda:</p>
                    <ul class="mb-0 ps-3 text-danger">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endif
