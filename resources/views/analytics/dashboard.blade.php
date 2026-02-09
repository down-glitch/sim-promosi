@extends('layouts.app')

@section('title', 'Dashboard Analitik - SIM-PROMOSI')
@section('page-title', 'Dashboard Analitik')

@section('content')
<div class="container-fluid py-4">
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #276A2B 0%, #38a169 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $totalKegiatan }}</h3>
                            <p class="mb-0 opacity-75">Total Kegiatan</p>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-calendar-check fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-gradient text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $totalSekolah }}</h3>
                            <p class="mb-0 opacity-75">Sekolah Terlibat</p>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-building fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-gradient text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $totalProvinsi }}</h3>
                            <p class="mb-0 opacity-75">Provinsi Terjangkau</p>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-map fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-gradient text-white border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #7c2d12 0%, #ea580c 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">100%</h3>
                            <p class="mb-0 opacity-75">Target Tercapai</p>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-graph-up fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3 px-4 border-bottom-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart me-2"></i>Distribusi Kegiatan per Provinsi</h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="chartProvinsi" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3 px-4 border-bottom-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-list-ul me-2"></i>Statistik Utama</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Kegiatan Bulan Ini</span>
                        <span class="fw-bold">24</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Sekolah Baru</span>
                        <span class="fw-bold">8</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Partisipasi</span>
                        <span class="fw-bold">1,240 orang</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Konversi</span>
                        <span class="fw-bold">15.6%</span>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Efisiensi Biaya</span>
                        <span class="fw-bold text-success">-12%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart distribusi kegiatan per provinsi
const ctx = document.getElementById('chartProvinsi').getContext('2d');
const chartProvinsi = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [
            @foreach($dataPerProvinsi as $item)
                '{{ $item->PROVINCE }}',
            @endforeach
        ],
        datasets: [{
            label: 'Jumlah Kegiatan',
            data: [
                @foreach($dataPerProvinsi as $item)
                    {{ $item->jumlah_kegiatan }},
                @endforeach
            ],
            backgroundColor: '#38a169',
            borderColor: '#276A2B',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${context.dataset.label}: ${context.parsed.y} kegiatan`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endsection