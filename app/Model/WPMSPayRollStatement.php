<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WPMSPayRollStatement extends Model
{
    protected $table = 'wpms_payroll_statement';
    protected $primaryKey = 'wpms_id';

    protected $fillable = [
        'wpms_id', 'employee_id_type', 'document_id', 'employee_id', 'document_id', 'reference_number', 'employee_name', 'employee_bic', 'employee_account',
        'salary_frequency', 'number_of_working_days', 'net_salary', 'basic_salary', 'extra_hours', 'extra_income', 'deductions',
        'social_security_deductions', 'notes_comments'
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'employee_id', 'employee_id');
    }
}
