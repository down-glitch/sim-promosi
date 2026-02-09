<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ManualEntry extends Model
{
    use HasFactory;

    protected $table = 'manual_entries';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'input_data_id',
        'province',
        'city',
        'school_name',
        'school_address',
        'contact_person',
        'phone_number',
        'notes',
    ];

    protected $casts = [
        'id' => 'integer',
        'input_data_id' => 'integer',
    ];

    // Relasi ke tabel trans_input_data
    public function inputData()
    {
        return $this->belongsTo(TransInputData::class, 'input_data_id', 'Input_Data_Id');
    }
}