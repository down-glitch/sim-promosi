<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstrInputDataType extends Model
{
    use HasFactory;


    protected $table = 'mstr_input_data_type';
    protected $primaryKey = 'Input_Data_Type_Id';
    protected $fillable = [
        'Input_Data_Type',
        'Created_By',
        'Modified_By',
        'Created_Date',
        'Modified_Date',
    ];

    protected $casts = [
            'Input_Data_Type_Id' => 'integer',
    ];
}
