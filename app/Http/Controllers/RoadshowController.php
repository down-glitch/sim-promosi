<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MstrSchools;
use App\Models\TransInputData;
use Illuminate\Support\Facades\DB;

class RoadshowController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data roadshow dari dua sumber:
        // 1. Dari mstr_schools yang terlink di trans_input_data
        // 2. Dari JSON di Note field untuk manual entries
        
        // Query 1: Data dari mstr_schools (linked entries)
        $linkedData = DB::table('mstr_schools as ms')
            ->join('trans_input_data_schools_id as tdsi', 'ms.INSTITUTION_CODE', '=', 'tdsi.School_Id')
            ->join('trans_input_data as td', 'tdsi.Input_Data_Id', '=', 'td.Input_Data_Id')
            ->where('td.Input_Data_Type', 1)
            ->select('ms.PROVINCE', 'ms.CITY')
            ->distinct()
            ->get()
            ->toArray();

        // Query 2: Data dari Note field (manual entries)
        $manualData = DB::table('trans_input_data as td')
            ->leftJoin('trans_input_data_schools_id as tdsi', 'td.Input_Data_Id', '=', 'tdsi.Input_Data_Id')
            ->where('td.Input_Data_Type', 1)
            ->whereNull('tdsi.Input_Data_Id') // Hanya yang tidak terlink ke sekolah
            ->whereNotNull('td.Note')
            ->get();

        // Process manual data dan extract province/city dari Note JSON
        $manualProcessed = [];
        foreach ($manualData as $data) {
            try {
                $note = json_decode($data->Note, true);
                if (is_array($note) && isset($note['provinsi']) && isset($note['kabupaten'])) {
                    $manualProcessed[] = (object)[
                        'PROVINCE' => $note['provinsi'],
                        'CITY' => $note['kabupaten']
                    ];
                }
            } catch (\Exception $e) {
                // Skip invalid JSON
            }
        }

        // Gabung kedua data
        $allData = array_merge($linkedData, $manualProcessed);

        // Group berdasarkan province dan city
        $grouped = collect($allData)->groupBy(function($item) {
            return $item->PROVINCE . '|' . $item->CITY;
        });

        // Hitung kegiatan per group
        $roadshows = $grouped->map(function($items, $key) {
            [$province, $city] = explode('|', $key);
            
            // Hitung kegiatan dari linked schools
            $linkedCount = DB::table('mstr_schools as ms')
                ->join('trans_input_data_schools_id as tdsi', 'ms.INSTITUTION_CODE', '=', 'tdsi.School_Id')
                ->join('trans_input_data as td', 'tdsi.Input_Data_Id', '=', 'td.Input_Data_Id')
                ->where('ms.PROVINCE', $province)
                ->where('ms.CITY', $city)
                ->where('td.Input_Data_Type', 1)
                ->distinct('tdsi.Input_Data_Id')
                ->count('DISTINCT tdsi.Input_Data_Id');

            // Hitung kegiatan dari manual entries
            $manualCount = DB::table('trans_input_data as td')
                ->leftJoin('trans_input_data_schools_id as tdsi', 'td.Input_Data_Id', '=', 'tdsi.Input_Data_Id')
                ->where('td.Input_Data_Type', 1)
                ->whereNull('tdsi.Input_Data_Id')
                ->whereNotNull('td.Note')
                ->whereRaw("JSON_VALID(td.Note) = 1")
                ->where(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(td.Note, '$.provinsi'))"), $province)
                ->where(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(td.Note, '$.kabupaten'))"), $city)
                ->count();

            $totalKegiatan = $linkedCount + $manualCount;

            return [
                'id' => md5($province . $city),
                'provinsi' => $province,
                'kabupaten' => $city,
                'jumlah_kegiatan' => $totalKegiatan
            ];
        })->values()->toArray();

        // Filter berdasarkan pencarian jika ada
        if ($request->has('search') && $request->search) {
            $search = strtolower($request->search);
            $roadshows = array_filter($roadshows, function($item) use ($search) {
                return stripos($item['provinsi'], $search) !== false || 
                       stripos($item['kabupaten'], $search) !== false;
            });
        }

        return view('activities.roadshow', compact('roadshows'));
    }


    public function detail(Request $request, $provinsi, $kabupaten)
    {
        // Query: Ambil SEMUA data roadshow untuk provinsi dan kabupaten ini
        // Baik yang terlink ke sekolah di mstr_schools maupun yang belum
        
        // 1. Ambil data dari sekolah yang terlink ke trans_input_data
        $linkedSchools = DB::table('mstr_schools as ms')
            ->leftJoin('trans_input_data_schools_id as tdsi', 'ms.INSTITUTION_CODE', '=', 'tdsi.School_Id')
            ->leftJoin('trans_input_data as td', 'tdsi.Input_Data_Id', '=', 'td.Input_Data_Id')
            ->leftJoin('trans_input_data_person as tdp', 'td.Input_Data_Id', '=', 'tdp.Input_Data_Id')
            ->where('ms.PROVINCE', $provinsi)
            ->where('ms.CITY', $kabupaten)
            ->where('td.Input_Data_Type', 1)
            ->select(
                'ms.INSTITUTION_CODE',
                'ms.NAME as nama_sekolah',
                'td.Input_Data_Id',
                'td.Event_Start_Date',
                'td.Promotion_Name',
                'tdp.Name as penanggungjawab'
            )
            ->orderBy('ms.NAME')
            ->get();

        // 2. Ambil data manual entries dari Note field
        $manualSchools = DB::table('trans_input_data as td')
            ->leftJoin('trans_input_data_schools_id as tdsi', 'td.Input_Data_Id', '=', 'tdsi.Input_Data_Id')
            ->leftJoin('trans_input_data_person as tdp', 'td.Input_Data_Id', '=', 'tdp.Input_Data_Id')
            ->where('td.Input_Data_Type', 1)
            ->whereNull('tdsi.Input_Data_Id') // Tidak terlink ke sekolah
            ->whereNotNull('td.Note')
            ->get();

        $manualProcessed = collect();
        foreach ($manualSchools as $item) {
            try {
                $note = json_decode($item->Note, true);
                if (is_array($note) && 
                    isset($note['provinsi']) && $note['provinsi'] === $provinsi &&
                    isset($note['kabupaten']) && $note['kabupaten'] === $kabupaten) {
                    
                    $item->INSTITUTION_CODE = $note['sekolah'] ?? 'N/A';
                    $item->nama_sekolah = $note['sekolah'] ?? 'N/A';
                    $manualProcessed->push($item);
                }
            } catch (\Exception $e) {
                // Skip invalid JSON
            }
        }

        // Gabung kedua data
        $schools = $linkedSchools->merge($manualProcessed);

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $schools = $schools->filter(function ($item) use ($search) {
                return stripos($item->nama_sekolah, $search) !== false ||
                       stripos($item->Promotion_Name, $search) !== false;
            });
        }

        // Map hasil untuk format yang diinginkan
        $schoolsFormatted = $schools->map(function ($item, $key) {
            // Ambil data Prodi dari trans_input_data_department (maksimal 3)
            $prodi = '-';
            $prodi_list = [];
            if ($item->Input_Data_Id) {
                $prodis = DB::table('trans_input_data_department as tidd')
                    ->leftJoin('mstr_department as md', 'tidd.Department_Id', '=', 'md.DEPARTMENT_ID')
                    ->where('tidd.Input_Data_Id', $item->Input_Data_Id)
                    ->pluck('md.DEPARTMENT_NAME')
                    ->filter()
                    ->unique()
                    ->take(3)
                    ->toArray();

                if (!empty($prodis)) {
                    $prodi = implode(', ', $prodis);
                    $prodi_list = $prodis; // Kirim sebagai array untuk ditampilkan secara terpisah
                }
            }

            // Ambil alumni dari trans_input_data_sponsorship
            $alumni = '-';
            if ($item->Input_Data_Id) {
                $alumniData = DB::table('trans_input_data_sponsorship')
                    ->where('Input_Data_Id', $item->Input_Data_Id)
                    ->first();

                if ($alumniData && !empty($alumniData->Sponsorship_Name)) {
                    if (is_numeric($alumniData->Sponsorship_Name)) {
                        $alumni = (int)$alumniData->Sponsorship_Name;
                    }
                }
            }

            return [
                'id' => $key + 1,
                'input_data_id' => $item->Input_Data_Id, // Tambahkan ID kegiatan untuk edit/hapus
                'tanggal' => $item->Event_Start_Date ? \Carbon\Carbon::parse($item->Event_Start_Date)->format('d F Y') : '-',
                'nama_sekolah' => $item->nama_sekolah,
                'penanggungjawab' => $item->penanggungjawab ?? '-',
                'prodi' => $prodi,
                'prodi_list' => $prodi_list,
                'alumni' => $alumni
            ];
        })->toArray();

        return response()->json([
            'provinsi' => $provinsi,
            'kabupaten' => $kabupaten,
            'schools' => $schoolsFormatted
        ]);
    }


    // API: Autocomplete provinsi
    public function autocompleteProvincia(Request $request)
    {
        $search = $request->get('q', '');
        
        $provinces = MstrSchools::where('PROVINCE', 'like', '%' . $search . '%')
            ->select('PROVINCE')
            ->distinct()
            ->limit(10)
            ->pluck('PROVINCE')
            ->toArray();

        return response()->json($provinces);
    }

    // API: Get kabupaten berdasarkan provinsi
    public function getKabupaten(Request $request, $provinsi)
    {
        $kabupatens = MstrSchools::where('PROVINCE', $provinsi)
            ->select('CITY')
            ->distinct()
            ->orderBy('CITY')
            ->pluck('CITY')
            ->toArray();

        return response()->json($kabupatens);
    }

    // API: Get sekolah berdasarkan provinsi dan kabupaten
    public function getSekolah(Request $request, $provinsi, $kabupaten)
    {
        $schools = MstrSchools::where('PROVINCE', $provinsi)
            ->where('CITY', $kabupaten)
            ->select('INSTITUTION_CODE', 'NAME', 'ADDRESS')
            ->orderBy('NAME')
            ->get()
            ->map(function($school) {
                return [
                    'id' => $school->INSTITUTION_CODE,
                    'text' => $school->NAME . ' (' . $school->ADDRESS . ')',
                    'name' => $school->NAME,
                    'address' => $school->ADDRESS
                ];
            })
            ->toArray();

        return response()->json($schools);
    }

    // API: Get history dari sekolah yang dipilih
    public function getSchoolHistory($school_id)
    {
        // First, get distinct events for this school
        $events = DB::table('trans_input_data_schools_id as tdsi')
            ->join('trans_input_data as td', 'tdsi.Input_Data_Id', '=', 'td.Input_Data_Id')
            ->where('tdsi.School_Id', $school_id)
            ->select(
                'td.Input_Data_Id',
                'td.Promotion_Name',
                'td.Event_Start_Date'
            )
            ->orderBy('td.Event_Start_Date', 'desc')
            ->distinct('td.Input_Data_Id')
            ->limit(10)
            ->get();

        // Map events with alumni count from sponsorship data
        $history = $events->map(function($item) {
            // Get total alumni (sum) for this event - check both Sponsorship_Name and Amount columns
            $alumniCount = DB::table('trans_input_data_sponsorship')
                ->where('Input_Data_Id', $item->Input_Data_Id)
                ->get()
                ->reduce(function($carry, $sponsorship) {
                    $count = 0;
                    
                    // Try to get count from Sponsorship_Name if it's numeric
                    if (!empty($sponsorship->Sponsorship_Name) && is_numeric($sponsorship->Sponsorship_Name)) {
                        $count = (int)$sponsorship->Sponsorship_Name;
                    }
                    // Fallback to Amount column
                    else if ($sponsorship->Amount && $sponsorship->Amount > 0) {
                        $count = (int)$sponsorship->Amount;
                    }
                    
                    return $carry + $count;
                }, 0);
            
            return [
                'Input_Data_Id' => $item->Input_Data_Id,
                'Promotion_Name' => $item->Promotion_Name,
                'Event_Start_Date' => $item->Event_Start_Date,
                'alumni_count' => $alumniCount
            ];
        })->toArray();

        return response()->json($history);
    }

    public function create()
    {
        return view('activities.roadshow-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'sekolah_id' => 'required|string',
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'penanggungjawab' => 'required|string|max:255',
            'top_3_prodi' => 'required|array',
            'top_3_prodi.*' => 'string|max:255',
            'total_pendaftar' => 'required|integer|min:0',
            'conversion_rate' => 'nullable|numeric|min:0|max:100',
            'jenis_kegiatan' => 'required|string|max:255',
            'program_studi' => 'required|string|max:255',
            'pic_roadshow' => 'required|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Cek dan buat sekolah jika belum ada di mstr_schools
            $schoolCode = $validated['sekolah_id'];

            // Cek apakah sekolah dengan code ini sudah ada
            $schoolExists = DB::table('mstr_schools')
                ->where('INSTITUTION_CODE', $schoolCode)
                ->first();

            if (!$schoolExists) {
                // Jika tidak ada, buat sekolah baru di mstr_schools
                DB::table('mstr_schools')->insert([
                    'INSTITUTION_CODE' => $schoolCode,
                    'PROVINCE' => $validated['provinsi'],
                    'CITY' => $validated['kabupaten'],
                    'NAME' => $validated['sekolah_id'], // Gunakan nama yang di-input
                    'ADDRESS' => $validated['kabupaten'], // Default address
                ]);
            }

            // 2. Buat input data baru
            $noteData = [
                'provinsi' => $validated['provinsi'],
                'kabupaten' => $validated['kabupaten'],
                'sekolah' => $validated['sekolah_id'],
            ];

            $inputData = DB::table('trans_input_data')->insertGetId([
                'Input_Data_Type' => 1, // 1 untuk Roadshow
                'Promotion_Name' => $validated['nama_kegiatan'],
                'Event_Start_Date' => $validated['tanggal_mulai'],
                'Event_End_Date' => $validated['tanggal_selesai'],
                'Note' => json_encode($noteData),
                'Created_By' => session('user_id'),
                'Modified_By' => session('user_id'),
                'Created_Date' => now(),
                'Modified_Date' => now(),
            ]);

            // 3. Link sekolah dengan event
            DB::table('trans_input_data_schools_id')->insert([
                'Input_Data_Id' => $inputData,
                'School_Id' => $schoolCode,
                'Created_By' => session('user_id'),
                'Modified_By' => session('user_id'),
                'Created_Date' => now(),
                'Modified_Date' => now(),
            ]);

            // Tambah penanggungjawab
            DB::table('trans_input_data_person')->insert([
                'Input_Data_Id' => $inputData,
                'Name' => $validated['penanggungjawab'],
                'Created_By' => session('user_id'),
                'Modified_By' => session('user_id'),
                'Created_Date' => now(),
                'Modified_Date' => now(),
            ]);

            // Parse top_3_prodi dan insert
            $prodis = $validated['top_3_prodi'];

            foreach ($prodis as $index => $prodi) {
                if ($index >= 3) break; // Maksimal 3

                // Cari atau buat department
                $dept = DB::table('mstr_department')->where('DEPARTMENT_NAME', $prodi)->first();
                if ($dept) {
                    DB::table('trans_input_data_department')->insert([
                        'Input_Data_Id' => $inputData,
                        'Department_Id' => $dept->DEPARTMENT_ID,
                        'Created_By' => session('user_id'),
                        'Modified_By' => session('user_id'),
                        'Created_Date' => now(),
                        'Modified_Date' => now(),
                    ]);
                }
            }

            // Tambah sponsor (alumni)
            DB::table('trans_input_data_sponsorship')->insert([
                'Input_Data_Id' => $inputData,
                'Sponsorship_Name' => (string)$validated['total_pendaftar'],
                'Amount' => $validated['total_pendaftar'],
                'Description' => 'Total pendaftar dari ' . $validated['kabupaten'],
                'Created_By' => session('user_id'),
                'Modified_By' => session('user_id'),
                'Created_Date' => now(),
                'Modified_Date' => now(),
            ]);

            DB::commit();

            return redirect()->route('activities.roadshow')
                ->with('success', 'Data roadshow berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    // Method untuk menampilkan form edit
    public function edit($id)
    {
        // Ambil data kegiatan berdasarkan ID
        $kegiatan = DB::table('trans_input_data as td')
            ->leftJoin('trans_input_data_schools_id as tdsi', 'td.Input_Data_Id', '=', 'tdsi.Input_Data_Id')
            ->leftJoin('mstr_schools as ms', 'tdsi.School_Id', '=', 'ms.INSTITUTION_CODE')
            ->leftJoin('trans_input_data_person as tdp', 'td.Input_Data_Id', '=', 'tdp.Input_Data_Id')
            ->leftJoin('trans_input_data_department as tidd', 'td.Input_Data_Id', '=', 'tidd.Input_Data_Id')
            ->leftJoin('mstr_department as md', 'tidd.Department_Id', '=', 'md.DEPARTMENT_ID')
            ->leftJoin('trans_input_data_sponsorship as tids', 'td.Input_Data_Id', '=', 'tids.Input_Data_Id')
            ->select(
                'td.*',
                'ms.INSTITUTION_CODE as sekolah_id',
                'ms.NAME as nama_sekolah',
                'ms.PROVINCE as provinsi',
                'ms.CITY as kabupaten',
                'tdp.Name as penanggungjawab',
                DB::raw('GROUP_CONCAT(md.DEPARTMENT_NAME) as top_3_prodi'),
                'tids.Sponsorship_Name as total_pendaftar'
            )
            ->where('td.Input_Data_Id', $id)
            ->groupBy('td.Input_Data_Id', 'ms.INSTITUTION_CODE', 'ms.NAME', 'ms.PROVINCE', 'ms.CITY', 'tdp.Name', 'tids.Sponsorship_Name')
            ->first();

        if (!$kegiatan) {
            abort(404, 'Data kegiatan tidak ditemukan');
        }

        // Decode note untuk mendapatkan provinsi dan kabupaten jika data berasal dari manual entry
        $note = json_decode($kegiatan->Note, true);
        if ($note && is_array($note)) {
            $kegiatan->provinsi = $note['provinsi'] ?? $kegiatan->provinsi;
            $kegiatan->kabupaten = $note['kabupaten'] ?? $kegiatan->kabupaten;
            $kegiatan->nama_sekolah = $note['sekolah'] ?? $kegiatan->nama_sekolah;
        }

        // Tambahkan default values untuk field-field yang mungkin tidak ada di database
        if (!isset($kegiatan->conversion_rate)) {
            $kegiatan->conversion_rate = 0;
        }
        if (!isset($kegiatan->jenis_kegiatan)) {
            $kegiatan->jenis_kegiatan = '';
        }
        if (!isset($kegiatan->program_studi)) {
            $kegiatan->program_studi = '';
        }
        if (!isset($kegiatan->pic_roadshow)) {
            $kegiatan->pic_roadshow = '';
        }
        if (!isset($kegiatan->catatan)) {
            $kegiatan->catatan = '';
        }

        return view('activities.roadshow-edit', compact('kegiatan'));
    }

    // Method untuk update data
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'provinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'sekolah_id' => 'required|string',
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'penanggungjawab' => 'required|string|max:255',
            'top_3_prodi' => 'required|array',
            'top_3_prodi.*' => 'string|max:255',
            'total_pendaftar' => 'required|integer|min:0',
            'conversion_rate' => 'nullable|numeric|min:0|max:100',
            'jenis_kegiatan' => 'required|string|max:255',
            'program_studi' => 'required|string|max:255',
            'pic_roadshow' => 'required|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update data utama
            $noteData = [
                'provinsi' => $validated['provinsi'],
                'kabupaten' => $validated['kabupaten'],
                'sekolah' => $validated['sekolah_id'],
            ];

            DB::table('trans_input_data')
                ->where('Input_Data_Id', $id)
                ->update([
                    'Promotion_Name' => $validated['nama_kegiatan'],
                    'Event_Start_Date' => $validated['tanggal_mulai'],
                    'Event_End_Date' => $validated['tanggal_selesai'],
                    'Note' => json_encode($noteData),
                    'Modified_By' => session('user_id'),
                    'Modified_Date' => now(),
                ]);

            // Update atau buat sekolah jika belum ada
            $schoolCode = $validated['sekolah_id'];
            $schoolExists = DB::table('mstr_schools')
                ->where('INSTITUTION_CODE', $schoolCode)
                ->first();

            if (!$schoolExists) {
                DB::table('mstr_schools')->insert([
                    'INSTITUTION_CODE' => $schoolCode,
                    'PROVINCE' => $validated['provinsi'],
                    'CITY' => $validated['kabupaten'],
                    'NAME' => $validated['sekolah_id'],
                    'ADDRESS' => $validated['kabupaten'],
                ]);
            }

            // Update link sekolah
            DB::table('trans_input_data_schools_id')
                ->where('Input_Data_Id', $id)
                ->update([
                    'School_Id' => $schoolCode,
                    'Modified_By' => session('user_id'),
                    'Modified_Date' => now(),
                ]);

            // Update penanggungjawab
            DB::table('trans_input_data_person')
                ->where('Input_Data_Id', $id)
                ->update([
                    'Name' => $validated['penanggungjawab'],
                    'Modified_By' => session('user_id'),
                    'Modified_Date' => now(),
                ]);

            // Hapus data prodi lama
            DB::table('trans_input_data_department')
                ->where('Input_Data_Id', $id)
                ->delete();

            // Tambahkan data prodi baru
            $prodis = $validated['top_3_prodi'];
            foreach ($prodis as $index => $prodi) {
                if ($index >= 3) break; // Maksimal 3

                $dept = DB::table('mstr_department')->where('DEPARTMENT_NAME', $prodi)->first();
                if ($dept) {
                    DB::table('trans_input_data_department')->insert([
                        'Input_Data_Id' => $id,
                        'Department_Id' => $dept->DEPARTMENT_ID,
                        'Created_By' => session('user_id'),
                        'Modified_By' => session('user_id'),
                        'Created_Date' => now(),
                        'Modified_Date' => now(),
                    ]);
                }
            }

            // Update sponsor (alumni)
            DB::table('trans_input_data_sponsorship')
                ->where('Input_Data_Id', $id)
                ->update([
                    'Sponsorship_Name' => (string)$validated['total_pendaftar'],
                    'Amount' => $validated['total_pendaftar'],
                    'Description' => 'Total pendaftar dari ' . $validated['kabupaten'],
                    'Modified_By' => session('user_id'),
                    'Modified_Date' => now(),
                ]);

            DB::commit();

            return redirect()->route('activities.roadshow')
                ->with('success', 'Data roadshow berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    // Method untuk hapus data
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Hapus semua data terkait
            DB::table('trans_input_data_sponsorship')
                ->where('Input_Data_Id', $id)
                ->delete();

            DB::table('trans_input_data_department')
                ->where('Input_Data_Id', $id)
                ->delete();

            DB::table('trans_input_data_person')
                ->where('Input_Data_Id', $id)
                ->delete();

            DB::table('trans_input_data_schools_id')
                ->where('Input_Data_Id', $id)
                ->delete();

            DB::table('trans_input_data')
                ->where('Input_Data_Id', $id)
                ->delete();

            DB::commit();

            return redirect()->route('activities.roadshow')
                ->with('success', 'Data roadshow berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Gagal menghapus data: ' . $e->getMessage());
        }
    }

    // API: Get departments/prodi
    public function getDepartments(Request $request)
    {
        $search = $request->get('q', '');

        $departments = DB::table('mstr_department')
            ->where('DEPARTMENT_NAME', 'like', '%' . $search . '%')
            ->select('DEPARTMENT_ID', 'DEPARTMENT_NAME')
            ->limit(10)
            ->get()
            ->map(function($dept) {
                return [
                    'id' => $dept->DEPARTMENT_ID,
                    'text' => $dept->DEPARTMENT_NAME,
                    'name' => $dept->DEPARTMENT_NAME
                ];
            })
            ->toArray();

        return response()->json($departments);
    }
}
