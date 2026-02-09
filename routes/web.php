<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Route untuk testing API - tanpa middleware auth
Route::get('/test-api-sekolah', [App\Http\Controllers\TestApiController::class, 'testApi'])->name('test.api.sekolah');

// Redirect home ke login
Route::get('/', function () {
    return redirect('/login');
});

// Login Routes (public)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'handleLogin'])->name('login.handle');
});

// Protected Routes (harus login)
Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route untuk menu-menu sidebar
    Route::get('/master-data/institutions', function() {
        return view('master-data.institutions');
    })->name('master-data.institutions');

    Route::get('/master-data/departments', function() {
        return view('master-data.departments');
    })->name('master-data.departments');

    Route::get('/master-data/users', function() {
        return view('master-data.users');
    })->name('master-data.users');

    Route::get('/schedules', function() {
        return view('schedules.index');
    })->name('schedules.index');

    Route::get('/letters', function() {
        return view('letters.index');
    })->name('letters.index');

    Route::get('/activities/roadshow', [App\Http\Controllers\RoadshowController::class, 'index'])->name('activities.roadshow');
    Route::get('/activities/roadshow/create', [App\Http\Controllers\RoadshowController::class, 'create'])->name('activities.roadshow.create');
    Route::post('/activities/roadshow', [App\Http\Controllers\RoadshowController::class, 'store'])->name('activities.roadshow.store');
    Route::get('/activities/roadshow/detail/{provinsi}/{kabupaten}', [App\Http\Controllers\RoadshowController::class, 'detail'])->name('activities.roadshow.detail');
    Route::get('/activities/roadshow/{id}/edit', [App\Http\Controllers\RoadshowController::class, 'edit'])->name('activities.roadshow.edit');
    Route::put('/activities/roadshow/{id}', [App\Http\Controllers\RoadshowController::class, 'update'])->name('activities.roadshow.update');
    Route::delete('/activities/roadshow/{id}', [App\Http\Controllers\RoadshowController::class, 'destroy'])->name('activities.roadshow.destroy');
    Route::get('/activities/roadshow/export/excel/{provinsi}/{kabupaten}', [App\Http\Controllers\RoadshowController::class, 'exportExcel'])->name('activities.roadshow.export.excel');
    Route::get('/activities/roadshow/export/pdf/{provinsi}/{kabupaten}', [App\Http\Controllers\RoadshowController::class, 'exportPdf'])->name('activities.roadshow.export.pdf');
    Route::get('/activities/roadshow/debug/{provinsi}/{kabupaten}', [App\Http\Controllers\RoadshowController::class, 'debug'])->name('activities.roadshow.debug');
    Route::get('/activities/roadshow/debug-jakarta', [App\Http\Controllers\RoadshowController::class, 'debugJakarta']);
    Route::get('/activities/roadshow/check-data', [App\Http\Controllers\RoadshowController::class, 'checkData'])->name('activities.roadshow.checkData');
    Route::get('/activities/roadshow/debug-data/{input_data_id}', [App\Http\Controllers\RoadshowController::class, 'checkDataDebug']);
    
    // API Routes
    Route::get('/api/autocomplete-provinsi', [App\Http\Controllers\RoadshowController::class, 'autocompleteProvincia']);
    Route::get('/api/get-kabupaten/{provinsi}', [App\Http\Controllers\RoadshowController::class, 'getKabupaten']);
    Route::get('/api/get-sekolah/{provinsi}/{kabupaten}', [App\Http\Controllers\RoadshowController::class, 'getSekolah']);
    Route::get('/api/get-school-history/{school_id}', [App\Http\Controllers\RoadshowController::class, 'getSchoolHistory']);
    Route::get('/api/get-departments', [App\Http\Controllers\RoadshowController::class, 'getDepartments']);

    Route::get('/activities/expo', function() {
        return view('activities.expo');
    })->name('activities.expo');

    Route::get('/activities/sponsorship', function() {
        return view('activities.sponsorship');
    })->name('activities.sponsorship');

    Route::get('/activities/tour', function() {
        return view('activities.tour');
    })->name('activities.tour');

    Route::get('/activities/presentation', function() {
        return view('activities.presentation');
    })->name('activities.presentation');

    Route::get('/activities/other', function() {
        return view('activities.other');
    })->name('activities.other');

    Route::get('/analytics/dashboard', [App\Http\Controllers\AnalyticsController::class, 'dashboard'])->name('analytics.dashboard');
    Route::get('/analytics/report', [App\Http\Controllers\AnalyticsController::class, 'report'])->name('analytics.report');
    Route::get('/analytics/export-report', [App\Http\Controllers\AnalyticsController::class, 'exportReport'])->name('analytics.export-report');

    Route::get('/import/form', [App\Http\Controllers\ImportController::class, 'showImportForm'])->name('import.form');
    Route::post('/import/process', [App\Http\Controllers\ImportController::class, 'import'])->name('import.process');

    // Routes untuk verifikasi dan autocomplete sekolah - tanpa middleware auth
    Route::prefix('api/sekolah')->withoutMiddleware(['auth'])->group(function () {
        Route::get('/autocomplete', [App\Http\Controllers\SchoolVerificationController::class, 'autocompleteSekolah'])->name('api.sekolah.autocomplete');
        Route::post('/verify', [App\Http\Controllers\SchoolVerificationController::class, 'verifySekolah'])->name('api.sekolah.verify');
        Route::get('/provinsi', [App\Http\Controllers\SchoolVerificationController::class, 'getProvinsi'])->name('api.sekolah.provinsi');
        Route::get('/kabupaten/{provinsi}', [App\Http\Controllers\SchoolVerificationController::class, 'getKabupatenByProvince'])->name('api.sekolah.kabupaten');
        Route::get('/search', [App\Http\Controllers\SchoolVerificationController::class, 'searchSekolah'])->name('api.sekolah.search');
    });

    // Route untuk testing API - tanpa middleware auth
    Route::get('/test-api-sekolah', [App\Http\Controllers\TestApiController::class, 'testApi'])->name('test.api.sekolah');

    Route::get('/reports', function() {
        return view('reports.index');
    })->name('reports.index');

    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/profile', [App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::patch('/settings/security', [App\Http\Controllers\SettingsController::class, 'updateSecurity'])->name('settings.security.update');
    Route::patch('/settings/notification', [App\Http\Controllers\SettingsController::class, 'updateNotification'])->name('settings.notification.update');
    Route::patch('/settings/system', [App\Http\Controllers\SettingsController::class, 'updateSystem'])->name('settings.system.update');
});
