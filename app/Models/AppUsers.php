<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppUsers extends Model
{
    use HasFactory;
    

    protected $table = 'app_users';
    protected $primaryKey = 'USER_ID';
    protected $fillable = [
        'PASSWORD',
        'NAMA_LENGKAP',
        'EMAIL',
    ];
}
