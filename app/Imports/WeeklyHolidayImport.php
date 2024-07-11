<?php

namespace App\Imports;

use DateTime;
use App\Model\Employee;
use App\Model\WeeklyHoliday;
use Illuminate\Support\Facades\Log;
use App\Lib\Enumerations\UserStatus;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class WeeklyHolidayImport  implements ToModel, WithValidation, WithStartRow
{
    use Importable;

    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function rules(): array
    {
        return [
            '*.0' => 'required',
            '*.1' => 'required|string',
            '*.2' => 'required|exists:employee,finger_id',
            '*.3' => 'required|exists:branch,branch_name',
            '*.4' => 'required|date_format:Y-m',
            '*.5' => function ($attribute, $value, $onFailure) {
                // $arr = explode(',', $value);
                // $arr = array_map('trim', $arr);
                // foreach ($arr as $key => $date) {
                //     if (!validateDate(dateConvertFormtoDB(trim($date)))) {
                //         $onFailure('Date is invalid, it should be yyyy-mm-dd as comma seperated values.');
                //     }
                // }
            },
        ];
    }

    public function customValidationMessages()
    {
        return [
            '2.required' => 'Employee id is required',
            '3.required' => 'Branch Name is required',
            '3.exists' => 'Branch Name is invalid',
            '4.required' => 'Month is required',
            '4.date_format' => 'Date format should be yyyy-mm',
        ];
    }

    public function model(array $row)
    {
        $employee = Employee::where('finger_id', $row[2])->first();
        $month = $row[4];
        // $list = explode(',', $row[5]);
        // $list = array_map('trim', $list);
        // $week_days = [];
        // foreach ($list as $key => $value) {
        //     $dateField = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');

        //     if ($dateField == '1970-01-01') {
        //         $dateField = date('Y-m-d', strtotime($value));
        //     }

        //     $week_days[] = dateConvertFormtoDB($dateField);
        // }
        $day_name = $row[5];
        // $day_name
        // dd($day_name);

        $day_name = explode(",", $row[5]);
        $dateList = '';
        $dayKey = '';
        $week_days = [];
        $dayKey1 = '';
        if (isset($month) && isset($day_name)) {
            $week = \weekedName();
            foreach ($week as $dayKey => $weekLi) {
                if ($weekLi === $day_name[0]) {
                    $dayKey = $dayKey;
                    break;
                }
            }
            if (count($day_name) > 1) {
                foreach ($week as $dayKey1 => $weekLi) {
                    if ($weekLi === $day_name[1]) {
                        $dayKey1 = $dayKey1;
                        break;
                    }
                }
            }
            $dateList = findMonthToAllDate($month);
            // dd($dateList);
            foreach ($dateList as $key => $dateLi) {
                if ($dateLi['day_name'] === $dayKey) {
                    $week_days[] .= $dateLi['date'];
                }
                if ($dateLi['day_name'] === $dayKey1) {
                    $week_days[] .= $dateLi['date'];
                }
            }
        }
        // dd($row[5]);
        $week_days = json_encode($week_days);
        $ifExists = WeeklyHoliday::where('employee_id', $employee->employee_id)->where('month',  $month)->first();

        if (!$ifExists) {
            // Log::info($week_days);
            $holidayData = WeeklyHoliday::create([
                'employee_id' => $employee->employee_id,
                'month' =>  $month,
                'day_name' =>  $row[5],
                'weekoff_days' => $week_days,
                'status' => UserStatus::$ACTIVE,
                'created_by' => auth()->user()->user_id,
                'updated_by' => auth()->user()->user_id,
            ]);
        } else {
            // Log::info($week_days);
            $holidayData =  WeeklyHoliday::where('employee_id', $employee->employee_id)->where('month',  $month)->update([
                'employee_id' => $employee->employee_id,
                'month' =>  $month,
                'day_name' =>  $row[5],
                'weekoff_days' => $week_days,
                'status' => UserStatus::$ACTIVE,
                'created_by' => auth()->user()->user_id,
                'updated_by' => auth()->user()->user_id,
            ]);

            Log::info($holidayData);
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
