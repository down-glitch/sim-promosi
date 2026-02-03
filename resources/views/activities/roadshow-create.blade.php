@extends('layouts.app')

@section('title', 'Tambah Data Roadshow - SIM-PROMOSI')
@section('page-title', 'Tambah Data Roadshow')

@section('content')
<div class="container-fluid">
    <!-- Alert Section -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Gagal menyimpan data!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-10 offset-md-1">
            <form action="{{ route('activities.roadshow.store') }}" method="POST" id="formRoadshow">
                @csrf

                <!-- SECTION 1: LOKASI ROADSHOW -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-geo-alt"></i> Lokasi Roadshow</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Provinsi -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="provinsiInput"
                                           placeholder="Ketik untuk mencari provinsi..." autocomplete="off" required>
                                    <input type="hidden" name="provinsi" id="provinsi" value="{{ old('provinsi') }}">
                                </div>
                                <small class="form-text text-muted">Ketik minimal 2 karakter untuk mencari provinsi</small>
                                <div id="provinsiList" class="list-group position-absolute rounded shadow" style="z-index: 1000; width: 45%; max-height: 300px; overflow-y: auto; display: none; background: white; border: 1px solid #ddd;"></div>
                            </div>

                            <!-- Kabupaten -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="kabupaten" name="kabupaten"
                                           placeholder="Pilih provinsi terlebih dahulu" disabled required>
                                    <input type="hidden" name="kabupaten_hidden" id="kabupaten_hidden">
                                </div>
                                <small class="form-text text-muted">Pilih kabupaten/kota setelah memilih provinsi</small>
                                <div id="kabupatenList" class="list-group position-absolute rounded shadow" style="z-index: 1000; width: 45%; max-height: 300px; overflow-y: auto; display: none; background: white; border: 1px solid #ddd;"></div>
                            </div>
                        </div>

                        <!-- Sekolah -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Sekolah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sekolah"
                                       placeholder="Pilih kabupaten terlebih dahulu untuk menampilkan daftar sekolah" disabled required>
                                <small class="form-text text-muted">Pilih sekolah dari daftar atau masukkan nama sekolah secara manual</small>
                                <input type="hidden" name="sekolah_id" id="sekolah_id">
                                <input type="hidden" name="sekolah_nama" id="sekolah_nama">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: RIWAYAT SEKOLAH -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Kegiatan Sekolah: <span id="sekolahNameDisplay" class="fw-bold fst-italic">-</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover" id="historyTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80px">Tanggal</th>
                                        <th>Nama Kegiatan</th>
                                        <th width="120px">Alumni Masuk UMY</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Pilih sekolah terlebih dahulu untuk melihat riwayat kegiatan</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: DATA KEGIATAN -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-calendar-event"></i> Data Kegiatan</h6>
                    </div>
                    <div class="card-body">
                        <!-- Nama Kegiatan -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_kegiatan" 
                                       placeholder="Contoh: Roadshow Promosi 2026" value="{{ old('nama_kegiatan') }}" required>
                            </div>
                        </div>

                        <!-- Tanggal Mulai dan Selesai -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_mulai"
                                       value="{{ old('tanggal_mulai') }}" min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_selesai"
                                       value="{{ old('tanggal_selesai') }}" min="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <!-- Penanggungjawab dan Top 3 Prodi -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Penanggungjawab <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="penanggungjawab"
                                       placeholder="Nama penanggungjawab kegiatan" value="{{ old('penanggungjawab') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Top 3 Prodi <span class="text-danger">*</span></label>
                                <select class="form-select" name="top_3_prodi[]" id="top3ProdiSelect" multiple required>
                                    @if(old('top_3_prodi'))
                                        @foreach(old('top_3_prodi') as $prodi)
                                            <option value="{{ $prodi }}" selected>{{ $prodi }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <small class="form-text text-muted">Pilih hingga 3 program studi (gunakan Ctrl/Cmd untuk memilih lebih dari satu)</small>
                            </div>
                        </div>

                        <!-- Total Pendaftar dan Conversion Rate -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Pendaftar (selama 3 tahun) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control bg-light" name="total_pendaftar" id="totalPendaftar"
                                           placeholder="Akan terisi otomatis saat memilih sekolah" value="{{ old('total_pendaftar', 0) }}" min="0" required>
                                    <span class="input-group-text" id="totalBadge" style="display: none; background-color: #d1ecf1;">
                                        <span class="badge bg-info">Dari History</span>
                                    </span>
                                </div>
                                <small class="form-text text-muted">Otomatis terisi dari history kegiatan sekolah (3 tahun terakhir)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Conversion Rate (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="conversion_rate" id="conversionRate"
                                       placeholder="Estimasi akan muncul dari data history" value="{{ old('conversion_rate', 0) }}" 
                                       min="0" max="100" step="0.01" required>
                                <small class="form-text text-muted">Estimasi dari history atau isi manual</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: DETAIL KEGIATAN -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-file-earmark-text"></i> Detail Kegiatan</h6>
                    </div>
                    <div class="card-body">
                        <!-- Jenis Kegiatan -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Jenis Kegiatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="jenis_kegiatan" 
                                       placeholder="Contoh: Roadshow, Presentasi, Booth, dll" 
                                       value="{{ old('jenis_kegiatan') }}" required>
                            </div>
                        </div>

                        <!-- Program Studi -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Program Studi yang Ditampilkan <span class="text-danger">*</span></label>
                                <select class="form-select" name="program_studi" id="programStudiSelect" required>
                                    @if(old('program_studi'))
                                        <option value="{{ old('program_studi') }}" selected>{{ old('program_studi') }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <!-- PIC Roadshow -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">PIC Roadshow <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="pic_roadshow" 
                                       placeholder="Person in Charge dari institusi" 
                                       value="{{ old('pic_roadshow') }}" required>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Catatan Tambahan</label>
                                <textarea class="form-control" name="catatan" rows="4" 
                                          placeholder="Informasi tambahan atau catatan penting tentang kegiatan">{{ old('catatan') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-md-12 d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-lg flex-grow-1">
                            <i class="bi bi-check-circle"></i> Simpan Data
                        </button>
                        <a href="{{ route('activities.roadshow') }}" class="btn btn-secondary btn-lg">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .list-group {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    
    .list-group-item {
        cursor: pointer;
        border: 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .list-group-item:last-child {
        border-bottom: 0;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    
    .list-group-item.active {
        background-color: #276A2B;
        color: white;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinsiInput = document.getElementById('provinsiInput');
    const provinsiHidden = document.getElementById('provinsi');
    const provinsiList = document.getElementById('provinsiList');
    const kabupatenInput = document.getElementById('kabupaten');
    const sekolahInput = document.getElementById('sekolah');
    const sekolahIdInput = document.getElementById('sekolah_id');
    const sekolahNamaInput = document.getElementById('sekolah_nama');
    const historyTable = document.getElementById('historyTable').getElementsByTagName('tbody')[0];

    // ========== PROVINSI AUTOCOMPLETE ==========
    let autocompleteTimeout;
    provinsiInput.addEventListener('input', function() {
        clearTimeout(autocompleteTimeout);
        const value = this.value.trim();

        if (value.length < 2) {
            provinsiList.style.display = 'none';
            return;
        }

        autocompleteTimeout = setTimeout(() => {
            fetch(`/api/autocomplete-provinsi?q=${encodeURIComponent(value)}`)
                .then(response => response.json())
                .then(data => {
                    provinsiList.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(prov => {
                            const item = document.createElement('div');
                            item.className = 'list-group-item cursor-pointer border-bottom';
                            item.textContent = prov;
                            item.onclick = () => selectProvinsi(prov);
                            provinsiList.appendChild(item);
                        });
                        provinsiList.style.display = 'block';
                    } else {
                        provinsiList.innerHTML = '<div class="list-group-item text-center text-muted">Provinsi tidak ditemukan</div>';
                        provinsiList.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error autocomplete:', error);
                    provinsiList.innerHTML = '<div class="list-group-item text-center text-danger">Error memuat data</div>';
                    provinsiList.style.display = 'block';
                });
        }, 300);
    });

    // When user presses Enter in provinsi, accept current value
    provinsiInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const value = this.value.trim();
            if (value) {
                // If there's only one suggestion, select it automatically
                const suggestions = provinsiList.querySelectorAll('.list-group-item:not(.text-center)');
                if (suggestions.length === 1) {
                    selectProvinsi(suggestions[0].textContent);
                } else {
                    selectProvinsi(value);
                }
            }
        }
    });

    // When user leaves provinsi field, accept the value if any
    provinsiInput.addEventListener('blur', function() {
        const value = this.value.trim();
        if (value && value !== provinsiHidden.value) {
            selectProvinsi(value);
        }
    });

    function selectProvinsi(prov) {
        provinsiInput.value = prov;
        provinsiHidden.value = prov;
        provinsiList.style.display = 'none';
        kabupatenInput.disabled = false;
        kabupatenInput.value = '';
        document.getElementById('kabupaten_hidden').value = '';
        sekolahInput.value = '';
        sekolahInput.disabled = true;
        sekolahNameDisplay.textContent = '-';
        historyTable.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Pilih sekolah terlebih dahulu untuk melihat riwayat</td></tr>';

        // Load kabupaten list
        fetch(`/api/get-kabupaten/${encodeURIComponent(prov)}`)
            .then(response => response.json())
            .then(data => {
                window.kabupatenListCache = data; // Store in global variable for search
                console.log('Kabupaten data:', data);
            })
            .catch(error => {
                console.error('Error loading kabupaten:', error);
                window.kabupatenListCache = []; // Initialize as empty array
            });
    }

    // ========== KABUPATEN INPUT (dengan autocomplete) ==========
    let kabupatenAutocompleteTimeout;
    kabupatenInput.addEventListener('input', function() {
        clearTimeout(kabupatenAutocompleteTimeout);
        const value = this.value.trim();
        const provinsi = provinsiHidden.value;

        if (!provinsi) {
            this.disabled = true;
            return;
        }

        // Reset sekolah section when kabupaten changes
        sekolahInput.disabled = true;
        sekolahInput.value = '';
        sekolahIdInput.value = '';
        sekolahNamaInput.value = '';
        sekolahNameDisplay.textContent = '-';
        historyTable.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Pilih sekolah terlebih dahulu untuk melihat riwayat</td></tr>';

        if (value.length < 2) {
            document.getElementById('kabupatenList').style.display = 'none';
            return;
        }

        kabupatenAutocompleteTimeout = setTimeout(() => {
            const kabupatenList = window.kabupatenListCache || [];
            const filtered = kabupatenList.filter(kab =>
                kab.toLowerCase().includes(value.toLowerCase())
            );

            const kabupatenListElement = document.getElementById('kabupatenList');
            kabupatenListElement.innerHTML = '';

            if (filtered.length > 0) {
                filtered.forEach(kab => {
                    const item = document.createElement('div');
                    item.className = 'list-group-item cursor-pointer border-bottom';
                    item.textContent = kab;
                    item.onclick = () => selectKabupaten(kab);
                    kabupatenListElement.appendChild(item);
                });
                kabupatenListElement.style.display = 'block';
            } else {
                kabupatenListElement.innerHTML = '<div class="list-group-item text-center text-muted">Kabupaten tidak ditemukan</div>';
                kabupatenListElement.style.display = 'block';
            }
        }, 300);
    });

    // When user presses Enter in kabupaten, accept current value
    kabupatenInput.addEventListener('keydown', function(e) {
        const kabupatenListElement = document.getElementById('kabupatenList');

        if (e.key === 'Enter') {
            e.preventDefault();
            const value = this.value.trim();
            if (value) {
                // If there's only one suggestion, select it automatically
                const suggestions = kabupatenListElement.querySelectorAll('.list-group-item:not(.text-center)');
                if (suggestions.length === 1) {
                    selectKabupaten(suggestions[0].textContent);
                } else {
                    // Check if the entered value exists in the list
                    const kabupatenList = window.kabupatenListCache || [];
                    const match = kabupatenList.find(kab =>
                        kab.toLowerCase() === value.toLowerCase()
                    );

                    if (match) {
                        selectKabupaten(match);
                    } else {
                        // Allow manual entry if not found in list
                        selectKabupaten(value);
                    }
                }
            }
        } else if (e.key === 'Escape') {
            // Hide the dropdown when pressing Escape
            document.getElementById('kabupatenList').style.display = 'none';
        }
    });

    // Close kabupaten list when clicking outside
    document.addEventListener('click', function(event) {
        if (event.target !== kabupatenInput && !document.getElementById('kabupatenList').contains(event.target)) {
            document.getElementById('kabupatenList').style.display = 'none';
        }
    });

    function selectKabupaten(kabupaten) {
        kabupatenInput.value = kabupaten;
        document.getElementById('kabupaten_hidden').value = kabupaten;
        document.getElementById('kabupatenList').style.display = 'none';

        sekolahInput.disabled = false;
        sekolahInput.value = '';
        sekolahIdInput.value = '';
        sekolahNamaInput.value = '';
        sekolahNameDisplay.textContent = '-';
        historyTable.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Pilih sekolah terlebih dahulu untuk melihat riwayat</td></tr>';

        sekolahInput.placeholder = "Ketik untuk mencari sekolah...";
    }

    // ========== SEKOLAH INPUT (dengan dropdown dari API) ==========
    let sekolahAutocompleteTimeout;
    let sekolahListCache = [];

    sekolahInput.addEventListener('focus', function() {
        const provinsi = provinsiHidden.value;
        const kabupaten = document.getElementById('kabupaten_hidden').value;

        if (!provinsi || !kabupaten) {
            // Show warning if kabupaten is not selected yet
            this.placeholder = "Pilih kabupaten terlebih dahulu";
            return;
        }

        // Fetch sekolah list dari API
        fetch(`/api/get-sekolah/${encodeURIComponent(provinsi)}/${encodeURIComponent(kabupaten)}`)
            .then(response => response.json())
            .then(data => {
                sekolahListCache = data;
                console.log('Sekolah list:', data);
            })
            .catch(error => console.error('Error loading sekolah:', error));
    });

    sekolahInput.addEventListener('input', function() {
        clearTimeout(sekolahAutocompleteTimeout);
        const provinsi = provinsiHidden.value;
        const kabupaten = document.getElementById('kabupaten_hidden').value;
        const search = this.value.trim();

        if (!provinsi || !kabupaten) {
            this.placeholder = "Pilih kabupaten terlebih dahulu";
            sekolahInput.disabled = true;
            return;
        }

        if (search.length === 0) {
            return;
        }

        // Filter dari cache
        sekolahAutocompleteTimeout = setTimeout(() => {
            const filtered = sekolahListCache.filter(s =>
                s.name.toLowerCase().includes(search.toLowerCase()) ||
                s.text.toLowerCase().includes(search.toLowerCase())
            );

            // Update placeholder to show how many schools match
            if (filtered.length > 0) {
                sekolahInput.placeholder = `Ditemukan ${filtered.length} sekolah...`;
            } else if (search.length > 2) {
                sekolahInput.placeholder = "Sekolah tidak ditemukan, masukkan nama sekolah secara manual";
            }

        }, 300);
    });

    // When user presses Enter in sekolah, accept current value
    sekolahInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const value = this.value.trim();
            if (value) {
                // Cari di cache apakah ada exact match
                const match = sekolahListCache.find(s =>
                    s.name.toLowerCase() === value.toLowerCase() ||
                    s.text.toLowerCase() === value.toLowerCase() ||
                    s.id === value
                );

                if (match) {
                    selectSekolah(match.name, match.id);
                } else {
                    // Manual entry - allow user to input custom school name
                    selectSekolah(value, value);
                }
            }
        }
    });

    function selectSekolah(sekolahName, sekolahCode) {
        sekolahInput.value = sekolahName;
        // Use the code if available, otherwise use the name
        const sekolahId = sekolahCode || sekolahName;
        sekolahIdInput.value = sekolahId;
        sekolahNamaInput.value = sekolahName;
        sekolahNameDisplay.textContent = sekolahName; // Update display name

        // Always try to load history, regardless of whether it's from the API list or manually entered
        loadHistory(sekolahId);
    }

    function loadHistory(sekolahId) {
        const totalPendaftarInput = document.getElementById('totalPendaftar');
        const conversionRateInput = document.getElementById('conversionRate');
        const totalBadge = document.getElementById('totalBadge');

        console.log('Loading history for school ID:', sekolahId);

        historyTable.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat riwayat kegiatan...</td></tr>';

        fetch(`/api/get-school-history/${encodeURIComponent(sekolahId)}`)
            .then(response => {
                console.log('API response status:', response.status);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                console.log('Received history data:', data);

                if (!Array.isArray(data)) throw new Error('Response is not an array');

                if (data.length > 0) {
                    const uniqueEvents = {};
                    const displayData = [];

                    data.forEach(item => {
                        if (!uniqueEvents[item.Input_Data_Id]) {
                            uniqueEvents[item.Input_Data_Id] = true;
                            displayData.push(item);
                        }
                    });

                    historyTable.innerHTML = displayData.map((item) => `
                        <tr>
                            <td>${new Date(item.Event_Start_Date).toLocaleDateString('id-ID', {year: 'numeric', month: 'short', day: 'numeric'})}</td>
                            <td>${item.Promotion_Name}</td>
                            <td class="text-center"><span class="badge bg-success">${item.alumni_count || 0} orang</span></td>
                        </tr>
                    `).join('');

                    let totalAlumni = 0;
                    const threeYearsAgo = new Date();
                    threeYearsAgo.setFullYear(threeYearsAgo.getFullYear() - 3);

                    displayData.forEach(item => {
                        const eventDate = new Date(item.Event_Start_Date);
                        if (eventDate >= threeYearsAgo) {
                            totalAlumni += (item.alumni_count || 0);
                        }
                    });

                    totalPendaftarInput.value = totalAlumni;
                    totalPendaftarInput.dispatchEvent(new Event('change', { bubbles: true }));
                    totalBadge.style.display = 'block';

                    if (totalAlumni > 0) {
                        const conversionRate = Math.min(Math.round((totalAlumni / (totalAlumni * 2.5)) * 100 * 100) / 100, 100);
                        conversionRateInput.value = conversionRate || 0;
                        conversionRateInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                } else {
                    historyTable.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada riwayat kegiatan untuk sekolah ini</td></tr>';
                    document.getElementById('totalPendaftar').value = 0;
                    document.getElementById('conversionRate').value = 0;
                    totalBadge.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error loading history:', error);
                historyTable.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Gagal memuat riwayat kegiatan</td></tr>';
                document.getElementById('totalPendaftar').value = 0;
                document.getElementById('conversionRate').value = 0;
            });
    }

    // Close provinsi list ketika klik di luar
    document.addEventListener('click', function(event) {
        if (event.target !== provinsiInput && !provinsiList.contains(event.target)) {
            provinsiList.style.display = 'none';
        }
    });

    // Form validation
    document.getElementById('formRoadshow').addEventListener('submit', function(e) {
        if (!provinsiHidden.value) {
            e.preventDefault();
            alert('Silakan isi provinsi terlebih dahulu');
            return false;
        }
        if (!document.getElementById('kabupaten_hidden').value) {
            e.preventDefault();
            alert('Silakan isi kabupaten/kota terlebih dahulu');
            return false;
        }
        if (!sekolahInput.value.trim()) {
            e.preventDefault();
            alert('Silakan isi sekolah terlebih dahulu');
            return false;
        }
    });

    // Close provinsi list ketika klik di luar
    document.addEventListener('click', function(event) {
        if (event.target !== provinsiInput && !provinsiList.contains(event.target)) {
            provinsiList.style.display = 'none';
        }
    });

    // Form validation
    document.getElementById('formRoadshow').addEventListener('submit', function(e) {
        if (!provinsiHidden.value) {
            e.preventDefault();
            alert('Silakan isi provinsi terlebih dahulu');
            return false;
        }
        if (!document.getElementById('kabupaten_hidden').value) {
            e.preventDefault();
            alert('Silakan isi kabupaten/kota terlebih dahulu');
            return false;
        }
        if (!sekolahInput.value.trim()) {
            e.preventDefault();
            alert('Silakan isi sekolah terlebih dahulu');
            return false;
        }
    });

    // Initialize Select2 elements after DOM is loaded
    $(document).ready(function() {
        // Initialize Select2 for Top 3 Prodi
        $('#top3ProdiSelect').select2({
            placeholder: "Pilih hingga 3 program studi",
            allowClear: true,
            maximumSelectionLength: 3, // Limit to 3 selections
            ajax: {
                url: '/api/get-departments',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(dept) {
                            return {
                                id: dept.name,
                                text: dept.name
                            };
                        })
                    };
                },
                cache: true
            }
        });

        // Initialize Select2 for Program Studi
        $('#programStudiSelect').select2({
            placeholder: "Pilih program studi",
            allowClear: true,
            ajax: {
                url: '/api/get-departments',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(dept) {
                            return {
                                id: dept.name,
                                text: dept.name
                            };
                        })
                    };
                },
                cache: true
            }
        });
    });

    // ========== DATE VALIDATION ==========
    const tanggalMulaiInput = document.querySelector('input[name="tanggal_mulai"]');
    const tanggalSelesaiInput = document.querySelector('input[name="tanggal_selesai"]');

    // Set default min date for end date based on start date
    if (tanggalMulaiInput.value) {
        tanggalSelesaiInput.min = tanggalMulaiInput.value;
    }

    // Update min date for end date when start date changes
    tanggalMulaiInput.addEventListener('change', function() {
        tanggalSelesaiInput.min = this.value;

        // If end date is earlier than start date, update it
        if (tanggalSelesaiInput.value && tanggalSelesaiInput.value < this.value) {
            tanggalSelesaiInput.value = this.value;
        }
    });
});
</script>
@endsection
