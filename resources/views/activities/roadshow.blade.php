@extends('layouts.app')

@section('title', 'Roadshow Promosi - SIM-PROMOSI')
@section('page-title', 'Roadshow Promosi')

@section('content')
<div class="container-fluid py-4">
    <x-ui.card
        title="Roadshow Promosi"
        subtitle="Kelola dan pantau kegiatan roadshow promosi di seluruh wilayah"
        icon="bi bi-megaphone"
        class="border-0"
    >
        <x-slot:headerActions>
            <x-ui.button
                :href="route('activities.roadshow.create')"
                variant="light"
                size="lg"
                icon="bi bi-plus-circle-fill"
                class="shadow-sm"
            >
                Tambah Data Baru
            </x-ui.button>
        </x-slot:headerActions>
        <!-- Alert -->
        @if (session('success'))
            <x-ui.alert type="success" dismissable="true" class="shadow-sm border-0 mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                </div>
            </x-ui.alert>
        @endif

            <!-- Search Form -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-0"
                               placeholder="Cari provinsi atau kabupaten..."
                               value="{{ request('search', '') }}">
                    </div>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <h3 class="text-success fw-bold">{{ count($roadshows) }}</h3>
                            <p class="text-muted mb-0">Wilayah Terdata</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <h3 class="text-primary fw-bold">
                                {{ array_sum(array_column($roadshows, 'jumlah_kegiatan')) }}
                            </h3>
                            <p class="text-muted mb-0">Total Kegiatan</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <h3 class="text-info fw-bold">
                                {{ count(array_filter($roadshows, function($item) { return $item['jumlah_kegiatan'] > 0; })) }}
                            </h3>
                            <p class="text-muted mb-0">Wilayah Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <h3 class="text-warning fw-bold">
                                {{ count(array_filter($roadshows, function($item) { return $item['jumlah_kegiatan'] == 0; })) }}
                            </h3>
                            <p class="text-muted mb-0">Wilayah Belum Aktif</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('activities.roadshow') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Provinsi</label>
                                <select name="provinsi" class="form-select">
                                    <option value="">Semua Provinsi</option>
                                    @foreach(collect($roadshows)->pluck('provinsi', 'provinsi')->unique()->sort() as $provinsi)
                                        <option value="{{ $provinsi }}" {{ request('provinsi') == $provinsi ? 'selected' : '' }}>
                                            {{ $provinsi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Urutkan Berdasarkan</label>
                                <select name="sort" class="form-select">
                                    <option value="">Default (Jumlah Kegiatan Terbanyak)</option>
                                    <option value="jumlah_kegiatan_desc" {{ request('sort') == 'jumlah_kegiatan_desc' ? 'selected' : '' }}>
                                        Jumlah Kegiatan (terbanyak)
                                    </option>
                                    <option value="jumlah_kegiatan_asc" {{ request('sort') == 'jumlah_kegiatan_asc' ? 'selected' : '' }}>
                                        Jumlah Kegiatan (tersedikit)
                                    </option>
                                    <option value="provinsi" {{ request('sort') == 'provinsi' ? 'selected' : '' }}>
                                        Nama Provinsi
                                    </option>
                                    <option value="kabupaten" {{ request('sort') == 'kabupaten' ? 'selected' : '' }}>
                                        Nama Kabupaten
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('activities.roadshow') }}" class="btn btn-outline-secondary">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Roadshow Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th class="text-center" width="60px">No</th>
                            <th>Provinsi</th>
                            <th>Kabupaten</th>
                            <th class="text-center" width="120px">Jumlah Kegiatan</th>
                            <th>Nama Kegiatan Terakhir</th>
                            <th class="text-center" width="120px">Tanggal Terakhir</th>
                            <th>Penanggung Jawab</th>
                            <th class="text-center" width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roadshows as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-semibold">{{ $item['provinsi'] }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $item['kabupaten'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill px-3">{{ $item['jumlah_kegiatan'] }}</span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $item['nama_kegiatan_terakhir'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $item['tanggal_terakhir'] }}</span>
                            </td>
                            <td>
                                <span class="text-primary fw-medium">{{ $item['penanggung_jawab'] }}</span>
                            </td>
                            <td class="text-center">
                                <a href="#" class="btn btn-outline-primary btn-sm detail-btn"
                                   data-provinsi="{{ $item['provinsi'] }}"
                                   data-kabupaten="{{ $item['kabupaten'] }}">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle fs-1 mb-3"></i>
                                <p class="mb-0">Data tidak ditemukan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #276A2B 0%, #38a169 100%);">
                <div class="d-flex flex-column">
                    <h5 class="modal-title fw-bold" id="detailModalLabel">Detail Kegiatan</h5>
                    <small class="opacity-75" id="locationSubtitle">Wilayah: -</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light btn-sm" id="exportPdfBtn" title="Export ke PDF">
                        <i class="bi bi-file-pdf"></i> PDF
                    </button>
                    <button type="button" class="btn btn-light btn-sm" id="exportExcelBtn" title="Export ke Excel">
                        <i class="bi bi-file-excel"></i> Excel
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <!-- Search dan Filter -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" id="detailSearch" class="form-control border-start-0 ps-0 fs-5 py-3"
                                   placeholder="Cari nama sekolah, tanggal, atau penanggung jawab...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded-3 h-100 d-flex align-items-center">
                            <div class="text-center w-100">
                                <i class="bi bi-clipboard-data text-success fs-2"></i>
                                <p class="mb-0 mt-2"><strong id="totalCount">0</strong> Kegiatan Ditemukan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Detail -->
                <div class="table-responsive rounded-3 overflow-hidden">
                    <table class="table table-hover align-middle shadow-sm">
                        <thead class="table-success">
                            <tr>
                                <th width="60px" class="fw-bold text-uppercase text-secondary small">No</th>
                                <th class="fw-bold text-uppercase text-secondary small">Tanggal</th>
                                <th class="fw-bold text-uppercase text-secondary small">Nama Sekolah</th>
                                <th class="fw-bold text-uppercase text-secondary small">Penanggungjawab</th>
                                <th class="fw-bold text-uppercase text-secondary small">Program Studi</th>
                                <th class="fw-bold text-uppercase text-secondary small">Jumlah Alumni</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody" class="border-top-0">
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="bi bi-info-circle text-muted fs-1 mb-3"></i>
                                        <h5 class="text-muted">Pilih wilayah untuk melihat detail kegiatan</h5>
                                        <p class="text-muted mb-0">Silakan klik tombol "Detail" pada salah satu wilayah untuk menampilkan data kegiatan</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div id="paginationControls" class="mt-4 d-none">
                    <nav aria-label="Detail pagination">
                        <ul class="pagination justify-content-center" id="paginationList">
                            <!-- Pagination will be inserted here dynamically -->
                        </ul>
                    </nav>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div>
                            <select id="perPageSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10" selected>10 per halaman</option>
                                <option value="25">25 per halaman</option>
                                <option value="50">50 per halaman</option>
                                <option value="100">100 per halaman</option>
                            </select>
                        </div>
                        <div class="text-muted" id="paginationInfo">
                            Menampilkan 0 dari 0 data
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Include CSRF token for AJAX requests
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Current pagination state
let currentPage = 1;
let currentPerPage = 10;

