<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

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

    Route::get('/activities/roadshow', function() {
        return view('activities.roadshow');
    })->name('activities.roadshow');

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

    Route::get('/reports', function() {
        return view('reports.index');
    })->name('reports.index');

    Route::get('/settings', function() {
        return view('settings.index');
    })->name('settings.index');
});
