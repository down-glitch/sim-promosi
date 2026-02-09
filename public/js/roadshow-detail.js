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
    fetch(`/activities/roadshow/detail/${encodeURIComponent(provinsi)}/${encodeURIComponent(kabupaten)}?${params.toString()}`, {
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