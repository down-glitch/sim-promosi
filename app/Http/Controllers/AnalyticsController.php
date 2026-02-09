<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\TransInputData;
use App\Models\MstrSchools;
use App\Models\ManualEntry;

class AnalyticsController extends Controller
{
    public function dashboard()
    {
        // Statistik umum
        $totalKegiatan = TransInputData::where('Input_Data_Type', 1)->count();

        $totalSekolah = DB::table('mstr_schools')
            ->join('trans_input_data_schools_id', 'mstr_schools.INSTITUTION_CODE', '=', 'trans_input_data_schools_id.School_Id')
            ->join('trans_input_data', 'trans_input_data_schools_id.Input_Data_Id', '=', 'trans_input_data.Input_Data_Id')
            ->where('trans_input_data.Input_Data_Type', 1)
            ->distinct('mstr_schools.INSTITUTION_CODE')
            ->count('mstr_schools.INSTITUTION_CODE');

        $totalProvinsi = DB::table('mstr_schools')
            ->join('trans_input_data_schools_id', 'mstr_schools.INSTITUTION_CODE', '=', 'trans_input_data_schools_id.School_Id')
            ->join('trans_input_data', 'trans_input_data_schools_id.Input_Data_Id', '=', 'trans_input_data.Input_Data_Id')
            ->where('trans_input_data.Input_Data_Type', 1)
            ->distinct('mstr_schools.PROVINCE')
            ->count('mstr_schools.PROVINCE');

        // Data untuk chart provinsi
        $dataPerProvinsi = DB::table('mstr_schools as ms')
            ->join('trans_input_data_schools_id as tdsi', 'ms.INSTITUTION_CODE', '=', 'tdsi.School_Id')
            ->join('trans_input_data as td', 'tdsi.Input_Data_Id', '=', 'td.Input_Data_Id')
            ->where('td.Input_Data_Type', 1)
            ->select('ms.PROVINCE', DB::raw('COUNT(*) as jumlah_kegiatan'))
            ->groupBy('ms.PROVINCE')
            ->orderByDesc('jumlah_kegiatan')
            ->limit(10)
            ->get();

        // Gabungkan dengan data dari manual entries
        $manualData = ManualEntry::select('province', DB::raw('COUNT(*) as jumlah_kegiatan'))
            ->groupBy('province')
            ->orderByDesc('jumlah_kegiatan')
            ->limit(10)
            ->get();

        // Gabungkan data dari kedua sumber
        $mergedData = collect();
        foreach ($dataPerProvinsi as $item) {
            $mergedData->push($item);
        }
        foreach ($manualData as $item) {
            $existing = $mergedData->firstWhere('PROVINCE', $item->province);
            if ($existing) {
                $existing->jumlah_kegiatan += $item->jumlah_kegiatan;
            } else {
                $mergedData->push([
                    'PROVINCE' => $item->province,
                    'jumlah_kegiatan' => $item->jumlah_kegiatan
                ]);
            }
        }

        // Urutkan kembali dan ambil 10 teratas
        $dataPerProvinsi = $mergedData->sortByDesc('jumlah_kegiatan')->take(10)->values();

        return view('analytics.dashboard', compact(
            'totalKegiatan',
            'totalSekolah',
            'totalProvinsi',
            'dataPerProvinsi'
        ));
    }

    public function report(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Query untuk data kegiatan
        $query = DB::table('trans_input_data as td')
            ->leftJoin('trans_input_data_schools_id as tdsi', 'td.Input_Data_Id', '=', 'tdsi.Input_Data_Id')
            ->leftJoin('mstr_schools as ms', 'tdsi.School_Id', '=', 'ms.INSTITUTION_CODE')
            ->leftJoin('manual_entries as me', 'td.Input_Data_Id', '=', 'me.input_data_id')
            ->select(
                'td.Input_Data_Id',
                'td.Promotion_Name',
                'td.Event_Start_Date',
                'td.Event_End_Date',
                DB::raw('COALESCE(ms.PROVINCE, me.province) as province'),
                DB::raw('COALESCE(ms.CITY, me.city) as city'),
                DB::raw('COALESCE(ms.NAME, me.school_name) as school_name')
            )
            ->where('td.Input_Data_Type', 1); // Hanya roadshow

        // Filter berdasarkan tanggal jika disediakan
        if ($startDate) {
            $query->whereDate('td.Event_Start_Date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('td.Event_End_Date', '<=', $endDate);
        }

        $kegiatan = $query->orderBy('td.Event_Start_Date', 'desc')->get();

        return view('analytics.report', compact('kegiatan', 'startDate', 'endDate'));
    }

    public function exportReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Query untuk data kegiatan
        $query = DB::table('trans_input_data as td')
            ->leftJoin('trans_input_data_schools_id as tdsi', 'td.Input_Data_Id', '=', 'tdsi.Input_Data_Id')
            ->leftJoin('mstr_schools as ms', 'tdsi.School_Id', '=', 'ms.INSTITUTION_CODE')
            ->leftJoin('manual_entries as me', 'td.Input_Data_Id', '=', 'me.input_data_id')
            ->select(
                'td.Input_Data_Id',
                'td.Promotion_Name',
                'td.Event_Start_Date',
                'td.Event_End_Date',
                DB::raw('COALESCE(ms.PROVINCE, me.province) as province'),
                DB::raw('COALESCE(ms.CITY, me.city) as city'),
                DB::raw('COALESCE(ms.NAME, me.school_name) as school_name')
            )
            ->where('td.Input_Data_Type', 1); // Hanya roadshow

        // Filter berdasarkan tanggal jika disediakan
        if ($startDate) {
            $query->whereDate('td.Event_Start_Date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('td.Event_End_Date', '<=', $endDate);
        }

        $kegiatan = $query->orderBy('td.Event_Start_Date', 'desc')->get();

        // Export ke Excel
        return $this->exportToExcel($kegiatan);
    }

    private function exportToExcel($data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'ID Kegiatan');
        $sheet->setCellValue('B1', 'Nama Kegiatan');
        $sheet->setCellValue('C1', 'Tanggal Mulai');
        $sheet->setCellValue('D1', 'Tanggal Selesai');
        $sheet->setCellValue('E1', 'Provinsi');
        $sheet->setCellValue('F1', 'Kabupaten/Kota');
        $sheet->setCellValue('G1', 'Nama Sekolah');

        // Isi data
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->Input_Data_Id);
            $sheet->setCellValue('B' . $row, $item->Promotion_Name);
            $sheet->setCellValue('C' . $row, $item->Event_Start_Date);
            $sheet->setCellValue('D' . $row, $item->Event_End_Date);
            $sheet->setCellValue('E' . $row, $item->province);
            $sheet->setCellValue('F' . $row, $item->city);
            $sheet->setCellValue('G' . $row, $item->school_name);
            $row++;
        }

        // Format header
        $headerRange = 'A1:G1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFDDDDDD');

        // Auto size columns
        for ($col = 'A'; $col !== 'H'; $col++) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Buat writer
        $writer = new Xlsx($spreadsheet);

        // Simpan ke temporary file
        $fileName = 'laporan_kegiatan_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filePath = storage_path('app/temp/' . $fileName);

        // Buat direktori jika belum ada
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer->save($filePath);

        // Return download response
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
