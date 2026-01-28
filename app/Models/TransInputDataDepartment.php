<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransInputDataDepartment extends Model
{
    use HasFactory;
    

    protected $table = 'trans_input_data_department';
    protected $primaryKey = 'Input_Data_Department_Id';
    protected $fillable = [
        'Input_Data_Id',
        'Department_Id',
        'Created_By',
        'Modified_By',
        'Created_Date',
        'Modified_Date',
    ];

    protected $casts = [
            'Input_Data_Department_Id' => 'integer',
            'Input_Data_Id' => 'integer',
    ];
}
