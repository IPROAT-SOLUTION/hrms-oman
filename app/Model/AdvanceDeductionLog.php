<?php

namespace App\Model;

use App\User;
use App\Model\Employee;
use App\Model\AdvanceDeduction;
use Illuminate\Database\Eloquent\Model;

class AdvanceDeductionLog extends Model
{
   protected $table = 'advance_deduction_logs';
   protected $primaryKey = 'advance_deduction_log_id';

   protected $fillable = [
      'advance_deduction_log_id', 'advance_deduction_id', 'branch_id', 'employee_id', 'advance_amount', 'advancededuction_name', 'date_of_advance_given', 'deduction_amouth_per_month', 'no_of_month_to_be_deducted', 'remaining_month', 'status', 'paid_amount', 'pending_amount', 'payment_type', 'reason', 'created_by', 'updated_by', 'deleted_by'
   ];


   public function advance()
   {
      return $this->hasOne(AdvanceDeduction::class, 'advance_deduction_id', 'advance_deduction_id');
   }
   public function employee()
   {
      return $this->hasOne(Employee::class, 'employee_id', 'employee_id');
   }
   public function createduser()
   {
      return $this->hasOne(User::class, 'user_id', 'created_by');
   }
   public function updateduser()
   {
      return $this->hasOne(User::class, 'user_id', 'updated_by');
   }
   public function deleted_user()
   {
      return $this->hasOne(User::class, 'user_id', 'deleted_by');
   }
}
