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
                                <select class="form-select" id="provinsiSelect" required>
                                    <option value="">Pilih Provinsi...</option>
                                </select>
                                <input type="hidden" name="provinsi" id="provinsi" value="{{ old('provinsi') }}">
                                <div id="provinsiFeedback" class="invalid-feedback"></div>
                            </div>

                            <!-- Kabupaten -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                <select class="form-select" id="kabupatenSelect" name="kabupaten" disabled required>
                                    <option value="">Pilih Kabupaten...</option>
                                </select>
                                <div id="kabupatenFeedback" class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Sekolah -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Sekolah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sekolahInput"
                                       placeholder="Ketik nama sekolah untuk mencari..." autocomplete="off">
                                <input type="hidden" name="sekolah_id" id="sekolahId">
                                <input type="hidden" name="sekolah_nama" id="sekolahNama">
                                <small class="form-text text-muted">Cari sekolah menggunakan nama lengkap atau sebagian nama</small>
                                <div id="sekolahList" class="list-group position-absolute rounded shadow" style="z-index: 1000; width: 100%; max-height: 300px; overflow-y: auto; display: none; background: white; border: 1px solid #ddd;"></div>
                                <div id="sekolahFeedback" class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: DATA KEGIATAN -->
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

                        <!-- Total Pendaftar -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Pendaftar (selama 3 tahun) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="total_pendaftar"
                                       placeholder="Jumlah total pendaftar" value="{{ old('total_pendaftar', 0) }}" min="0" required>
                                <small class="form-text text-muted">Jumlah total pendaftar selama 3 tahun terakhir</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Conversion Rate (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="conversion_rate"
                                       placeholder="Estimasi conversion rate" value="{{ old('conversion_rate', 0) }}"
                                       min="0" max="100" step="0.01" required>
                                <small class="form-text text-muted">Estimasi persentase konversi</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: DETAIL KEGIATAN -->
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
                        <button type="submit" class="btn btn-success btn-lg flex-grow-1" id="submitBtn">
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
    const provinsiSelect = document.getElementById('provinsiSelect');
    const kabupatenSelect = document.getElementById('kabupatenSelect');
    const sekolahInput = document.getElementById('sekolahInput');
    const sekolahList = document.getElementById('sekolahList');
    const sekolahId = document.getElementById('sekolahId');
    const sekolahNama = document.getElementById('sekolahNama');
    const submitBtn = document.getElementById('submitBtn');

    // Load daftar provinsi
    fetch('{{ route("api.sekolah.provinsi") }}')
        .then(response => response.json())
        .then(data => {
            provinsiSelect.innerHTML = '<option value="">Pilih Provinsi...</option>';
            data.forEach(prov => {
                const option = document.createElement('option');
                option.value = prov.text;
                option.textContent = prov.text;
                provinsiSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading provinces:', error);
        });

    // Event listener untuk provinsi
    provinsiSelect.addEventListener('change', function() {
        const provinsi = this.value;
        kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten...</option>';
        
        if (provinsi) {
            kabupatenSelect.disabled = false;
            
            // Load daftar kabupaten
            fetch(`{{ route("api.sekolah.kabupaten", ":provinsi") }}`.replace(':provinsi', encodeURIComponent(provinsi)))
                .then(response => response.json())
                .then(data => {
                    kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten...</option>';
                    data.forEach(kab => {
                        const option = document.createElement('option');
                        option.value = kab.text;
                        option.textContent = kab.text;
                        kabupatenSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading cities:', error);
                });
        } else {
            kabupatenSelect.disabled = true;
        }
    });

    // Event listener untuk kabupaten
    kabupatenSelect.addEventListener('change', function() {
        sekolahInput.value = '';
        sekolahId.value = '';
        sekolahNama.value = '';
        sekolahList.style.display = 'none';
    });

    // Autocomplete sekolah
    let sekolahAutocompleteTimeout;
    sekolahInput.addEventListener('input', function() {
        clearTimeout(sekolahAutocompleteTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            sekolahList.style.display = 'none';
            return;
        }

        sekolahAutocompleteTimeout = setTimeout(() => {
            const provinsi = provinsiSelect.value;
            const kabupaten = kabupatenSelect.value;
            
            if (!provinsi || !kabupaten) {
                alert('Pilih provinsi dan kabupaten terlebih dahulu');
                return;
            }
            
            // Cari sekolah menggunakan API
            fetch(`{{ route("api.sekolah.autocomplete") }}?q=${encodeURIComponent(query)}&limit=10`)
                .then(response => response.json())
                .then(data => {
                    sekolahList.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(school => {
                            const item = document.createElement('div');
                            item.className = 'list-group-item cursor-pointer border-bottom';
                            item.innerHTML = `
                                <div><strong>${school.text}</strong></div>
                                <small class="text-muted">${school.alamat || 'Alamat tidak tersedia'}, ${school.kabupaten}, ${school.provinsi}</small>
                            `;
                            item.onclick = () => selectSekolah(school);
                            sekolahList.appendChild(item);
                        });
                        sekolahList.style.display = 'block';
                    } else {
                        sekolahList.innerHTML = '<div class="list-group-item text-center text-muted">Sekolah tidak ditemukan</div>';
                        sekolahList.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error searching schools:', error);
                    sekolahList.innerHTML = '<div class="list-group-item text-center text-danger">Error memuat data</div>';
                    sekolahList.style.display = 'block';
                });
        }, 300);
    });

    // Close sekolah list when clicking outside
    document.addEventListener('click', function(event) {
        if (event.target !== sekolahInput && !sekolahList.contains(event.target)) {
            sekolahList.style.display = 'none';
        }
    });

    function selectSekolah(school) {
        sekolahInput.value = school.text;
        sekolahId.value = school.id;
        sekolahNama.value = school.text;
        sekolahList.style.display = 'none';
        
        // Validasi bahwa sekolah telah dipilih
        sekolahInput.classList.remove('is-invalid');
        document.getElementById('sekolahFeedback').textContent = '';
    }

    // Form validation
    document.getElementById('formRoadshow').addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validasi provinsi
        if (!provinsiSelect.value) {
            provinsiSelect.classList.add('is-invalid');
            document.getElementById('provinsiFeedback').textContent = 'Provinsi harus dipilih';
            isValid = false;
        } else {
            provinsiSelect.classList.remove('is-invalid');
            document.getElementById('provinsiFeedback').textContent = '';
        }
        
        // Validasi kabupaten
        if (!kabupatenSelect.value) {
            kabupatenSelect.classList.add('is-invalid');
            document.getElementById('kabupatenFeedback').textContent = 'Kabupaten harus dipilih';
            isValid = false;
        } else {
            kabupatenSelect.classList.remove('is-invalid');
            document.getElementById('kabupatenFeedback').textContent = '';
        }
        
        // Validasi sekolah
        if (!sekolahId.value) {
            sekolahInput.classList.add('is-invalid');
            document.getElementById('sekolahFeedback').textContent = 'Sekolah harus dipilih';
            isValid = false;
        } else {
            sekolahInput.classList.remove('is-invalid');
            document.getElementById('sekolahFeedback').textContent = '';
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        // Tampilkan loading pada tombol submit
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...';
        submitBtn.disabled = true;
    });
});
</script>
@endsection