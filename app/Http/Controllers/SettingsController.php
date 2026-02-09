<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'Anda harus login untuk mengubah pengaturan.']);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update user properties
        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'department' => $request->department,
            'bio' => $request->bio,
        ]);

        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updateSecurity(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'Anda harus login untuk mengubah pengaturan.']);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Password saat ini tidak cocok'])
                ->withInput();
        }

        // Update password
        $user->fill([
            'password' => Hash::make($request->new_password),
        ]);
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil diubah!');
    }

    public function updateNotification(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'Anda harus login untuk mengubah pengaturan.']);
        }

        $user->fill([
            'email_notifications' => $request->has('email_notifications'),
            'sms_notifications' => $request->has('sms_notifications'),
            'push_notifications' => $request->has('push_notifications'),
        ]);
        $user->save();

        return redirect()->back()->with('success', 'Preferensi notifikasi berhasil diperbarui!');
    }

    public function updateSystem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language' => 'nullable|string|in:id,en',
            'timezone' => 'nullable|string',
            'date_format' => 'nullable|string',
            'theme' => 'nullable|string|in:light,dark,auto',
            'primary_color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'secondary_color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'accent_color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'background_color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Store user preferences in session
        session([
            'user_language' => $request->language,
            'user_timezone' => $request->timezone,
            'user_date_format' => $request->date_format,
            'user_theme' => $request->theme,
            'user_primary_color' => $request->primary_color,
            'user_secondary_color' => $request->secondary_color,
            'user_accent_color' => $request->accent_color,
            'user_background_color' => $request->background_color,
        ]);

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui!');
    }
}