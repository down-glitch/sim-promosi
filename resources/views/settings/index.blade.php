@extends('layouts.app')

@section('title', 'Pengaturan - SIM-PROMOSI')
@section('page-title', 'Pengaturan')

@section('content')
<div class="container-fluid py-4">
    <x-ui.card
        title="Pengaturan Sistem"
        subtitle="Atur preferensi dan konfigurasi aplikasi Anda"
        icon="bi bi-gear"
        class="border-0"
    >
        <!-- Alert -->
        @if (session('success'))
            <x-ui.alert type="success" dismissable="true" class="shadow-sm border-0 mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                </div>
            </x-ui.alert>
        @endif

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="settingTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                    <i class="bi bi-person me-2"></i> Profil Akun
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                    <i class="bi bi-shield-lock me-2"></i> Keamanan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="notification-tab" data-bs-toggle="tab" data-bs-target="#notification" type="button" role="tab">
                    <i class="bi bi-bell me-2"></i> Notifikasi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab">
                    <i class="bi bi-hdd-stack me-2"></i> Sistem
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-4">
            <!-- Profile Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <h5 class="mb-4">Informasi Profil</h5>
                <form method="POST" action="{{ route('settings.profile.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', session('user_name', auth()->user()?->name ?? 'Admin')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', auth()->user()?->email ?? 'admin@example.com') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-semibold">Nomor Telepon</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', auth()->user()?->phone ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="department" class="form-label fw-semibold">Departemen/Unit</label>
                                <input type="text" class="form-control" id="department" name="department" value="{{ old('department', auth()->user()?->department ?? '') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label fw-semibold">Bio/Deskripsi</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3">{{ old('bio', auth()->user()?->bio ?? '') }}</textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Tab -->
            <div class="tab-pane fade" id="security" role="tabpanel">
                <h5 class="mb-4">Keamanan Akun</h5>
                <form method="POST" action="{{ route('settings.security.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-semibold">Password Saat Ini</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="new_password" class="form-label fw-semibold">Password Baru</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-shield-lock me-2"></i> Ubah Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notification Tab -->
            <div class="tab-pane fade" id="notification" role="tabpanel">
                <h5 class="mb-4">Preferensi Notifikasi</h5>
                <form method="POST" action="{{ route('settings.notification.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" {{ old('email_notifications', auth()->user()?->email_notifications ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="email_notifications">Aktifkan Notifikasi Email</label>
                                </div>
                                <div class="form-text">Menerima notifikasi penting melalui email</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" {{ old('sms_notifications', auth()->user()?->sms_notifications ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="sms_notifications">Aktifkan Notifikasi SMS</label>
                                </div>
                                <div class="form-text">Menerima notifikasi penting melalui pesan teks</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="push_notifications" name="push_notifications" {{ old('push_notifications', auth()->user()?->push_notifications ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="push_notifications">Aktifkan Notifikasi Push</label>
                                </div>
                                <div class="form-text">Menerima notifikasi langsung di browser</div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-bell me-2"></i> Simpan Preferensi
                        </button>
                    </div>
                </form>
            </div>

            <!-- System Tab -->
            <div class="tab-pane fade" id="system" role="tabpanel">
                <h5 class="mb-4">Pengaturan Sistem</h5>
                <form method="POST" action="{{ route('settings.system.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="language" class="form-label fw-semibold">Bahasa</label>
                                <select class="form-select" id="language" name="language">
                                    <option value="id" {{ old('language', config('app.locale', 'id')) == 'id' ? 'selected' : '' }}>Indonesia</option>
                                    <option value="en" {{ old('language', config('app.locale', 'id')) == 'en' ? 'selected' : '' }}>English</option>
                                </select>
                                <div class="form-text">Pilih bahasa antarmuka aplikasi</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="timezone" class="form-label fw-semibold">Zona Waktu</label>
                                <select class="form-select" id="timezone" name="timezone">
                                    <option value="Asia/Jakarta" {{ old('timezone', config('app.timezone', 'Asia/Jakarta')) == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (GMT+7)</option>
                                    <option value="Asia/Makassar" {{ old('timezone', config('app.timezone', 'Asia/Jakarta')) == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (GMT+8)</option>
                                    <option value="Asia/Jayapura" {{ old('timezone', config('app.timezone', 'Asia/Jakarta')) == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (GMT+9)</option>
                                </select>
                                <div class="form-text">Pilih zona waktu lokal Anda</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_format" class="form-label fw-semibold">Format Tanggal</label>
                                <select class="form-select" id="date_format" name="date_format">
                                    <option value="d/m/Y" {{ old('date_format', config('app.date_format', 'd/m/Y')) == 'd/m/Y' ? 'selected' : '' }}>Hari/Bulan/Tahun (01/12/2026)</option>
                                    <option value="m/d/Y" {{ old('date_format', config('app.date_format', 'd/m/Y')) == 'm/d/Y' ? 'selected' : '' }}>Bulan/Hari/Tahun (12/01/2026)</option>
                                    <option value="Y-m-d" {{ old('date_format', config('app.date_format', 'd/m/Y')) == 'Y-m-d' ? 'selected' : '' }}>Tahun-Bulan-Hari (2026-12-01)</option>
                                </select>
                                <div class="form-text">Format tanggal yang ditampilkan di aplikasi</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="theme" class="form-label fw-semibold">Tema Aplikasi</label>
                                <select class="form-select" id="theme" name="theme">
                                    <option value="light" {{ old('theme', config('app.theme', 'light')) == 'light' ? 'selected' : '' }}>Light Mode</option>
                                    <option value="dark" {{ old('theme', config('app.theme', 'light')) == 'dark' ? 'selected' : '' }}>Dark Mode</option>
                                    <option value="auto" {{ old('theme', config('app.theme', 'light')) == 'auto' ? 'selected' : '' }}>Sesuai Sistem</option>
                                </select>
                                <div class="form-text">Pilih tema tampilan aplikasi</div>
                            </div>
                        </div>
                    </div>

                    <!-- Theme Color Customization Section -->
                    <hr class="my-4">
                    <h5 class="mb-4">Kustomisasi Warna Tema</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="primary_color" class="form-label fw-semibold">Warna Utama</label>
                                <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="{{ old('primary_color', session('user_primary_color', '#276a2b')) }}" title="Pilih warna utama">
                                <div class="form-text">Warna utama yang digunakan di seluruh aplikasi</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="secondary_color" class="form-label fw-semibold">Warna Sekunder</label>
                                <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" value="{{ old('secondary_color', session('user_secondary_color', '#1f5522')) }}" title="Pilih warna sekunder">
                                <div class="form-text">Warna pendukung untuk elemen UI</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="accent_color" class="form-label fw-semibold">Warna Aksen</label>
                                <input type="color" class="form-control form-control-color" id="accent_color" name="accent_color" value="{{ old('accent_color', session('user_accent_color', '#4caf50')) }}" title="Pilih warna aksen">
                                <div class="form-text">Warna untuk eleman interaktif seperti tombol</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="background_color" class="form-label fw-semibold">Warna Latar</label>
                                <input type="color" class="form-control form-control-color" id="background_color" name="background_color" value="{{ old('background_color', session('user_background_color', '#f8f9fa')) }}" title="Pilih warna latar">
                                <div class="form-text">Warna latar belakang utama aplikasi</div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-hdd-stack me-2"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-ui.card>
</div>
@endsection