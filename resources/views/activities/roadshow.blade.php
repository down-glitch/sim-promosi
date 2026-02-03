@extends('layouts.app')

@section('title', 'Roadshow Promosi - SIM-PROMOSI')
@section('page-title', 'Roadshow Promosi')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient text-white py-3 px-4" style="background: linear-gradient(135deg, #276A2B 0%, #38a169 100%);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div class="flex-1">
                    <h4 class="mb-1 fw-bold"><i class="bi bi-megaphone me-2"></i>Roadshow Promosi</h4>
                    <p class="mb-0 opacity-75">Kelola dan pantau kegiatan roadshow promosi di seluruh wilayah</p>
                </div>
                <a href="{{ route('activities.roadshow.create') }}" class="btn btn-light btn-lg shadow-sm px-4 py-2">
                    <i class="bi bi-plus-circle-fill me-2"></i>Tambah Data Baru
                </a>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Alert -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
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

            <!-- Roadshow Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th class="text-center" width="60px">No</th>
                            <th>Provinsi</th>
                            <th>Kabupaten</th>
                            <th class="text-center" width="150px">Jumlah Kegiatan</th>
                            <th class="text-center" width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roadshows as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item['provinsi'] }}</td>
                            <td>{{ $item['kabupaten'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill px-3">{{ $item['jumlah_kegiatan'] }}</span>
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
                            <td colspan="5" class="text-center py-4 text-muted">
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
            </div>
        </div>
    </div>
</div>

<script>
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

        // Ambil data dari server
        fetch(`{{ route('activities.roadshow.detail', ['provinsi' => 'PROV', 'kabupaten' => 'KAB']) }}`.replace('PROV', provinsi).replace('KAB', kabupaten))
            .then(response => response.json())
            .then(data => {
                // Update title modal
                document.getElementById('detailModalLabel').textContent = `Detail - ${data.kabupaten}, ${data.provinsi}`;

                // Populate tabel
                const tableBody = document.getElementById('detailTableBody');
                if (data.schools.length > 0) {
                    tableBody.innerHTML = data.schools.map((school, idx) => `
                        <tr class="border-bottom">
                            <td class="fw-bold text-center">${idx + 1}</td>
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
                                            '<span class="badge bg-light text-dark border">' + prodi + '</span>'
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
                    document.getElementById('totalCount').textContent = data.schools.length;
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
                }

                // Store current provinsi and kabupaten
                window.currentProvinsi = provinsi;
                window.currentKabupaten = kabupaten;
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
            });
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

    const tableRows = document.querySelectorAll('#detailTableBody tr:not([style*="display: none"])');
    let tableHtml = '';

    tableRows.forEach((row, index) => {
        if (row.cells.length > 0) {
            const cells = row.cells;
            if (cells[0].textContent !== 'Loading...' &&
                cells[0].textContent !== 'Tidak Ada Kegiatan' &&
                cells[0].textContent !== 'Terjadi Kesalahan') {
                tableHtml += '<tr>';
                for (let i = 0; i < cells.length; i++) {
                    tableHtml += `<td>${cells[i].textContent}</td>`;
                }
                tableHtml += '</tr>';
            }
        }
    });

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Laporan Detail Roadshow - ${window.currentKabupaten}, ${window.currentProvinsi}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 20px; }
                .header h1 { font-size: 18px; margin-bottom: 5px; }
                .header p { font-size: 14px; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .footer { margin-top: 30px; font-size: 12px; color: #666; text-align: center; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN DETAIL ROADSHOW PROMOSI</h1>
                <p>Wilayah: ${window.currentKabupaten}, ${window.currentProvinsi}</p>
                <p>Tanggal Cetak: ${new Date().toLocaleDateString('id-ID')}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Sekolah</th>
                        <th>Penanggungjawab</th>
                        <th>Prodi</th>
                        <th>Alumni</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableHtml}
                </tbody>
            </table>
            <div class="footer">
                Laporan ini dicetak dari SIM-PROMOSI
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();

    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
});

// Fungsi untuk menghasilkan Excel (XLSX)
document.getElementById('exportExcelBtn').addEventListener('click', function() {
    if (!window.currentProvinsi || !window.currentKabupaten) {
        alert('Tidak ada data untuk diekspor');
        return;
    }

    // Check if XLSX library is loaded
    if (typeof XLSX === 'undefined') {
        // Create script element to load XLSX library
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
        script.onload = function() {
            // Wait a bit for the library to be fully loaded
            setTimeout(() => {
                exportToExcel();
            }, 100);
        };
        script.onerror = function() {
            alert('Gagal memuat library Excel. Silakan coba lagi.');
        };
        document.head.appendChild(script);
    } else {
        exportToExcel();
    }

    function exportToExcel() {
        const tableRows = document.querySelectorAll('#detailTableBody tr:not([style*="display: none"])');
        const data = [];

        // Add header row
        data.push(['NO', 'TANGGAL', 'NAMA SEKOLAH', 'PENANGGUNG JAWAB', 'PRODI', 'ALUMNI']);

        tableRows.forEach((row, index) => {
            if (row.cells.length > 0) {
                const cells = row.cells;
                if (cells[0].textContent !== 'Loading...' &&
                    cells[0].textContent !== 'Tidak Ada Kegiatan' &&
                    cells[0].textContent !== 'Terjadi Kesalahan') {
                    const rowData = [];
                    // Extract text content from each cell
                    for (let i = 0; i < cells.length; i++) {
                        // Get text content, removing any HTML tags
                        const cellText = cells[i].innerText || cells[i].textContent || '';
                        rowData.push(cellText.trim());
                    }
                    data.push(rowData);
                }
            }
        });

        // Create worksheet
        const ws = XLSX.utils.aoa_to_sheet(data);

        // Create workbook
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Detail Roadshow');

        // Generate filename with proper format
        const dateStr = new Date().toISOString().slice(0, 10);
        const filename = `detail_roadshow_${window.currentKabupaten}_${window.currentProvinsi}_${dateStr}.xlsx`;

        // Write file
        try {
            XLSX.writeFile(wb, filename);
        } catch (error) {
            console.error('Error exporting to Excel:', error);
            alert('Terjadi kesalahan saat mengekspor ke Excel: ' + error.message);
        }
    }
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
@endsection