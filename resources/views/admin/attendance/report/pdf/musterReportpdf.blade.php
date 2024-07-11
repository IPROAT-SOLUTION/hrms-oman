<!DOCTYPE html>
<html lang="en">

<head>
    <title> @lang('attendance.muster_attendance')</title>
    <meta charset="utf-8">
</head>
<style>
    .printHead {
        width: 100%;
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    th {
        background-color: #D1D1D1;
    }

    tr:nth-child(even) {
        background-color: #ffffff;
    }
</style>

<body>
    <div class="container">
        <div style="text-align: center;width:100%;text-align:center">
            <h3 style="margin-top: 10px;"><b>Muster Report</b></h3>
        </div>

        <div class="text-center" style="text-align: center;width:100%;text-align:center;font-weight:bold">
            <p class="text-center font-bold" style="text-decoration: underline;">
                @if (isset($from_date) && isset($to_date))
                    @if (date('Y-m', strtotime(dateConvertFormToDB($from_date))) == date('Y-m', strtotime(dateConvertFormToDB($to_date))))
                        <p class="col-md-12 text-center font-bold">
                            <span>{{ 'Month - ' . date('F', strtotime(dateConvertFormToDB($from_date))) . ' ' }}</span>
                            <span>{{ '(' . (dateConvertFormToDB($from_date) ?: '') . ' to ' . (dateConvertFormToDB($to_date) ?: '') . ')' }}</span>
                        </p>
                    @else
                        {{ (date('F d', strtotime(dateConvertFormToDB($from_date))) ?: '') . ' to ' . (date('F d', strtotime(dateConvertFormToDB($to_date))) ?: '') }}
                    @endif
                @endif
            </p>
            <p><span style="color: green">P</span>- Present, <span style="color: red">AA</span>- Absent,
                <span style="color: #F6BE00">WH</span style="color: #F6BE00">- Weekly Holiday, <span
                    style="color: #F6BE00">PH</span>- Public
                Holiday, <span style="color: blue">FL</span>-
                Full Day Leave, <span style="color: blue">HL</span>- Half Day Leave, <span
                    style="color: green">Other</span>- Shift Short Name.
            </p>
        </div>
        <div class="table-responsive">
            <table id="dailyAttendance" class="table table-bordered"
                style="font-size: 12px; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #D1D1D1;font-weight:bold">
                        <td>@lang('common.serial')</td>
                        <td>@lang('common.branch')</td>
                        <td>@lang('common.id')</td>
                        <td>@lang('common.name')</td>
                        <td>@lang('common.department')</td>
                        <td>@lang('common.title')</td>
                        @foreach ($monthToDate as $head)
                            <td>{{ $head['day'] . '/' . $head['day_name'] }}</td>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    {{ $sl = null }}
                    @foreach ($results as $key => $attendance)
                        <tr>
                            @foreach ($attendance as $column => $value)
                                @if ($key % 5 === 0)
                                    <td
                                        style="color: {{ getColorForAttendance($attendance[$column], $shift_name) }};font-weight:bold;">
                                        {{ $attendance[$column] }}
                                    </td>
                                @else
                                    <td>{{ $attendance[$column] }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
