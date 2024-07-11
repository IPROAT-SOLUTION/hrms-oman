<style>
    table {
        margin: 0 0 40px 0;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        display: table;
        border-collapse: collapse;
    }

    .printHead {
        width: 35%;
        margin: 0 auto;
    }

    table,
    td,
    th {
        font-size: 10px;
        border: 1px solid black;
    }

    td {
        font-size: 8px;
        padding: 3px;
    }

    th {
        padding: 3px;
    }

    .present {
        color: #7ace4c;
        font-weight: 700;
    }

    .absence {
        color: #f33155;
        font-weight: 700;
    }

    .leave {
        color: #41b3f9;
        font-weight: 700;
    }

    .bolt {
        font-weight: 700;
    }
</style>

<body style="word-wrap: break-word; font-family: Arial, sans-serif;">
    <div class="printHead" style="text-align: center;">
        <h3 style="margin-top: 10px;"><b>Muster Report</b></h3>
    </div>

    <div class="row text-center">
        <p class="font-bold">P- Present, AA- Absent, WH- Weekly Holiday, PH- Public Holiday, FL-
            Full Day Leave, HL- Half Day Leave, Other- Shift Short Name.</p>
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
    </div>

    <div class="table-responsive">
        <table id="musterAttendance" class="table table-bordered table-hover manage-u-table" style="font-size: 12px;">
            <thead>
                <tr class="tr_header">
                    <th style="width: 32px">@lang('common.serial')</th>
                    <th style="width: 100px">@lang('common.branch')</th>
                    <th style="width: 100px">@lang('common.id')</th>
                    <th style="width: 100px">@lang('common.name')</th>
                    <th style="width: 100px">@lang('common.department')</th>
                    <th style="width: 100px">@lang('common.title')</th>
                    @foreach ($monthToDate as $head)
                        <th>{{ $head['day'] . '/' . $head['day_name'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{ $sl = null }}
                @foreach ($results as $fingerID => $attendance)
                    <tr rowspan="5">

                        <td>{{ ++$sl }}</td>
                        <td>{{ $attendance[0]['branch_name'] }}</td>
                        <td>{{ $fingerID }}</td>
                        <td>{{ $attendance[0]['fullName'] }}</td>
                        <td>{{ $attendance[0]['department_name'] }}</td>
                        <td class="text-center">
                            {{ 'Shift Name' }}
                            <br>
                            {{ 'In Time' }}
                            <br>
                            {{ 'Out Time' }}
                            <br>
                            {{ 'Working.Hrs' }}
                            <br>
                            {{ 'Over Time' }}
                            <br>
                        </td>

                        @foreach ($attendance as $data)
                            @if (strtotime($data['date']) <= strtotime(date('Y-m-d')))
                                <td class="text-center" style="width:250px;">
                                    {{ $data['shift_name'] != null ? $data['shift_name'] : $data['attendance_status'] . ($data['leave_type'] ? '(' . $data['leave_type'] . ')' : '') }}
                                    <br>
                                    {{ $data['in_time'] != null ? date('H:i', strtotime($data['in_time'])) : '-:-' }}
                                    <br>
                                    {{ $data['out_time'] != null ? date('H:i', strtotime($data['out_time'])) : '-:-' }}
                                    <br>
                                    {{ $data['working_time'] != null ? date('H:i', strtotime($data['working_time'])) : '-:-' }}
                                    <br>
                                    {{ $data['approved_over_time'] != null ? date('H:i', strtotime($data['approved_over_time'])) : '-:-' }}
                                    <br>
                                </td>
                            @else
                                <td></td>
                            @endif
                        @endforeach

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
