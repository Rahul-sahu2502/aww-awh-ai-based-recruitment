<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class master_district extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = 'master_district';
    protected $hidden = [];
    protected $fillable = ["name"];
}
