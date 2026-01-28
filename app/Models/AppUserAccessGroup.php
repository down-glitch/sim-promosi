<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppUserAccessGroup extends Model
{
    use HasFactory;
    

    protected $table = 'app_user_access_group';
    protected $primaryKey = 'APP_ID';
    protected $fillable = [
        'USER_ID',
        'LEVEL_USER',
    ];

    protected $casts = [
            'LEVEL_USER' => 'integer',
    ];
}
