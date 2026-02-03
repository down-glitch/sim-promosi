<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransInputDataPerson extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'trans_input_data_person';
    protected $primaryKey = 'Input_Data_Person_Id';
    protected $fillable = [
        'Input_Data_Id',
        'Name',
        'Created_By',
        'Modified_By',
        'Created_Date',
        'Modified_Date',
    ];

    protected $casts = [
            'Input_Data_Person_Id' => 'integer',
            'Input_Data_Id' => 'integer',
    ];

    // Relasi ke tabel trans_input_data
    public function inputData()
    {
        return $this->belongsTo(TransInputData::class, 'Input_Data_Id', 'Input_Data_Id');
    }
}
