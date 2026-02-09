<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class TransInputData extends Model
{
    use HasFactory, Auditable;

    public $timestamps = false;

    protected $table = 'trans_input_data';
    protected $primaryKey = 'Input_Data_Id';
    protected $fillable = [
        'Input_Data_Type',
        'Promotion_Name',
        'Event_Start_Date',
        'Event_End_Date',
        'Note',
        'Created_By',
        'Modified_By',
        'Created_Date',
        'Modified_Date',
        'Input_Letter_Request_Id',
    ];

    protected $casts = [
            'Input_Data_Id' => 'integer',
            'Input_Letter_Request_Id' => 'integer',
    ];

    // Relasi ke tabel trans_input_data_schools_id
    public function inputDataSchoolsId()
    {
        return $this->hasMany(TransInputDataSchoolsId::class, 'Input_Data_Id', 'Input_Data_Id');
    }

    // Relasi ke tabel trans_input_data_person
    public function inputDataPerson()
    {
        return $this->hasMany(TransInputDataPerson::class, 'Input_Data_Id', 'Input_Data_Id');
    }

    // Relasi ke tabel trans_input_data_department
    public function inputDataDepartment()
    {
        return $this->hasMany(TransInputDataDepartment::class, 'Input_Data_Id', 'Input_Data_Id');
    }

    // Relasi ke tabel manual_entries
    public function manualEntry()
    {
        return $this->hasOne(ManualEntry::class, 'input_data_id', 'Input_Data_Id');
    }
}
