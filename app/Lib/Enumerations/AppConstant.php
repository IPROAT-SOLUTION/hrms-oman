<?php

namespace App\Lib\Enumerations;

class AppConstant
{
    public static $PASI_LIMIT = 3000;
    public static $DISTANCE = 500;
    public static $OKEY = 1;
    public static $NOT_OKEY = 0;
    public static $ZERO_HOUR = '00:00:00';
    public static $MINIMUM_OT_HOUR = '01:00:00';
    public static $HALF_DAY_HOUR = '04:00:00';
    public static $FULL_DAY_HOUR = '09:00:00';
    public static $INCENTIVE_HOUR = '12:00:00';
    public static $PLANT = 'muscat-insurance';
    public static $OMANIS = 0;
    public static $INCREMENT_MONTH = 1;
    public static $PROBATION_LIMIT = 6; // months - after probation like that 
    public static $SICK_LEAVE_ID = 6;
    public static $ANNUAL_LEAVE_ID = 7;
}
