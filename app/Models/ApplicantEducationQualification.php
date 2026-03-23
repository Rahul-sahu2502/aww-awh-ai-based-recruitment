<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantEducationQualification extends Model
{
    use HasFactory;


    protected $table = 'tbl_applicant_education_qualification';
    protected $primaryKey = 'qualification_id';
    public $timestamps = false; // Since we manually handle timestamps

    protected $fillable = [
        'qualification_id',
        'fk_applicant_id',
        'fk_Quali_ID',
        'fk_subject_id',
        'year_passing',
        'total_marks',
        'obtained_marks',
        'percentage',
        'fk_grade_id',
        'qualification_board',
        'Created_on',
        'Created_at',
        'Created_by',
        'updated_at',
        'updated_by',
        'create_ip'
    ];
}
