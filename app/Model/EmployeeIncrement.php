<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmployeeIncrement extends Model
{

    protected $table = 'employee_increments';
    protected $primaryKey = 'employee_increment_id';

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'basic_amount',
        'increment_percentage',
        'increment_amount',
        'year',
    ];
}
