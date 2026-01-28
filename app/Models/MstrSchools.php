<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstrSchools extends Model
{
    use HasFactory;
    

    protected $table = 'mstr_schools';
    protected $primaryKey = 'INSTITUTION_CODE';
    protected $fillable = [
        'NAME',
        'ADDRESS',
        'CITY',
        'PROVINCE',
    ];
}
