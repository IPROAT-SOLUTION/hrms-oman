<?php

use App\Model\MsSql;
use App\Model\Branch;
use App\Model\WorkShift;
use App\Model\FrontSetting;
use App\Model\ManualAttendance;
use NumberToWords\NumberToWords;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use App\Lib\Enumerations\AppConstant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

function shiftList()
{
    $workShift = WorkShift::all();
    $result = [];

    foreach ($workShift as $key => $value) {
        $result[$value->work_shift_id] = $value->shift_name;
    }

    return $result;
}

function branchList()
{
    $branches = Branch::all();
    $result = ['' => '---- Please Select ----'];

    foreach ($branches as $value) {
        $result[$value->branch_id] = $value->branch_name;
    }
    return $result;
}

function fullOrHalfDay($status)
{
    $array = array("0" => 'Half Day', "1" => 'Full Day');
    foreach ($array as $key => $value) {
        if ((int) $key == $status) {
            return $value;
        }
    }
}

function dateConvertFormtoDB($date)
{
    if (!empty($date)) {
        return date("Y-m-d", strtotime(str_replace('/', '-', $date)));
    }
}

function monthConvertFormtoDB($month)
{
    if (!empty($month)) {
        return date("Y-m", strtotime(str_replace('/', '-', $month)));
    }
}

function weekOffDateList($day, $month)
{
    // $start_date = $month . '-01';
    // $end_date   = date("Y-m-t", strtotime($start_date));

    $date = new DateTime('first ' . $day . ' of this month');
    // $thisMonth = $date->format('m');
    $thisMonth = date('m', strtotime($month));
    $dates = array();
    $i = 0;
    while ($date->format('m') === $thisMonth) {
        $i++;
        $dates[] = $date->format('Y-m-d');
        $date->modify('next ' . $day);
    }
    return $dates;
}

function monthConvertDBtoForm($month)
{
    if (!empty($month)) {
        $month = strtotime($month);
        return date('Y/m', $month);
    }
}
function dateConvertDBtoForm($date)
{
    if (!empty($date)) {
        $date = strtotime($date);
        return date('d/m/Y', $date);
    }
}
function findMonthFromToDate($start_date, $end_date)
{

    $target = strtotime($start_date);

    $workingDate = [];

    while ($target <= strtotime(date("Y-m-d", strtotime($end_date)))) {
        $temp = [];
        $temp['date'] = date('Y-m-d', $target);
        $temp['day'] = date('d', $target);
        $temp['day_name'] = date('D', $target);
        $workingDate[] = $temp;
        $target += (60 * 60 * 24);
    }
    // dd($workingDate);
    return $workingDate;
}
function employeeInfo()
{
    return DB::select("call SP_getEmployeeInfo('" . decrypt(session('logged_session_data.employee_id')) . "')");
}

function permissionCheck()
{

    $role_id = decrypt(session('logged_session_data.role_id'));
    return $result = json_decode(DB::table('menus')->select('menu_url')
        ->join('menu_permission', 'menu_permission.menu_id', '=', 'menus.id')
        ->where('menu_permission.role_id', '=', $role_id)
        ->whereNotNull('action')->get()->toJson(), true);
}

function showMenu()
{
    $role_id = decrypt(session('logged_session_data.role_id'));
    $modules = json_decode(DB::table('modules')->get()->toJson(), true);
    $menus = json_decode(DB::table('menus')
        ->select(DB::raw('menus.id, menus.name, menus.menu_url, menus.parent_id, menus.module_id'))
        ->join('menu_permission', 'menu_permission.menu_id', '=', 'menus.id')
        ->where('menu_permission.role_id', $role_id)
        ->where('menus.status', 1)
        ->whereNull('action')
        ->orderBy('menus.id', 'ASC')
        ->get()->toJson(), true);

    $sideMenu = [];
    if ($menus) {
        foreach ($menus as $menu) {
            if (!isset($sideMenu[$menu['module_id']])) {
                $moduleId = array_search($menu['module_id'], array_column($modules, 'id'));

                $sideMenu[$menu['module_id']] = [];
                $sideMenu[$menu['module_id']]['id'] = $modules[$moduleId]['id'];
                $sideMenu[$menu['module_id']]['name'] = $modules[$moduleId]['name'];
                $sideMenu[$menu['module_id']]['icon_class'] = $modules[$moduleId]['icon_class'];
                $sideMenu[$menu['module_id']]['menu_url'] = '#';
                $sideMenu[$menu['module_id']]['parent_id'] = '';
                $sideMenu[$menu['module_id']]['module_id'] = $modules[$moduleId]['id'];
                $sideMenu[$menu['module_id']]['sub_menu'] = [];
            }
            if ($menu['parent_id'] == 0) {
                $sideMenu[$menu['module_id']]['sub_menu'][$menu['id']] = $menu;
                $sideMenu[$menu['module_id']]['sub_menu'][$menu['id']]['sub_menu'] = [];
            } else {
                array_push($sideMenu[$menu['module_id']]['sub_menu'][$menu['parent_id']]['sub_menu'], $menu);
            }
        }
    }

    return $sideMenu;
}

