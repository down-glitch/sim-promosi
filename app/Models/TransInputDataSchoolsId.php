<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransInputDataSchoolsId extends Model
{
    use HasFactory;
    

    protected $table = 'trans_input_data_schools_id';
    protected $primaryKey = 'Input_Data_Schools_Id';
    protected $fillable = [
        'Input_Data_Id',
        'School_Id',
        'Created_By',
        'Modified_By',
        'Created_Date',
        'Modified_Date',
    ];

    protected $casts = [
            'Input_Data_Schools_Id' => 'integer',
            'Input_Data_Id' => 'integer',
    ];
}
