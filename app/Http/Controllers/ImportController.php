<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TransInputData;
use App\Models\TransInputDataSchoolsId;
use App\Models\TransInputDataPerson;
use App\Models\TransInputDataDepartment;
use App\Models\TransInputDataSponsorship;
use App\Models\MstrSchools;
use App\Models\MstrDepartment;
use App\Models\ManualEntry;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function showImportForm()
    {
        return view('import.form');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // max 10MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $file = $request->file('file');

        // Generate unique batch ID
        $batchId = 'import_' . date('Ymd_His') . '_' . Str::random(8);

        // Proses file berdasarkan tipe
        $extension = $file->getClientOriginalExtension();

        switch ($extension) {
            case 'csv':
                $data = $this->processCsv($file->getPathname());
                break;
            case 'xlsx':
            case 'xls':
                $data = $this->processExcel($file->getPathname());
                break;
            default:
                return redirect()->back()->withErrors(['file' => 'Format file tidak didukung']);
        }

        // Validasi data sebelum diimpor
        $validationErrors = $this->validateImportData($data);
        if (!empty($validationErrors)) {
            return redirect()->back()->withErrors(['data' => $validationErrors])->withInput();
        }

        // Simpan data ke database
        $importedCount = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                $result = $this->saveImportedRow($row, $batchId);
                if ($result) {
                    $importedCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage(); // +2 karena baris 1 adalah header
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withErrors(['import_errors' => $errors]);
        }

        return redirect()->route('activities.roadshow')->with('success',
            "Berhasil mengimpor {$importedCount} data kegiatan roadshow");
    }

    private function processCsv($filePath)
    {
        $data = [];
        $file = fopen($filePath, 'r');

        // Lewati header
        $header = fgetcsv($file);

        while (($row = fgetcsv($file)) !== FALSE) {
            // Sesuaikan dengan kolom yang ada di file CSV
            $data[] = [
                'provinsi' => $row[0] ?? '',
                'kabupaten' => $row[1] ?? '',
                'nama_sekolah' => $row[2] ?? '',
                'alamat_sekolah' => $row[3] ?? '',
                'nama_kegiatan' => $row[4] ?? '',
                'tanggal_mulai' => $row[5] ?? '',
                'tanggal_selesai' => $row[6] ?? '',
                'penanggungjawab' => $row[7] ?? '',
                'prodi_1' => $row[8] ?? '',
                'prodi_2' => $row[9] ?? '',
                'prodi_3' => $row[10] ?? '',
                'jumlah_alumni' => $row[11] ?? '',
            ];
        }

        fclose($file);
        return $data;
    }

    private function processExcel($filePath)
    {
        // Cek apakah library tersedia
        if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
            throw new \Exception('Library PhpSpreadsheet tidak ditemukan. Mohon install dengan: composer require phpoffice/phpspreadsheet');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Lewati header
        array_shift($rows);

        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'provinsi' => $row[0] ?? '',
                'kabupaten' => $row[1] ?? '',
                'nama_sekolah' => $row[2] ?? '',
                'alamat_sekolah' => $row[3] ?? '',
                'nama_kegiatan' => $row[4] ?? '',
                'tanggal_mulai' => $row[5] ?? '',
                'tanggal_selesai' => $row[6] ?? '',
                'penanggungjawab' => $row[7] ?? '',
                'prodi_1' => $row[8] ?? '',
                'prodi_2' => $row[9] ?? '',
                'prodi_3' => $row[10] ?? '',
                'jumlah_alumni' => $row[11] ?? '',
            ];
        }

        return $data;
    }

    private function validateImportData($data)
    {
        $errors = [];

        foreach ($data as $index => $row) {
            // Validasi setiap baris
            if (empty($row['provinsi'])) {
                $errors[] = "Baris " . ($index + 2) . ": Kolom provinsi wajib diisi";
            }

            if (empty($row['kabupaten'])) {
                $errors[] = "Baris " . ($index + 2) . ": Kolom kabupaten wajib diisi";
            }

            if (empty($row['nama_kegiatan'])) {
                $errors[] = "Baris " . ($index + 2) . ": Kolom nama kegiatan wajib diisi";
            }

            if (empty($row['tanggal_mulai'])) {
                $errors[] = "Baris " . ($index + 2) . ": Kolom tanggal mulai wajib diisi";
            } else {
                // Validasi format tanggal
                $date = \DateTime::createFromFormat('Y-m-d', $row['tanggal_mulai']) ?:
                        \DateTime::createFromFormat('d/m/Y', $row['tanggal_mulai']) ?:
                        \DateTime::createFromFormat('d-m-Y', $row['tanggal_mulai']);

                if (!$date) {
                    $errors[] = "Baris " . ($index + 2) . ": Format tanggal mulai tidak valid";
                }
            }

            if (empty($row['tanggal_selesai'])) {
                $errors[] = "Baris " . ($index + 2) . ": Kolom tanggal selesai wajib diisi";
            } else {
                // Validasi format tanggal
                $date = \DateTime::createFromFormat('Y-m-d', $row['tanggal_selesai']) ?:
                        \DateTime::createFromFormat('d/m/Y', $row['tanggal_selesai']) ?:
                        \DateTime::createFromFormat('d-m-Y', $row['tanggal_selesai']);

                if (!$date) {
                    $errors[] = "Baris " . ($index + 2) . ": Format tanggal selesai tidak valid";
                }
            }
        }

        return $errors;
    }

    private function saveImportedRow($row, $batchId)
    {
        // Konversi format tanggal
        $startDate = $this->convertDate($row['tanggal_mulai']);
        $endDate = $this->convertDate($row['tanggal_selesai']);

        // Cek apakah sekolah sudah ada
        $school = MstrSchools::where([
            ['NAME', $row['nama_sekolah']],
            ['PROVINCE', $row['provinsi']],
            ['CITY', $row['kabupaten']]
        ])->first();

        if (!$school) {
            // Buat sekolah baru
            $school = MstrSchools::create([
                'INSTITUTION_CODE' => 'INST_' . strtoupper(substr(md5($row['nama_sekolah']), 0, 8)),
                'NAME' => $row['nama_sekolah'],
                'ADDRESS' => $row['alamat_sekolah'],
                'CITY' => $row['kabupaten'],
                'PROVINCE' => $row['provinsi'],
            ]);
        }

        // Buat data kegiatan
        $inputData = TransInputData::create([
            'Input_Data_Type' => 1, // Roadshow
            'Promotion_Name' => $row['nama_kegiatan'],
            'Event_Start_Date' => $startDate,
            'Event_End_Date' => $endDate,
            'Note' => json_encode([
                'provinsi' => $row['provinsi'],
                'kabupaten' => $row['kabupaten'],
                'sekolah' => $row['nama_sekolah'],
            ]),
            'Created_By' => auth()->id() ?? 1, // Gunakan ID user yang sedang login atau default ke 1
            'Modified_By' => auth()->id() ?? 1,
            'Created_Date' => now(),
            'Modified_Date' => now(),
            'import_batch_id' => $batchId,
            'imported_at' => now(),
        ]);

        // Hubungkan dengan sekolah
        TransInputDataSchoolsId::create([
            'Input_Data_Id' => $inputData->Input_Data_Id,
            'School_Id' => $school->INSTITUTION_CODE,
            'Created_By' => auth()->id() ?? 1,
            'Modified_By' => auth()->id() ?? 1,
            'Created_Date' => now(),
            'Modified_Date' => now(),
        ]);

        // Tambahkan penanggung jawab
        if (!empty($row['penanggungjawab'])) {
            TransInputDataPerson::create([
                'Input_Data_Id' => $inputData->Input_Data_Id,
                'Name' => $row['penanggungjawab'],
                'Created_By' => auth()->id() ?? 1,
                'Modified_By' => auth()->id() ?? 1,
                'Created_Date' => now(),
                'Modified_Date' => now(),
            ]);
        }

        // Tambahkan program studi
        $prodiFields = ['prodi_1', 'prodi_2', 'prodi_3'];
        foreach ($prodiFields as $field) {
            if (!empty($row[$field])) {
                $department = MstrDepartment::firstOrCreate([
                    'DEPARTMENT_NAME' => $row[$field],
                ], [
                    'DEPARTMENT_NAME' => $row[$field],
                ]);

                TransInputDataDepartment::create([
                    'Input_Data_Id' => $inputData->Input_Data_Id,
                    'Department_Id' => $department->DEPARTMENT_ID,
                    'Created_By' => auth()->id() ?? 1,
                    'Modified_By' => auth()->id() ?? 1,
                    'Created_Date' => now(),
                    'Modified_Date' => now(),
                ]);
            }
        }

        // Tambahkan data alumni
        if (!empty($row['jumlah_alumni']) && is_numeric($row['jumlah_alumni'])) {
            TransInputDataSponsorship::create([
                'Input_Data_Id' => $inputData->Input_Data_Id,
                'Sponsorship_Name' => (string)$row['jumlah_alumni'],
                'Amount' => $row['jumlah_alumni'],
                'Description' => 'Jumlah alumni dari ' . $row['kabupaten'],
                'Created_By' => auth()->id() ?? 1,
                'Modified_By' => auth()->id() ?? 1,
                'Created_Date' => now(),
                'Modified_Date' => now(),
            ]);
        }

        return true;
    }

    private function convertDate($dateString)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $dateString) ?:
                \DateTime::createFromFormat('d/m/Y', $dateString) ?:
                \DateTime::createFromFormat('d-m-Y', $dateString);

        if ($date) {
            return $date->format('Y-m-d');
        }

        return null;
    }
}
