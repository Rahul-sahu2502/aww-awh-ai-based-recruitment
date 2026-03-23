<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class master_project extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = 'ila_pariyojna';
    protected $hidden = [];
    protected $fillable = ["Project_Name","Project_code"];
}
