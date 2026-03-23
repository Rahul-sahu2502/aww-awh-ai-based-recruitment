<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class user_post_apply extends Model
{
    // Specify the exact table name
    protected $table = 'tbl_user_post_apply';

    // Specify the primary key
    protected $primaryKey = 'apply_id';

    // Disable Laravel's default timestamps (created_at, updated_at)
    public $timestamps = false;

    // Define fillable fields for mass assignment
    protected $fillable = [
        'fk_applicant_id',
        'fk_post_id',
        'fk_district_id',
        'application_num',
        'post_area',
        'post_block',
        'post_gp',
        'post_village',
        'post_nagar',
        'post_ward',
        'post_projects',
        'status',
        'self_attested_file',
        'self_attested_file_upload_date',
        'reason_rejection',
        'edu_qualification_mark',
        'min_edu_qualification_mark ',
        'govt_min_edu_qualification_mark ',
        'domicile_mark',
        'walk_in_interview_mark',
        'skill_test_mark',
        'total_mark',
        'is_final_submit',
        'apply_date',
        'status_date',
        'eligiblity_date',
        'updated_at',
        'stepCount',
    ];
}
