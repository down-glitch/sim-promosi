@extends('layouts.app')

@section('title', 'Laporan Analitik - SIM-PROMOSI')
@section('page-title', 'Laporan Analitik')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient text-white py-3 px-4" style="background: linear-gradient(135deg, #276A2B 0%, #38a169 100%);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div class="flex-1">
                    <h4 class="mb-1 fw-bold"><i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan Kegiatan</h4>
                    <p class="mb-0 opacity-75">Filter dan ekspor data kegiatan roadshow</p>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('analytics.report') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label fw-semibold">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('analytics.export-report', request()->all()) }}" class="btn btn-success">
                            <i class="bi bi-download me-1"></i> Ekspor Excel
                        </a>
                    </div>
                </div>
            </form>

            <!-- Results Info -->
            <div class="mb-4">
                <p class="mb-0">
                    Menampilkan <strong>{{ $kegiatan->count() }}</strong> dari total kegiatan
                    @if($startDate && $endDate)
                        untuk periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    @elseif($startDate)
                        mulai dari {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                    @elseif($endDate)
                        hingga {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    @endif
                </p>
            </div>

            <!-- Results Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th class="text-center" width="60px">No</th>
                            <th>Nama Kegiatan</th>
                            <th class="text-center" width="120px">Tanggal Mulai</th>
                            <th class="text-center" width="120px">Tanggal Selesai</th>
                            <th>Provinsi</th>
                            <th>Kabupaten/Kota</th>
                            <th>Nama Sekolah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kegiatan as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->Promotion_Name }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($item->Event_Start_Date)->format('d M Y') }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($item->Event_End_Date)->format('d M Y') }}</td>
                            <td>{{ $item->province }}</td>
                            <td>{{ $item->city }}</td>
                            <td>{{ $item->school_name }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle fs-1 mb-3"></i>
                                <p class="mb-0">Tidak ada data kegiatan yang ditemukan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection