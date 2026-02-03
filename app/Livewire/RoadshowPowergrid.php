<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class RoadshowPowergrid extends Component
{
    use WithPagination;

    #[Reactive]
    public $search = '';

    public int $perPage = 10;

    public function getRoadshowData()
    {
        // Ambil data roadshow dari database
        $linkedData = DB::table('mstr_schools as ms')
            ->join('trans_input_data_schools_id as tdsi', 'ms.INSTITUTION_CODE', '=', 'tdsi.School_Id')
            ->join('trans_input_data as td', 'tdsi.Input_Data_Id', '=', 'td.Input_Data_Id')
            ->where('td.Input_Data_Type', 1)
            ->select('ms.PROVINCE', 'ms.CITY')
            ->distinct()
            ->get()
            ->toArray();

        $manualData = DB::table('trans_input_data as td')
            ->leftJoin('trans_input_data_schools_id as tdsi', 'td.Input_Data_Id', '=', 'tdsi.Input_Data_Id')
            ->where('td.Input_Data_Type', 1)
            ->whereNull('tdsi.Input_Data_Id')
            ->whereNotNull('td.Note')
            ->get();

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

        $allData = array_merge($linkedData, $manualProcessed);

        $grouped = collect($allData)->groupBy(function($item) {
            return $item->PROVINCE . '|' . $item->CITY;
        });

        $roadshows = $grouped->map(function($items, $key) {
            [$province, $city] = explode('|', $key);
            
            $linkedCount = DB::table('mstr_schools as ms')
                ->join('trans_input_data_schools_id as tdsi', 'ms.INSTITUTION_CODE', '=', 'tdsi.School_Id')
                ->join('trans_input_data as td', 'tdsi.Input_Data_Id', '=', 'td.Input_Data_Id')
                ->where('ms.PROVINCE', $province)
                ->where('ms.CITY', $city)
                ->where('td.Input_Data_Type', 1)
                ->distinct('tdsi.Input_Data_Id')
                ->count('DISTINCT tdsi.Input_Data_Id');

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

        return $roadshows;
    }

    public function getFilteredRoadshows()
    {
        $data = $this->getRoadshowData();

        if ($this->search) {
            $search = strtolower($this->search);
            $data = array_filter($data, function($item) use ($search) {
                return stripos($item['provinsi'], $search) !== false || 
                       stripos($item['kabupaten'], $search) !== false;
            });
        }

        return array_values($data);
    }

    public function render()
    {
        $filtered = $this->getFilteredRoadshows();
        $total = count($filtered);
        $totalPages = ceil($total / $this->perPage);
        $page = $this->getPage();
        
        $offset = ($page - 1) * $this->perPage;
        $roadshows = array_slice($filtered, $offset, $this->perPage);

        return view('livewire.roadshow-powergrid', [
            'roadshows' => $roadshows,
            'total' => $total,
            'totalPages' => $totalPages,
            'page' => $page,
            'perPage' => $this->perPage,
        ]);
    }

    public function goToPage($page)
    {
        $this->setPage($page);
    }

    public function nextPage()
    {
        $this->setPage($this->getPage() + 1);
    }

    public function previousPage()
    {
        $this->setPage($this->getPage() - 1);
    }

    private function getPage()
    {
        return intval(request()->query('page', 1));
    }

    private function setPage($page)
    {
        // Livewire 4 menangani pagination secara otomatis
        // Metode ini memperbarui URL query
    }
}
