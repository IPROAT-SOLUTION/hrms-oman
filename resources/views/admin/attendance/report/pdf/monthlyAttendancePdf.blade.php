<!DOCTYPE html>
<html lang="en">

<head>
    <title> @lang('attendance.monthly_attendance')</title>
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

    .page-break {
        page-break-inside: avoid;
        page-break-after: always;
    }
</style>

<body>
    <div class="container">
        <div class="table-responsive">
            @foreach ($results as $group => $result)
                @if (!empty($result))
                    <div style="text-align: center;">
                        <h3 style="margin-top: 10px;text-align: center;"><b>Monthly Attendance Report
                                {{ "($from_date-$to_date)" }}</b></h3>
                    </div>
                    <table id="monthlyAttendance" class="table table-bordered"
                        style="font-size: 14px; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <td colspan="5" style="text-align: left !important;font-size:16px;">
                                    Employee Name:
                                    <b>{{ $result[0]['fullName'] ?? '' }}</b>
                                </td>
                                <td colspan="4" style="text-align: left !important;font-size:16px;">Department:
                                    <b>{{ $result[0]['department_name'] ?? '' }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" style="text-align: left !important;font-size:16px;">Employee Code:
                                    <b>{{ $result[0]['finger_print_id'] ?? '' }}</b>
                                </td>
                                <td colspan="4" style="text-align: left !important;font-size:16px;">Designation:
                                    <b>{{ $result[0]['designation_name'] ?? '' }}</b>
                                </td>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <th>@lang('common.serial')</th>
                                <th>@lang('common.date')</th>
                                <th>@lang('attendance.shift')</th>
                                <th>@lang('attendance.in_time')</th>
                                <th>@lang('attendance.out_time')</th>
                                <th>@lang('attendance.working_time')</th>
                                <th>@lang('attendance.over_time')</th>
                                <th>@lang('attendance.permission_duration')</th>
                                <th>@lang('common.status')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $present = 0;
                            $publicHoliday = 0;
                            $weeklyHoliday = 0;
                            $absence = 0;
                            $fullLeave = 0;
                            $halfLeave = 0;
                            $workHour = [];
                            $otHour = [];
                            $permissionHour = [];
                            ?>

                            @foreach ($result as $row => $value)
                                <tr>
                                    <td>{{ $serial = ++$row }}</td>
                                    <td>{{ $value['date'] }}</td>
                                    <td>
                                        @if ($value['shift_name'] != '')
                                            {{ $value['shift_name'] }}
                                        @else
                                            {{ '--' }}
                                        @endif
                                    </td>
                                    <td>

                                        @if ($value['in_time'] != '')
                                            {{ $value['in_time'] }}
                                        @else
                                            {{ '--' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($value['out_time'] != '')
                                            {{ $value['out_time'] }}
                                        @else
                                            {{ '--' }}
                                        @endif
                                    </td>

                                    <td>

                                        @if ($value['working_time'] != '')
                                            {{ $value['working_time'] }}
                                            @php $workHour[] = $value['working_time']; @endphp
                                        @else
                                            {{ '--' }}
                                        @endif

                                    </td>
                                    <td>
                                        @if ($value['approved_over_time'] != '')
                                            {{ $value['approved_over_time'] }}
                                            @php $otHour[] = $value['approved_over_time']; @endphp
                                        @else
                                            {{ '--' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($value['permission_duration'] == null)
                                            {{ '--' }}
                                        @else
                                            {{ date('H:i', strtotime($value['permission_duration'])) }}
                                            @php $permissionHour[] = $value['permission_duration']; @endphp
                                        @endif
                                    </td>
                                    <td>
                                        @if ($value['action'] == 'Absence')
                                            {{ __('common.absence') }}
                                            @php $absence+= 1; @endphp
                                        @elseif ($value['action'] == 'FullDayLeave')
                                            {{ __('common.full_day_leave') }}
                                            @php $fullLeave+= 1; @endphp
                                        @elseif ($value['action'] == 'HalfDayLeave')
                                            {{ __('common.half_day_leave') }}
                                            @php $halfLeave+= 1; @endphp
                                        @elseif ($value['action'] == 'PublicHoliday')
                                            {{ 'Public Holiday' }}
                                            @php $publicHoliday+= 1; @endphp
                                        @elseif ($value['action'] == 'WeeklyHoliday')
                                            {{ 'Weekly Holiday' }}
                                            @php $weeklyHoliday+= 1; @endphp
                                        @elseif($value['action'] == 'Present')
                                            {{ __('common.present') }}
                                            @php $present+= 1; @endphp
                                        @elseif($value['action'] == 'HalfDayPresent')
                                            {{ __('common.half_day_present') }}
                                            @php $present+= 0.5; @endphp
                                        @else
                                            {{ '' }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr style="font-weight: bold">
                                <td colspan="6">
                                <td colspan="2">{{ 'Total Present' }}</td>
                                <td>{{ $present }}</td>
                            </tr>
                            <tr style="font-weight: bold">
                                <td colspan="6">
                                <td colspan="2">{{ 'Total Absent' }}</td>
                                <td>{{ $absence }}</td>
                            </tr>
                            <tr style="font-weight: bold">
                                <td colspan="6">
                                <td colspan="2">{{ 'Total Leave' }}</td>
                                <td>{{ $fullLeave + $halfLeave }}</td>
                            </tr>
                            <tr style="font-weight: bold">
                                <td colspan="6">
                                <td colspan="2">{{ 'Total Weekly Holiday' }}</td>
                                <td>{{ $weeklyHoliday }}</td>
                            </tr>
                            <tr style="font-weight: bold">
                                <td colspan="6">
                                <td colspan="2">{{ 'Total Public Holiday' }}</td>
                                <td>{{ $publicHoliday }}</td>
                            </tr>
                            <tr style="font-weight: bold">
                                <td colspan="6">
                                <td colspan="2">{{ 'Total Permission Hour' }}</td>
                                <td>{{ sumTimeArr($permissionHour) }}</td>
                            </tr>
                            <tr style="font-weight: bold">
                                <td colspan="6">
                                <td colspan="2">{{ 'Total Working Hour' }}</td>
                                <td>{{ sumTimeArr($workHour) }}</td>
                            </tr>
                            <tr style="font-weight: bold">
                                <td colspan="6">
                                <td colspan="2">{{ 'Total OT Hour' }}</td>
                                <td>{{ sumTimeArr($otHour) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="page-break-after: always;"></div>
                @endif
            @endforeach
        </div>
    </div>
</body>

</html>
