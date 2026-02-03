@extends('layouts.app')

@section('title', 'Dashboard - SIM-PROMOSI')
@section('page-title', 'Dashboard')

@section('content')
        <!-- Content Area -->
        <div class="content-area">
            <div class="page-header">
                <h2 class="page-title-large">Selamat Datang di SIM-PROMOSI</h2>
                <p class="page-description">Monitor dan kelola aktivitas promosi universitas Anda</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-container">
                <!-- Total Data Tahun Ini -->
                <div class="stat-card">
                    <div class="stat-icon bg-green">
                        <i class="bi bi-calendar-year"></i>
                    </div>
                    <div class="stat-number">{{ $dataTahunIni ?? 0 }}</div>
                    <div class="stat-label">Total Data Tahun Ini</div>
                </div>

                <!-- Data Bulan Ini -->
                <div class="stat-card">
                    <div class="stat-icon bg-blue">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                    <div class="stat-number">{{ $dataBulanIni ?? 0 }}</div>
                    <div class="stat-label">Data Bulan Ini</div>
                </div>

                @forelse($inputTypes as $index => $type)
                    @php
                        $colors = ['bg-green', 'bg-blue', 'bg-orange', 'bg-purple'];
                        $icons = [
                            'bi-megaphone', // Roadshow
                            'bi-ticket-perforated', // Expo
                            'bi-gift', // Sponsorship
                            'bi-buildings', // Wisata Kampus
                            'bi-projector', // Presentasi & AMT
                            'bi-star' // Promosi Lain
                        ];
                        $colorClass = $colors[$index % count($colors)];
                        $iconClass = $icons[$index % count($icons)];
                    @endphp
                    <div class="stat-card">
                        <div class="stat-icon {{ $colorClass }}">
                            <i class="bi {{ $iconClass }}"></i>
                        </div>
                        <div class="stat-number">{{ $type['jumlah_data'] ?? 0 }}</div>
                        <div class="stat-label">{{ $type['Input_Data_Type'] }}</div>
                    </div>
                @empty
                    <div class="stat-card">
                        <div class="stat-icon bg-green">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div class="stat-number">0</div>
                        <div class="stat-label">Tidak Ada Data</div>
                    </div>
                @endforelse
            </div>

            <!-- Charts and Recent Activity -->
            <div class="dashboard-row">
                <div class="card">
                    <div class="card-header">Statistik Promosi Tahun Ini</div>
                    <div class="chart-placeholder" style="height: 300px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px;">
                        <canvas id="monthlyChart" width="400" height="200" data-monthly-data='<?php echo json_encode($dataPerBulan); ?>'></canvas>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Aktivitas Terbaru</div>
                    <ul class="recent-activity">
                        @forelse($recentActivities as $activity)
                        <li class="activity-item">
                            <div class="activity-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">{{ $activity['title'] }}</div>
                                <div class="activity-desc">{{ $activity['description'] }}</div>
                                <div class="activity-time">{{ $activity['time'] }}</div>
                            </div>
                        </li>
                        @empty
                        <li class="activity-item">
                            <div class="activity-icon bg-secondary bg-opacity-10 text-secondary">
                                <i class="bi bi-info-circle"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Tidak Ada Aktivitas</div>
                                <div class="activity-desc">Belum ada aktivitas terbaru</div>
                                <div class="activity-time">-</div>
                            </div>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Script untuk grafik -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('monthlyChart').getContext('2d');

                    // Data dari PHP disisipkan sebagai data attribute
                    const monthlyData = JSON.parse(document.getElementById('monthlyChart').getAttribute('data-monthly-data'));
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                    // Ambil nilai untuk setiap bulan
                    const dataValues = Object.values(monthlyData);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: months,
                            datasets: [{
                                label: 'Jumlah Kegiatan per Bulan',
                                data: dataValues,
                                borderColor: '#276A2B',
                                backgroundColor: 'rgba(39, 106, 43, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
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
                });
            </script>
        </div>
@endsection