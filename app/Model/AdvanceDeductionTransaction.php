<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdvanceDeductionTransaction extends Model
{
    protected $table = 'advance_deduction_transaction';
    protected $primaryKey = 'advance_deduction_transaction_id';

    protected $fillable = [
        'advance_deduction_transaction_id','advance_deduction_log_id','advance_deduction_id', 'employee_id','advance_deduction_log_id','transaction_date','payment_type','cash_received', 'created_by', 'updated_by','created_at','updated_at','pending_amount','remaining_month'
    ];

    public function advance()
    {
       return $this->hasOne(AdvanceDeduction::class, 'advance_deduction_id', 'advance_deduction_id');
    }
    public function employee()
    {
       return $this->hasOne(Employee::class, 'employee_id', 'employee_id');
    }
}
