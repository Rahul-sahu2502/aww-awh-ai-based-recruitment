<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_detail extends Model
{
    use HasFactory;

    public $timestamps = false; // Disable timestamps

    protected $table = 'tbl_user_detail';

    protected $primaryKey = 'ID';

    // Define the fillable attributes
    protected $fillable = [
        'ID',
        'First_Name',
        'Middle_Name',
        'Last_Name',
        'MotherName',
        'FatherName',
        'DOB',
        'Contact_Number',
        'Email',
        'aadharno',
        'epicno',
        'Nationality',
        'Caste',
        'Domicile_District_lgd',
        'Corr_Address',
        'Perm_Address',
        'Corr_District_lgd',
        'Corr_pincode',
        'Perm_District_lgd',
        'Perm_pincode',
        'Gender',
        'Created_On',
        'IP_Address'
    ];
}
