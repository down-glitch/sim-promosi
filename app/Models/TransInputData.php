<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransInputData extends Model
{
    use HasFactory;


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
}