function convartMonthAndYearToWord($data)
{
    $monthAndYear = explode('-', $data);

    $month = $monthAndYear[1];
    $dateObj = DateTime::createFromFormat('!m', $month);
    $monthName = $dateObj->format('F');
    $year = $monthAndYear[0];

    return $monthAndYearName = $monthName . " " . $year;
}

function employeeAward()
{
    return ['Employee of the Month' => 'Employee of the Month', 'Employee of the Year' => 'Employee of the Year', 'Best Employee' => 'Best Employee'];
}

function weekedName()
{
    $week = array("Sun" => 'Sunday', "Mon" => 'Monday', "Tue" => 'Tuesday', "Wed" => 'Wednesday', "Thu" => 'Thursday', "Fri" => 'Friday', "Sat" => 'Saturday');
    return $week;
}

function attStatus($att_status)
{
    $status = array("1" => 'Present', "2" => 'Absent', "3" => 'Leave', "4" => 'Holiday', "5" => 'Future', "6" => 'Update', "7" => 'Error', "8" => 'Missing OUT Punch', "9" => 'Missing In Punch', '10' => 'Less Hours', '11' => 'Comp Off', '13' => 'Week Off', '14' => 'Half Day Leave', '15' => 'Full Day Leave', '16' => 'Half Day Present', '17' => 'Full Day Present');
    foreach ($status as $key => $value) {
        if ((int) $key == $att_status) {
            return $value;
        }
    }
}

function userStatus($att_status)
{
    $status = array("0" => 'Probation Period', "1" => 'Active', "2" => 'Inactive', "3" => 'Terminated', "4" => 'Permanent');
    foreach ($status as $key => $value) {
        if ((int) $key == $att_status) {
            return $value;
        }
    }
}

function allDevices()
{
    $options = [];
    $device = MsSql::select('device_name')->groupBy('device_name')->get(['device_name'])->toArray();
    $manual = ManualAttendance::select('device_name')->groupBy('device_name')->get(['device_name'])->toArray();
    $devices = (object) array_merge($device, $manual);

    foreach ($devices as $value) {
        if ($value['device_name'] != null) {
            $options[] = $value['device_name'];
        }
    }

    return $options;
}

function findMonthToAllDate($month)
{
    $start_date = $month . '-01';
    $end_date = date("Y-m-t", strtotime($start_date));

    $target = strtotime($start_date);
    $workingDate = [];
    while ($target <= strtotime(date("Y-m-d", strtotime($end_date)))) {
        $temp = [];
        $temp['date'] = date('Y-m-d', $target);
        $temp['day'] = date('d', $target);
        $temp['day_name'] = date('D', $target);
        $workingDate[] = $temp;
        $target += (60 * 60 * 24);
    }
    return $workingDate;
}

function findFromDateToDateToAllDate($start_date, $end_date)
{
    $target = strtotime($start_date);
    $workingDate = [];
    while ($target <= strtotime(date("Y-m-d", strtotime($end_date)))) {
        $temp = [];
        $temp['date'] = date('Y-m-d', $target);
        $temp['day'] = date('d', $target);
        $temp['day_name'] = date('D', $target);
        $workingDate[] = $temp;
        $target += (60 * 60 * 24);
    }
    return $workingDate;
}

function findMonthToStartDateAndEndDate($month)
{
    $start_date = $month . '-01';
    $end_date = date("Y-m-t", strtotime($start_date));
    $data = [
        'start_date' => $start_date,
        'end_date' => $end_date,
    ];
    return $data;
}

function findAllDates($start_date, $end_date)
{
    $target = strtotime($start_date);
    $workingDate = [];
    while ($target <= strtotime(date("Y-m-d", strtotime($end_date)))) {
        $temp = [];
        $temp = date('Y-m-d', $target);
        $workingDate[] = $temp;
        $target += (60 * 60 * 24);
    }
    return $workingDate;
}

function getFrontData()
{
    $setting = FrontSetting::orderBy('id', 'desc')->first();

    return $setting;
}

function password($count)
{
    $result = "";
    for ($value = 0; $value <= $count; $value++) {
        $result = $result . '*';
    }
    return $result;
}

