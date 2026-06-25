<div class="card mb-6 border-0 shadow-sm">
  <div class="card-header align-items-center d-flex justify-content-between p-4 bg-white border-bottom">
    <h5 class="card-title mb-0 fw-bold"><i class="icon-base ti tabler-activity icon-lg me-2 text-primary"></i>Aktivitas Akun Saya</h5>
    <form action="{{ route('profile.index') }}" method="GET" class="d-flex align-items-center">
        <input type="hidden" name="tab" value="aktivitas">
        <div class="input-group input-group-sm border-0 bg-lighter rounded-pill px-2">
            <span class="input-group-text border-0 bg-transparent text-muted px-2"><i class="icon-base ti tabler-search"></i></span>
            <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none px-1" placeholder="Cari aktivitas..." value="{{ request('search') }}" onchange="this.form.submit()">
        </div>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover border-top mb-0">
        <thead class="table-light">
            <tr>
                <th class="ps-4">Aksi</th>
                <th>Deskripsi</th>
                <th>IP & Browser</th>
                <th class="text-end pe-4">Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paginatedData ?? collect() as $data)
                <tr>
                    <td class="fw-bold text-primary ps-4">
                        <span class="d-inline-flex align-items-center">
                            @if(str_contains(strtolower($data->action), 'login'))
                                <i class="ti tabler-login text-success me-2 bg-success bg-opacity-10 p-1 rounded"></i>
                            @elseif(str_contains(strtolower($data->action), 'logout'))
                                <i class="ti tabler-logout text-danger me-2 bg-danger bg-opacity-10 p-1 rounded"></i>
                            @elseif(str_contains(strtolower($data->action), 'password'))
                                <i class="ti tabler-lock text-warning me-2 bg-warning bg-opacity-10 p-1 rounded"></i>
                            @else
                                <i class="ti tabler-edit text-info me-2 bg-info bg-opacity-10 p-1 rounded"></i>
                            @endif
                            {{ $data->action ?? '-' }}
                        </span>
                    </td>
                    <td><span class="text-dark">{{ $data->description ?? '-' }}</span></td>
                    <td>
                        <div class="d-flex flex-column">
                            <small class="mb-1 text-dark fw-medium"><i class="ti tabler-network text-muted me-1"></i> {{ $data->ip_address ?? '-' }}</small>
                            @if($data->user_agent)
                            <div>
                                <a class="text-primary small d-inline-flex align-items-center text-decoration-none fw-medium" data-bs-toggle="collapse" href="#collapseBrowser{{ $data->id }}" role="button" aria-expanded="false" aria-controls="collapseBrowser{{ $data->id }}">
                                    <i class="ti tabler-browser me-1"></i> Detail Browser <i class="ti tabler-chevron-down ms-1" style="font-size: 10px;"></i>
                                </a>
                                <div class="collapse mt-2" id="collapseBrowser{{ $data->id }}">
                                    <div class="p-2 bg-lighter rounded-3 small text-break text-muted border" style="max-width: 300px; font-size: 0.75rem; line-height: 1.4;">
                                        {{ $data->user_agent }}
                                    </div>
                                </div>
                            </div>
                            @else
                            <small class="text-muted"><i class="ti tabler-browser me-1"></i> -</small>
                            @endif
                        </div>
                    </td>
                    <td class="text-end text-muted small pe-4">{{ $data->created_at ? $data->created_at->translatedFormat('d M Y H:i') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-label-secondary rounded-circle mb-3" style="width: 60px; height: 60px;">
                            <i class="ti tabler-inbox text-muted icon-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Belum ada aktivitas</h6>
                        <p class="text-muted mb-0 small">Belum ada aktivitas yang tercatat untuk akun Anda.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
  </div>
  @if(isset($paginatedData) && $paginatedData->hasPages())
  <div class="card-footer py-3 bg-white border-top">
      {{ $paginatedData->links('pagination::bootstrap-5') }}
  </div>
  @endif
</div>
