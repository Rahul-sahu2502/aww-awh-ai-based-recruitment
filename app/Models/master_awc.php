<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class master_awc extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = 'final_cg_awc_tbl';
    protected $hidden = [];
    protected $fillable = [];
}
