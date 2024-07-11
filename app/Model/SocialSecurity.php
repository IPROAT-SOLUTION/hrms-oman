<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SocialSecurity extends Model
{
    protected $table = 'social_security';
    protected $primaryKey = 'social_security_id';

    protected $fillable = [
        'social_security_id', 'year', 'nationality', 'percentage', 'gross_salary', 'employer_contribution'
    ];
}
