<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransLetterRequest extends Model
{
    use HasFactory;
    

    protected $table = 'trans_letter_request';
    protected $primaryKey = 'Input_Letter_Request_Id';
    protected $fillable = [
        'Letter_Sender',
        'Letter_Number',
        'Letter_Date',
        'Promosion_Request',
        'No_Agenda',
        'Created_By',
        'Modified_By',
        'Created_Date',
        'Modified_Date',
    ];

    protected $casts = [
            'Input_Letter_Request_Id' => 'integer',
            'Promosion_Request' => 'integer',
    ];
}