// Function to load detail data with pagination
function loadDetailData(provinsi, kabupaten, page = 1, perPage = 10) {
    // Show loading indicator
    document.getElementById('detailTableBody').innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-5">
                <div class="d-flex flex-column align-items-center justify-content-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="text-muted">Memuat data...</h5>
                </div>
            </td>
        </tr>
    `;

    // Hide pagination controls while loading
    document.getElementById('paginationControls').classList.add('d-none');

    // Build query parameters
    const params = new URLSearchParams({
        page: page,
        per_page: perPage
    });

    // Ambil data dari server dengan CSRF token dan pagination
    fetch(`{{ route('activities.roadshow.detail', ['provinsi' => 'PROV', 'kabupaten' => 'KAB']) }}`.replace('PROV', provinsi).replace('KAB', kabupaten) + '?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Update title modal
            document.getElementById('detailModalLabel').textContent = `Detail - ${data.kabupaten}, ${data.provinsi}`;

            // Populate tabel
            const tableBody = document.getElementById('detailTableBody');
            if (data.schools.length > 0) {
                tableBody.innerHTML = data.schools.map((school, idx) => `
                    <tr class="border-bottom">
                        <td class="fw-bold text-center">${school.id}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-event me-2 text-success"></i>
                                <span class="fw-medium">${school.tanggal}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-building me-2 text-primary"></i>
                                <span class="fw-semibold">${school.nama_sekolah}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-badge me-2 text-info"></i>
                                <span>${school.penanggungjawab}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                ${(school.prodi_list && school.prodi_list.length > 0)
                                    ? school.prodi_list.map(prodi =>
                                        '<span class="badge bg-light text-dark border">' +
                                        // Sanitize the prodi name to prevent XSS
                                        prodi.replace(/</g, '&lt;').replace(/>/g, '&gt;') +
                                        '</span>'
                                      ).join('')
                                    : '<span class="text-muted fst-italic">-</span>'}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-people me-2 text-warning"></i>
                                <span class="fw-bold text-success">${school.alumni}</span>
                            </div>
                        </td>
                    </tr>
                `).join('');

                // Update total count
                document.getElementById('totalCount').textContent = data.pagination.total;

                // Show and update pagination controls
                updatePaginationControls(data.pagination);
                document.getElementById('paginationControls').classList.remove('d-none');
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-clipboard-x text-muted fs-1 mb-3"></i>
                                <h5 class="text-muted">Tidak Ada Kegiatan</h5>
                                <p class="text-muted mb-0">Belum ada kegiatan roadshow yang tercatat untuk wilayah ini</p>
                            </div>
                        </td>
                    </tr>
                `;

                // Update total count
                document.getElementById('totalCount').textContent = '0';

                // Hide pagination controls if no data
                document.getElementById('paginationControls').classList.add('d-none');
            }

            // Store current provinsi and kabupaten
            window.currentProvinsi = provinsi;
            window.currentKabupaten = kabupaten;

            // Update current pagination state
            currentPage = page;
            currentPerPage = perPage;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('detailTableBody').innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <i class="bi bi-exclamation-triangle text-danger fs-1 mb-3"></i>
                            <h5 class="text-danger">Terjadi Kesalahan</h5>
                            <p class="text-muted mb-0">Gagal memuat data. Silakan coba lagi nanti.</p>
                        </div>
                    </td>
                </tr>
            `;

            // Update total count
            document.getElementById('totalCount').textContent = '0';

            // Hide pagination controls on error
            document.getElementById('paginationControls').classList.add('d-none');

            // Show error notification
            alert('Terjadi kesalahan saat memuat data: ' + error.message);
        });
}

// Function to update pagination controls
function updatePaginationControls(pagination) {
    const paginationList = document.getElementById('paginationList');
    const paginationInfo = document.getElementById('paginationInfo');

    // Update pagination info
    paginationInfo.textContent = `Menampilkan ${pagination.from}-${pagination.to} dari ${pagination.total} data`;

    // Build pagination HTML
    let paginationHTML = '';

    // Previous button
    if (pagination.current_page > 1) {
        paginationHTML += `<li class="page-item">
            <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
        </li>`;
    } else {
        paginationHTML += `<li class="page-item disabled">
            <span class="page-link">Previous</span>
        </li>`;
    }

    // Page numbers
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

    if (startPage > 1) {
        paginationHTML += `<li class="page-item">
            <a class="page-link" href="#" data-page="1">1</a>
        </li>`;

        if (startPage > 2) {
            paginationHTML += `<li class="page-item disabled">
                <span class="page-link">...</span>
            </li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        if (i === pagination.current_page) {
            paginationHTML += `<li class="page-item active">
                <span class="page-link">${i}</span>
            </li>`;
        } else {
            paginationHTML += `<li class="page-item">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
        }
    }

    if (endPage < pagination.last_page) {
        if (endPage < pagination.last_page - 1) {
            paginationHTML += `<li class="page-item disabled">
                <span class="page-link">...</span>
            </li>`;
        }

        paginationHTML += `<li class="page-item">
            <a class="page-link" href="#" data-page="${pagination.last_page}">${pagination.last_page}</a>
        </li>`;
    }

    // Next button
    if (pagination.current_page < pagination.last_page) {
        paginationHTML += `<li class="page-item">
            <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
        </li>`;
    } else {
        paginationHTML += `<li class="page-item disabled">
            <span class="page-link">Next</span>
        </li>`;
    }

    paginationList.innerHTML = paginationHTML;
}

// Handle detail button clicks
document.addEventListener('click', function(e) {
    if (e.target.closest('.detail-btn')) {
        e.preventDefault();
        const btn = e.target.closest('.detail-btn');
        const provinsi = btn.getAttribute('data-provinsi');
        const kabupaten = btn.getAttribute('data-kabupaten');

        // Tampilkan modal
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();

        // Update subtitle
        document.getElementById('locationSubtitle').textContent = `Wilayah: ${kabupaten}, ${provinsi}`;

        // Load data with default pagination
        loadDetailData(provinsi, kabupaten, 1, currentPerPage);
    }
});

// Handle pagination clicks
document.addEventListener('click', function(e) {
    if (e.target.closest('.page-link')) {
        e.preventDefault();
        const link = e.target.closest('.page-link');
        const page = parseInt(link.getAttribute('data-page'));

        if (page && window.currentProvinsi && window.currentKabupaten) {
            loadDetailData(window.currentProvinsi, window.currentKabupaten, page, currentPerPage);
        }
    }
});

// Handle per page selection change
document.getElementById('perPageSelect').addEventListener('change', function() {
    const perPage = parseInt(this.value);
    currentPerPage = perPage;

    if (window.currentProvinsi && window.currentKabupaten) {
        loadDetailData(window.currentProvinsi, window.currentKabupaten, 1, perPage);
    }
});

// Search filter di modal detail
document.getElementById('detailSearch').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#detailTableBody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        if (row.cells.length > 0) {
            const text = row.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm);
            row.style.display = isVisible ? '' : 'none';

            if (isVisible && row.cells[0].textContent !== 'Loading...' &&
                row.cells[0].textContent !== 'Tidak Ada Kegiatan' &&
                row.cells[0].textContent !== 'Terjadi Kesalahan') {
                visibleCount++;
            }
        }
    });

    // Update total count based on filtered results
    document.getElementById('totalCount').textContent = visibleCount;
});

// Fungsi untuk menghasilkan PDF
document.getElementById('exportPdfBtn').addEventListener('click', function() {
    if (!window.currentProvinsi || !window.currentKabupaten) {
        alert('Tidak ada data untuk diekspor');
        return;
    }

    // Redirect ke route server-side export PDF
    const url = `/activities/roadshow/export/pdf/${encodeURIComponent(window.currentProvinsi)}/${encodeURIComponent(window.currentKabupaten)}`;
    window.location.href = url;
});

// Fungsi untuk menghasilkan Excel
document.getElementById('exportExcelBtn').addEventListener('click', function() {
    if (!window.currentProvinsi || !window.currentKabupaten) {
        alert('Tidak ada data untuk diekspor');
        return;
    }

    // Redirect ke route server-side export Excel
    const url = `/activities/roadshow/export/excel/${encodeURIComponent(window.currentProvinsi)}/${encodeURIComponent(window.currentKabupaten)}`;
    window.location.href = url;
});

// Search functionality for the table
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        if (row.cells.length > 0) {
            const text = row.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm);
            row.style.display = isVisible ? '' : 'none';
        }
    });
});

</script>

    </x-ui.card>
</div>
@endsection