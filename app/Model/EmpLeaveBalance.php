<?php

namespace App\Model;

use App\Model\LeaveType;
use Illuminate\Database\Eloquent\Model;

class EmpLeaveBalance extends Model
{
    protected $table = 'emp_leave_balances';
    protected $primaryKey = 'id';
    protected $fillable = [
        'employee_id',
        'department_id',  'designation_id', 'branch_id',  'finger_id',
        'leave_type_id', 'leave_balance', 'created_at', 'updated_at'
    ];


    public function employee()
    {
        return $this->hasOne(Employee::class, 'employee_id', 'employee_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
