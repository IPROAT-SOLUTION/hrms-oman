<!DOCTYPE html>
<html lang="en">

<head>
    <title> @lang('attendance.summary_attendance')</title>
    <meta charset="utf-8">
</head>
<style>
    table {
        margin: 0 0 40px 0;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        display: table;
        border-collapse: collapse;
    }

    .printHead {
        width: 100%;
        text-align: center;
    }

    table,
    td,
    th {
        border: 1px solid black;
        text-align: center;
    }

    td {
        padding: 5px;
        text-align: center;
    }

    th {
        padding: 5px;
        text-align: center;
    }
</style>

<body style="word-wrap:break-word">
    <div class="printHead">
        <p style="margin-left: 32px;margin-top: 10px">
            <span style="font-weight:bold;">Summary Report</span>
        <p class="text-center font-bold" style="text-decoration: underline;">
            @if (isset($from_date) && isset($to_date))
                @if (date('Y-m', strtotime(dateConvertFormToDB($from_date))) == date('Y-m', strtotime(dateConvertFormToDB($to_date))))
                    <p class="text-center font-bold">
                        <span>{{ 'Month - ' . date('F', strtotime(dateConvertFormToDB($from_date))) . ' ' }}</span>
                        <span>{{ '(' . (dateConvertFormToDB($from_date) ?: '') . ' to ' . (dateConvertFormToDB($to_date) ?: '') . ')' }}</span>
                    </p>
                @else
                    {{ (date('F d', strtotime(dateConvertFormToDB($from_date))) ?: '') . ' to ' . (date('F d', strtotime(dateConvertFormToDB($to_date))) ?: '') }}
                @endif
            @endif
        </p>
        </p>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="font-bold">
                    P- Present, AA- Absent, WH- Weekly Holiday, PH- Public
                    Holiday, FL-
                    Full Day Leave, HL- Half Day Leave, Other- Shift Short Name,
                    @foreach ($leaveTypes as $key => $leaveType)
                        <span>{{ acronym($leaveType->leave_type_name) . ' - ' . $leaveType->leave_type_name }}{{ $loop->last ? '.' : ',' }}</span>
                    @endforeach
                </p>
                <hr>


            </div>
        </div>

        <div class="table-responsive">
            <table id="dailyAttendance" class="table table-bordered table-striped table-hover manage-u-table"
                style="font-size: 12px;">
                <thead>
                    <tr class="tr_header">
                        <th>@lang('common.serial')</th>
                        <th>@lang('employee.employee_id')</th>
                        <th>@lang('common.name')</th>
                        <th>@lang('employee.designation')</th>
                        <th>@lang('employee.department')</th>
                        @foreach ($monthToDate as $head)
                            <th class="text-center">{{ $head['day'] . '/' . $head['day_name'] }}</th>
                        @endforeach
                        <th>@lang('attendance.day_of_worked')</th>
                        <th>@lang('attendance.ph')</th>
                        @foreach ($leaveTypes as $leaveType)
                            <th>{{ acronym($leaveType->leave_type_name) }}</th>
                        @endforeach
                        <th>@lang('attendance.total_paid_days')</th>
                        <th>@lang('attendance.wh') </th>
                        <th>@lang('attendance.total_days')</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sl = null;
                        $totalPresent = 0;
                        $leaveData = [];
                        $totalCol = 0;
                        $totalWorkHour = 0;
                        $totalWeeklyHoliday = 0;
                        $totalGovtHoliday = 0;
                        $totalAbsent = 0;
                        $totalLeave = 0;
                    @endphp
                    @foreach ($results as $key => $value)
                        <tr>
                            <td>{{ ++$sl }}</td>
                            <td>{{ $value[0]['finger_id'] }}</td>
                            <td>{{ $value[0]['fullName'] }}</td>
                            <td>{{ $value[0]['designation_name'] }}</td>
                            <td>{{ $value[0]['department_name'] }}</td>
                            {{-- <td>{{ $value[0]['gender'] == 0 ? 'Male' : 'Female' }} </td>
                                            <td>{{ userStatus($value[0]['status']) }}</td> --}}
                            @foreach ($value as $v)
                                @php
                                    if ($sl == 1) {
                                        $totalCol++;
                                    }
                                    if ($v['attendance_status'] == 'present') {
                                        $totalPresent++;
                                        if ($v['shift_name'] != '' && $v['shift_name'] != null) {
                                            $shiftName = acronym($v['shift_name']);
                                        } else {
                                            $shiftName = 'NA';
                                        }

                                        if ($v['inout_status'] == 'O') {
                                            echo "<td class='text-center'><span style='color:black ;font-weight:bold'>" .
                                                $v['inout_status'] .
                                                '' .
                                                $shiftName .
                                                '</span></td>';
                                        } else {
                                            echo "<td class='text-center'><span style='color:black ;font-weight:bold'>" .
                                                $shiftName .
                                                '</span></td>';
                                        }
                                    } elseif ($v['attendance_status'] == 'absence') {
                                        $totalAbsent++;
                                        echo "<td class='text-center'><span style='color:'#D1D1D1';'>AA</span></td>";
                                    } elseif ($v['attendance_status'] == 'leave') {
                                        if ($v['day'] == 'FL') {
                                            $totalLeave += 1;
                                            $leaveData[$key][$v['leave_type']]['day'][] = 1;
                                        }
                                        if ($v['day'] == 'HL') {
                                            $totalLeave += 0.5;
                                            $leaveData[$key][$v['leave_type']]['day'][] = 0.5;
                                        }

                                        $leaveData[$key][$v['leave_type']][] = $v['leave_type'];

                                        echo "<td class='text-center'><span style='color:black ;font-weight:bold'>" .
                                            $v['day'] .
                                            '(' .
                                            acronym($v['leave_type']) .
                                            ')' ??
                                            'NA' . '</span></td>';
                                    } elseif ($v['attendance_status'] == 'holiday') {
                                        $totalWeeklyHoliday++;
                                        echo "<td class='text-center'><span style='color:black ;font-weight:bold'>WH</span></td>";
                                    } elseif ($v['attendance_status'] == 'publicHoliday') {
                                        $totalGovtHoliday++;
                                        echo "<td class='text-center'><span style='color: black ;font-weight:bold'>PH</span></td>";
                                    } elseif ($v['attendance_status'] == 'left') {
                                        echo "<td class='text-center'><span style='color:black ;font-weight:bold'></span></td>";
                                    } else {
                                        echo '<td></td>';
                                    }
                                @endphp
                            @endforeach
                            <td class='text-center'><span class="bolt">{{ $totalPresent }}</span>
                            </td>
                            <td class='text-center'><span class="bolt">{{ $totalGovtHoliday }}</span>
                            </td>
                            @foreach ($leaveTypes as $leaveType)
                                <td class='text-center'>

                                    @php
                                        if ($sl == 1) {
                                            $totalCol++;
                                        }
                                        if (isset($leaveData[$key][$leaveType->leave_type_name])) {
                                            $c = array_sum($leaveData[$key][$leaveType->leave_type_name]['day']);
                                        } else {
                                            $c = 0;
                                        }
                                    @endphp
                                    <span class="bolt">
                                        {{ $c }}
                                    </span>
                                </td>
                            @endforeach
                            <td class='text-center'><span
                                    class="bolt">{{ $totalPresent + $totalLeave + $totalGovtHoliday }}</span>
                            </td>
                            <td class='text-center'><span class="bolt">{{ $totalWeeklyHoliday }}</span></td>
                            <td class='text-center'><span
                                    class="bolt">{{ $totalPresent + $totalWeeklyHoliday + $totalAbsent + $totalLeave }}</span>
                            </td>
                            @php
                                $totalPresent = 0;
                                $totalWeeklyHoliday = 0;
                                $totalAbsent = 0;
                                $totalLeave = 0;
                                $totalGovtHoliday = 0;
                            @endphp
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
