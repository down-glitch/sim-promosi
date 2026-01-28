<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransInputDataSponsorship extends Model
{
    use HasFactory;
    

    protected $table = 'trans_input_data_sponsorship';
    protected $primaryKey = 'Input_Data_Sponsorship_Id';
    protected $fillable = [
        'Input_Data_Id',
        'Sponsorship_Name',
        'Amount',
        'Description',
        'Created_By',
        'Modified_By',
        'Created_Date',
        'Modified_Date',
    ];

    protected $casts = [
            'Input_Data_Sponsorship_Id' => 'integer',
            'Input_Data_Id' => 'integer',
            'Amount' => 'float',
    ];
}
