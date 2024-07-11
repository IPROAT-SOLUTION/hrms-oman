<!DOCTYPE html>
<html lang="en">

<head>
    <title> @lang('attendance.daily_attendance')</title>
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

<body>
    <div class="container">
        <div style="text-align: center;">
            <h3 style="margin-top: 10px;"><b>Daily Attendance</b></h3>
            <p style="margin-top: 5px;">Date: {{ $date }}</p>
        </div>

        <div class="table-responsive">
            <table id="dailyAttendance" class="table table-bordered" style="font-size: 12px; border-collapse: collapse;">
                <thead class="bg-title" style="background-color: #DFDFDF; color: black;">
                    <tr class="tr_header bg-title">
                        <th>@lang('common.serial')</th>
                        <th>@lang('common.date')</th>
                        <th>@lang('common.employee_name')</th>
                        <th>@lang('common.id')</th>
                        <th>@lang('attendance.department')</th>
                        <th>@lang('attendance.shift')</th>
                        <th>@lang('attendance.in_time')</th>
                        <th>@lang('attendance.out_time')</th>
                        <th style="padding:2px 8px;width: 100px; border: 0.1px solid #000;;">@lang('attendance.duration')</th>
                        <th>@lang('attendance.early_by')</th>
                        <th>@lang('attendance.late_by')</th>
                        <th>@lang('attendance.permission_duration')</th>
                        {{-- <th>@lang('attendance.history_of_records')</th> --}}
                        <th>@lang('attendance.status')</th>
                    </tr>
                </thead>

                <tbody>
                    {{ $sl = null }}
                    @foreach ($results as $dept => $result)
                        @foreach ($result as $key => $value)
                            @php
                                $zero = '00:00';
                                $isHoliday = false;
                                $holidayDate = '';
                            @endphp
                            <tr>
                                <td>
                                    {{ ++$sl }}</td>
                                <td>
                                    {{ $value->date }}</td>
                                <td>
                                    {{ $value->fullName }}</td>
                                <td>
                                    {{ $value->finger_print_id }}</td>
                                <td>
                                    {{ $dept }}</td>
                                <td>
                                    {{ $value->shift_name ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        if ($value->in_time != '') {
                                            echo $value->in_time;
                                        } else {
                                            echo $zero;
                                        }
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        if ($value->out_time != '') {
                                            echo $value->out_time;
                                        } else {
                                            echo $zero;
                                        }
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        if ($value->working_time != null) {
                                            echo date('H:i', strtotime($value->working_time));
                                        } else {
                                            echo $zero;
                                        }
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        if ($value->early_by != null) {
                                            echo date('H:i', strtotime($value->early_by));
                                        } else {
                                            echo $zero;
                                        }
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        if ($value->late_by != null) {
                                            echo date('H:i', strtotime($value->late_by));
                                        } else {
                                            echo $zero;
                                        }
                                    @endphp
                                </td>

                                <td style="font-size:12px;">
                                    @php
                                        if ($value->permission_duration != null) {
                                            echo $value->permission_duration;
                                        } else {
                                            echo $zero;
                                        }
                                    @endphp
                                </td>

                                {{-- <td>
                                    @php
                                        if ($value->in_out_time != null) {
                                            echo $value->in_out_time;
                                        } else {
                                            echo $zero;
                                        }
                                    @endphp
                                </td> --}}

                                <td>
                                    <?php
                                    echo attStatus($value->attendance_status);
                                    ?>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
