@extends('layouts.app')

@section('title', 'Contoh Halaman - SIM-PROMOSI')
@section('page-title', 'Contoh Halaman')

@section('content')
        <!-- Content Area -->
        <div class="content-area">
            <div class="page-header">
                <h2 class="page-title-large">Contoh Halaman</h2>
                <p class="page-description">Ini adalah contoh penggunaan layout utama SIM-PROMOSI</p>
            </div>

            <div class="card">
                <div class="card-header">Informasi</div>
                <div class="card-body">
                    <p>Sidebar telah dipindahkan ke layout utama dan dapat digunakan di semua halaman.</p>
                    <p>Gunakan <code>@extends('layouts.app')</code> untuk menggunakan layout ini.</p>
                    <p>Tentukan judul halaman dengan <code>@section('title', 'Judul Halaman')</code>.</p>
                    <p>Tentukan judul halaman di header dengan <code>@section('page-title', 'Judul Header')</code>.</p>
                    <p>Tambahkan konten halaman di dalam <code>@section('content')</code>.</p>
                </div>
            </div>
        </div>
@endsection