<?php

namespace App\Lib\Enumerations;

class LeaveStatus
{
     public static $PENDING  = 1;
     public static $APPROVE  = 2;
     public static $REJECT  = 3;

     public static $SICK_LEAVE_A = 1.0;
     public static $SICK_LEAVE_B = 0.75;
     public static $SICK_LEAVE_C = 0.50;
     public static $SICK_LEAVE_D = 0.35;

     public static $SICK_LEAVE_D1 = 1;
     public static $SICK_LEAVE_D2 = 21;
     public static $SICK_LEAVE_D3 = 22;
     public static $SICK_LEAVE_D4 = 35;
     public static $SICK_LEAVE_D5 = 36;
     public static $SICK_LEAVE_D6 = 70;
     public static $SICK_LEAVE_D7 = 71;
     public static $SICK_LEAVE_D8 = 182;

     public static function sickLeavePolicy()
     {
          return [
               '1-21' => '100%',
               '25-35' => '75%',
               '36-70' => '50%',
               '71-182' => '35%'
          ];
     }
}
