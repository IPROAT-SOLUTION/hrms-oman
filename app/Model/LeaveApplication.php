<?php

namespace App\Model;

use App\Traits\BranchTrait;
use Cohensive\Embed\Facades\Embed;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
  // use BranchTrait;

  protected $table = 'leave_application';
  protected $primaryKey = 'leave_application_id';

  protected $fillable = [
    'leave_application_id', 'branch_id', 'employee_id', 'leave_type_id', 'application_from_date', 'application_to_date', 'application_date',
    'number_of_day', 'approve_date', 'approve_by', 'reject_date', 'reject_by', 'purpose', 'remarks', 'manager_remarks', 'status', 'manager_status', 'document', 'created_by'
  ];

  public function employee()
  {
    return $this->belongsTo(Employee::class, 'employee_id')->withDefault(
      [
        'employee_id' => 0,
        'user_id' => 0,
        'finger_id' => 0,
        'department_id' => 0,
        'email' => 'unknown email',
        'first_name' => 'unknown',
        'last_name' => ''

      ]
    );
  }

  public function approveBy()
  {
    return $this->belongsTo(Employee::class, 'approve_by', 'employee_id')->withDefault(
      [
        'employee_id' => 0,
        'finger_id' => 0,
        'user_id' => 0,
        'department_id' => 0,
        'email' => 'unknown email',
        'first_name' => 'unknown',
        'last_name' => ''

      ]
    );
  }
  public function createdBy()
  {
    return $this->belongsTo(Employee::class, 'created_by', 'employee_id')->withDefault(
      [
        'employee_id' => 0,
        'finger_id' => 0,
        'user_id' => 0,
        'department_id' => 0,
        'email' => 'unknown email',
        'first_name' => 'unknown',
        'last_name' => ''
      ]
    );
  }

  public function rejectBy()
  {
    return $this->belongsTo(Employee::class, 'reject_by', 'employee_id')->withDefault(
      [
        'employee_id' => 0,
        'finger_id' => 0,
        'user_id' => 0,
        'department_id' => 0,
        'email' => 'unknown email',
        'first_name' => 'unknown',
        'last_name' => ''
      ]
    );
  }

  public function leaveType()
  {
    return $this->belongsTo(LeaveType::class, 'leave_type_id')->withDefault(
      [
        'leave_type_id' => 0,
        'leave_type_name' => 0,
        'num_of_day' => 0,
        'nationality' => 0,
        'religion' => 0,
        'gender' => 0,
        'status' => 0,
        'branch_id' => 0,
      ]
    );
  }

  public function getVideoHtmlAttribute()
  {
    $embed = Embed::make($this->document)->parseUrl();
    if (!$embed)
      return '';
    $embed->setAttribute([
      'id' => 'viewFiles',
      'width' => '100%', 'height' => '100%',
      'allowfullscreen' => 'true'
    ]);
    return $embed->getHtml();
  }
}
