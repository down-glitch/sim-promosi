# Layout dan Sidebar SIM-PROMOSI

## Deskripsi
Sidebar telah dipindahkan ke layout utama (`app.blade.php`) sehingga dapat digunakan di semua halaman tanpa perlu menyalin kode sidebar ke setiap file.

## Struktur File
- `resources/views/layouts/app.blade.php` - Layout utama aplikasi
- `resources/views/components/sidebar.blade.php` - Komponen sidebar yang dapat digunakan di layout
- `resources/views/dashboard.blade.php` - Contoh halaman dashboard menggunakan layout baru
- `resources/views/welcome.blade.php` - Contoh halaman welcome menggunakan layout baru
- `resources/views/master-data/`, `resources/views/activities/`, dll. - Direktori untuk halaman-halaman lain

## Cara Menggunakan Layout Baru
Untuk membuat halaman baru yang menggunakan layout utama:

```blade
@extends('layouts.app')

@section('title', 'Judul Halaman - SIM-PROMOSI')
@section('page-title', 'Judul Halaman')

@section('content')
    <!-- Konten halaman disini -->
    <div class="content-area">
        <div class="card">
            <div class="card-header">Header Kartu</div>
            <div class="card-body">
                <p>Konten kartu disini.</p>
            </div>
        </div>
    </div>
@endsection
```

## Fitur Sidebar
- Sidebar tetap muncul di semua halaman
- Dapat diciutkan/melebar dengan tombol toggle
- Mendukung menu dropdown
- Responsif untuk perangkat mobile
- Menyesuaikan route aktif dengan highlight otomatis

## Route yang Tersedia
Semua route sudah terdaftar di `routes/web.php` dan siap digunakan.