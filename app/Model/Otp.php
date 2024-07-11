<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $table = 'otps';
    protected $primaryKey = 'otp_id';

    protected $fillable = [
        'otp_id',
        'otp',
        'employee_id',
        'email',
        'created_at',
        'updated_at',
        
    ];
}
