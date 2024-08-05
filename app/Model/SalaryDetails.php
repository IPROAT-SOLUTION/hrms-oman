<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SalaryDetails extends Model
{
    protected $table = 'salary_details';
    protected $primaryKey = 'salary_details_id';

    protected $fillable = [
        'salary_details_id',
        'employee_id',
        'branch_id',
        'month_of_salary',
        'basic_salary',
        'total_allowances',
        'total_deductions',
        'total_late',
        'total_late_amount',
        'total_absence',
        'increment_amount',
        'arrears_adjustment',
        'salary_advance',
        'total_absence_amount',
        'overtime_rate',
        'total_over_time_hour',
        'total_overtime_amount',
        'total_present',
        'total_leave',
        'total_working_days',
        'tax',
        'gross_salary',
        'public_holiday',
        'weekly_holiday',
        'pay_cut',
        'gsm',
        'lop',
        'prem_others',
        'extra_hours',
        'extra_amount',
        'comment',
        'status',
        'created_by',
        'updated_by',
        'payment_method',
        'action',
        'hourly_rate',
        'taxable_salary',
        'per_day_salary',
        'net_salary',
        'working_hour',
        'increment',
        'housing_allowance',
        'utility_allowance',
        'transport_allowance',
        'living_allowance',
        'employer_contribution',
        'mobile_allowance',
        'special_allowance',
        'social_security',
        'account_number',
        'ifsc_number',
        'name_of_the_bank',
        'account_holder',
        'education_and_club_allowance',
        'membership_allowance'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function payGrade()
    {
        return $this->belongsTo(PayGrade::class, 'pay_grade_id');
    }

    public function hourlySalaries()
    {
        return $this->belongsTo(HourlySalary::class, 'hourly_salaries_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }
}