function getRouteData($search)
{
    $options = [];

    $qry = '1 ';
    if ($search != '') {
        $qry = 'menus.menu_url LIKE  %' . $search . '%';
    }
    $menus = DB::table('menus')->where('status', AppConstant::$OKEY)
        ->where('menus.menu_url', '!=', null)
        ->join('menu_permission', 'menu_permission.menu_id', 'menus.id')
        ->where('menu_permission.role_id', decrypt(session('logged_session_data.role_id')))
        ->whereRaw($qry)
        ->orderBy('menus.name')
        ->get();

    foreach ($menus as $value) {
        $options[$value->menu_url] = $value->name;
    }

    return $options;
}

function getModelData()
{
    $options = [
        'App\User',
        'App\Model\Employee',
    ];

    return $options;
}

function paginate($items, $perPage = 5, $page = null, $options = [])
{
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);
    return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
}

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) === $date;
}

function dateRange($begin, $end, $interval = null)
{
    $begin = new DateTime($begin);
    $end = new DateTime($end);
    // Because DatePeriod does not include the last date specified.
    $end = $end->modify('+1 day');
    $interval = new DateInterval($interval ? $interval : 'P1D');

    return iterator_to_array(new DatePeriod($begin, $interval, $end));
}

function sumTimeArr($array, $hours = false)
{
    $sum = strtotime('00:00:00');

    $totaltime = 0;

    foreach ($array as $element) {

        // Converting the time into seconds
        $timeinsec = strtotime($element) - $sum;

        // Sum the time with previous value
        $totaltime = $totaltime + $timeinsec;
    }

    // Totaltime is the summation of all
    // time in seconds

    // Hours is obtained by dividing
    // totaltime with 3600
    $h = sprintf('%02d', intval($totaltime / 3600));

    $totaltime = $totaltime - ($h * 3600);

    // Minutes is obtained by dividing
    // remaining total time with 60
    $m = sprintf('%02d', intval($totaltime / 60));

    // Remaining value is seconds
    $s = sprintf('%02d', $totaltime - ($m * 60));

    // Printing the result

    if ($hours) {
        return "$h";
    }

    return "$h:$m:$s";
}

function decimalHours($time)
{
    $hms = explode(":", $time);
    return ($hms[0] + ($hms[1] / 60) + ($hms[2] / 3600));
}

function acronym($string = '')
{
    if (preg_match_all('/\b(\w)/', strtoupper($string), $m)) {
        return  implode('', $m[1]); // $v is now SOQTU
    }
    return $string;
}

function number_to_word($value)
{
    $format = new NumberFormatter('en_EU', NumberFormatter::CURRENCY_CODE);
    return ucwords($format->formatCurrency($value, 'OMR'));
}

function dateTimeToTime($dateTime)
{
    return date('H:i', strtotime($dateTime));
}

function numberToColumnName($num)
{
    $numeric = ($num - 1) % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval(($num - 1) / 26);
    if ($num2 > 0) {
        return numberToColumnName($num2) . $letter;
    } else {
        return $letter;
    }
}

function employee_category($id = '')
{
    $options = array('Employee',  'Chiefs');
    return list_response($options, $id);
}

function employee_marital_status($id = '')
{
    $options = array('Married', 'Unmarried', 'NoDisclosure');
    return list_response($options, $id);
}

function employee_gender($id = '')
{
    $options = array('Male', 'Female', 'NoDisclosure');
    return list_response($options, $id);
}

function yes_or_no($id = '')
{
    $options = array('No', 'Yes');
    return list_response($options, $id);
}

function employee_status($id = '')
{
    $options = array('In-Active', 'Active',  'Terminated',  'Resigned');
    return list_response($options, $id);
}

function employee_faith($id = '')
{
    $options = array('Muslim', 'Non-Muslim');
    return list_response($options, $id);
}

function employee_nationality($id = '')
{
    $options = array('Omanis',  'Expats');
    return list_response($options, $id);
}

function list_response($options, $id = '')
{
    if (isset($options[$id])) {
        return $options[$id];
    }
    return $options;
}

function numberToWords($number)
{
    $hyphen      = '-';
    $conjunction = ' ';
    $separator   = ' ';
    $negative    = 'negative ';
    $decimal     = ' ';
    $dictionary  = [
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'forty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    ];

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'numberToWords only accepts numbers between ' . PHP_INT_MIN . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . numberToWords(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . numberToWords($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= numberToWords($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $fraction = (int) $fraction;
        $string .= $decimal . 'Rials and ' . numberToWords($fraction) . ' baisas';
    }
   
    return ucwords($string);
}

function getColorForAttendance($status, $shift_name)
{
    if (!empty($shift_name) && in_array($status, $shift_name)) {
        return 'green';
    }

    switch ($status) {
        case 'AA':
            return 'red';
        case 'P':
            return 'blue';
        case 'HL':
            return 'blue';
        case 'FL':
            return 'blue';
        case 'WH':
            return '#F6BE00';
        case 'PH':
            return '#F6BE00';
        default:
            return 'black';
    }
}
