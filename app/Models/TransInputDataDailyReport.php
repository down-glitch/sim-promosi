<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransInputDataDailyReport extends Model
{
    use HasFactory;
    

    protected $table = 'trans_input_data_daily_report';
    protected $primaryKey = 'Input_Data_Daily_Report_Id';
    protected $fillable = [
        'Input_Data_Id',
        'Daily_Report',
        'File_Attachment',
        'Place',
        'Employe',
        'Order_Id',
        'Date',
        'Created_By',
        'Modified_By',
        'Created_Date',
        'Modified_Date',
    ];

    protected $casts = [
            'Input_Data_Daily_Report_Id' => 'integer',
            'Input_Data_Id' => 'integer',
            'Order_Id' => 'integer',
    ];
}
