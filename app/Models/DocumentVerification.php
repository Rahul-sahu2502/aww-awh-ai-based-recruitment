<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVerification extends Model
{
    use HasFactory;

    protected $table = 'document_verifications';

    protected $fillable = [
        'fk_user_id',
        'fk_post_id',
        'fk_apply_id',
        'applicant_id',
        'document_key',
        'document_name',
        'is_verified',
        'remark',
        'verified_by',
    ];
}
