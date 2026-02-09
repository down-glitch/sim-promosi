@extends('layouts.app')

@section('title', 'Impor Data Kegiatan - SIM-PROMOSI')
@section('page-title', 'Impor Data Kegiatan')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient text-white py-3 px-4" style="background: linear-gradient(135deg, #276A2B 0%, #38a169 100%);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div class="flex-1">
                    <h4 class="mb-1 fw-bold"><i class="bi bi-upload me-2"></i>Impor Data Kegiatan</h4>
                    <p class="mb-0 opacity-75">Unggah file Excel atau CSV untuk menambahkan data kegiatan secara massal</p>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-8 mx-auto">
                        <div class="card border-0 bg-light-subtle">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Petunjuk Impor Data</h5>
                                
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item bg-transparent border-0 ps-0 py-1">
                                        <i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>
                                        Format file yang didukung: Excel (.xlsx, .xls) atau CSV (.csv)
                                    </li>
                                    <li class="list-group-item bg-transparent border-0 ps-0 py-1">
                                        <i class="bi bi-file-text text-info me-2"></i>
                                        Ukuran maksimal file: 10 MB
                                    </li>
                                    <li class="list-group-item bg-transparent border-0 ps-0 py-1">
                                        <i class="bi bi-layout-wtf text-warning me-2"></i>
                                        Struktur kolom: Provinsi, Kabupaten/Kota, Nama Sekolah, Alamat Sekolah, Nama Kegiatan, Tanggal Mulai, Tanggal Selesai, Penanggung Jawab, Prodi 1, Prodi 2, Prodi 3, Jumlah Alumni
                                    </li>
                                    <li class="list-group-item bg-transparent border-0 ps-0 py-1">
                                        <i class="bi bi-calendar-check text-success me-2"></i>
                                        Format tanggal yang diterima: YYYY-MM-DD, DD/MM/YYYY, atau DD-MM-YYYY
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="mb-4">
                            <label for="file" class="form-label fw-semibold">Pilih File</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx,.xls,.csv">
                            
                            @error('file')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('activities.roadshow') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-upload me-1"></i> Impor Data
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            
            @if($errors->has('import_errors'))
            <div class="row mt-4">
                <div class="col-md-8 mx-auto">
                    <div class="alert alert-danger">
                        <h5 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Error Impor Data:</h5>
                        <ul class="mb-0">
                            @foreach($errors->get('import_errors') as $errorList)
                                @foreach($errorList as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection