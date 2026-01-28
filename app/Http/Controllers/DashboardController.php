<?php

namespace App\Http\Controllers;

use App\Models\MstrInputDataType;
use App\Models\TransInputData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show dashboard
     */
    public function index()
    {
        // Ambil semua tipe input data
        $inputTypes = MstrInputDataType::all();

        // Hitung jumlah data dari setiap tipe secara manual
        $formattedInputTypes = $inputTypes->map(function($type) {
            $count = TransInputData::where('Input_Data_Type', $type->Input_Data_Type)->count();

            return [
                'Input_Data_Type' => $type->Input_Data_Type,
                'jumlah_data' => $count
            ];
        });

        // Ambil aktivitas terbaru
        $recentActivities = TransInputData::latest('Created_Date')
            ->limit(4)
            ->get()
            ->map(function($activity) {
                return [
                    'title' => 'Promosi Baru Ditambahkan',
                    'description' => $activity->Promotion_Name ?? 'Data promosi',
                    'time' => $activity->Created_Date ? \Carbon\Carbon::parse($activity->Created_Date)->diffForHumans() : 'Baru saja'
                ];
            })
            ->toArray();

        // Jika tidak ada aktivitas dari TransInputData, gunakan data dummy
        if (empty($recentActivities)) {
            $recentActivities = [
                [
                    'title' => 'Sistem Siap Digunakan',
                    'description' => 'Silakan tambahkan data promosi pertama Anda',
                    'time' => 'Baru saja'
                ]
            ];
        }

        return view('dashboard', [
            'inputTypes' => $formattedInputTypes,
            'recentActivities' => $recentActivities
        ]);
    }
}
