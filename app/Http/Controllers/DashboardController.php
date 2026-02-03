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

        // Hitung jumlah data dari setiap tipe secara efisien dengan join
        $formattedInputTypes = DB::table('mstr_input_data_type as mdt')
            ->leftJoin('trans_input_data as tid', 'mdt.Input_Data_Type_Id', '=', 'tid.Input_Data_Type')
            ->select(
                'mdt.Input_Data_Type_Id',
                'mdt.Input_Data_Type',
                DB::raw('COUNT(tid.Input_Data_Id) as jumlah_data')
            )
            ->groupBy('mdt.Input_Data_Type_Id', 'mdt.Input_Data_Type')
            ->orderBy('mdt.Input_Data_Type_Id')
            ->get()
            ->map(function($type) {
                return [
                    'Input_Data_Type_Id' => $type->Input_Data_Type_Id,
                    'Input_Data_Type' => $type->Input_Data_Type,
                    'jumlah_data' => $type->jumlah_data
                ];
            })
            ->toArray();

        // Ambil aktivitas terbaru
        $recentActivities = TransInputData::with(['inputDataSchoolsId.school', 'inputDataPerson', 'inputDataDepartment.department'])
            ->latest('Created_Date')
            ->limit(4)
            ->get()
            ->map(function($activity) {
                // Dapatkan nama sekolah terkait
                $schoolName = '';
                if ($activity->inputDataSchoolsId->first()) {
                    $school = $activity->inputDataSchoolsId->first()->school;
                    $schoolName = $school ? $school->NAME : '';
                }

                // Dapatkan nama penanggung jawab
                $personName = '';
                if ($activity->inputDataPerson->first()) {
                    $person = $activity->inputDataPerson->first();
                    $personName = $person ? $person->Name : '';
                }

                // Dapatkan prodi terkait
                $departmentNames = [];
                foreach ($activity->inputDataDepartment as $dept) {
                    if ($dept->department) {
                        $departmentNames[] = $dept->department->DEPARTMENT_NAME;
                    }
                }

                $deptText = !empty($departmentNames) ? ' | Prodi: ' . implode(', ', $departmentNames) : '';

                return [
                    'title' => $activity->Promotion_Name ?? 'Data promosi',
                    'description' => 'Sekolah: ' . ($schoolName ?: 'N/A') . ($personName ? ' | Penanggung Jawab: ' . $personName : '') . $deptText,
                    'time' => $activity->Created_Date ? \Carbon\Carbon::parse($activity->Created_Date)->diffForHumans() : 'Baru saja',
                    'type' => $this->getActivityTypeName($activity->Input_Data_Type)
                ];
            })
            ->toArray();

        // Jika tidak ada aktivitas dari TransInputData, gunakan data dummy
        if (empty($recentActivities)) {
            $recentActivities = [
                [
                    'title' => 'Sistem Siap Digunakan',
                    'description' => 'Silakan tambahkan data promosi pertama Anda',
                    'time' => 'Baru saja',
                    'type' => 'Info'
                ]
            ];
        }

        // Statistik tambahan: data tahun ini
        $currentYear = now()->year;
        $dataTahunIni = TransInputData::whereYear('Created_Date', $currentYear)
            ->count();

        $dataBulanIni = TransInputData::whereYear('Created_Date', $currentYear)
            ->whereMonth('Created_Date', now()->month)
            ->count();

        // Data per bulan untuk tahun ini
        $dataPerBulan = TransInputData::select(
                DB::raw('MONTH(Created_Date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('Created_Date', $currentYear)
            ->groupBy(DB::raw('MONTH(Created_Date)'))
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Lengkapi array untuk semua bulan
        $dataPerBulanLengkap = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataPerBulanLengkap[$i] = $dataPerBulan[$i] ?? 0;
        }

        return view('dashboard', [
            'inputTypes' => $formattedInputTypes,
            'recentActivities' => $recentActivities,
            'dataTahunIni' => $dataTahunIni,
            'dataBulanIni' => $dataBulanIni,
            'dataPerBulan' => $dataPerBulanLengkap
        ]);
    }

    /**
     * Helper function to get activity type name
     */
    private function getActivityTypeName($typeId)
    {
        $type = MstrInputDataType::find($typeId);
        return $type ? $type->Input_Data_Type : 'Unknown';
    }
}
