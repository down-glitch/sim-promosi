<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstrDepartment extends Model
{
    use HasFactory;
    

    protected $table = 'mstr_department';
    protected $primaryKey = 'DEPARTMENT_ID';
    protected $fillable = [
        'DEPARTMENT_NAME',
        'FACULTY',
    ];
}
