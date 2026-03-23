<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class master_sector extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = 'ila_sectors';
    protected $hidden = [];
    protected $fillable = ["Sec_Name","Sector_Code","Project_Code"];
}
