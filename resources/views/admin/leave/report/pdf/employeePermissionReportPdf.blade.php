<!DOCTYPE html>
<html lang="en">

<head>
    <title>@lang('leave.employee_permission_report')</title>
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
        background-color: #f2f2f2;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    /* table {
        text-align: left;
        line-height: 40px;
        border-collapse: separate;
        border-spacing: 0;
        border: 2px solid #ed1c40;
        width: 100%;
        margin: 50px auto;
        border-radius: .25rem;
    }

    thead tr:first-child {
        background: #ed1c40;
        color: #fff;
        border: none;
    }

    th:first-child,
    td:first-child {
        padding: 0 15px 0 20px;
    }

    th {
        font-weight: 500;
    }

    thead tr:last-child th {
        border-bottom: 3px solid #ddd;
    }

    tbody tr:hover {
        background-color: #f2f2f2;
        cursor: default;
    }

    tbody tr:last-child td {
        border: none;
    }

    tbody td {
        border-bottom: 1px solid #ddd;
    }

    td:last-child {
        text-align: center;
        padding-right: 10px;
    } */
</style>

<body>
    <div class="container">
        <div class="printHead">
            <p style="font-size: 18px"><b>Permission Report {{ "($from_date to $to_date)" }}</b></p>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:100px;">@lang('common.serial')</th>
                        <th>@lang('leave.employee')</th>
                        <th>@lang('leave.employee_id')</th>
                        <th>@lang('leave.department')</th>
                        <th>@lang('leave.applied_date')</th>
                        <th>@lang('leave.permission_duration')</th>
                        <th>@lang('leave.approve_date')</th>
                        <th>@lang('leave.purpose')</th>
                        <th>@lang('leave.manager_remarks')</th>
                        <th>@lang('leave.hr_remarks')</th>
                        <th>@lang('leave.duration')</th>
                    </tr>
                </thead>
                <tbody>
                    {{ $sl = null }}
                    @forelse ($results as $value)
                        <tr>
                            <td>{{ ++$sl }}</td>
                            <td>{{ $value->employee->first_name . ' ' . $value->employee->last_name }}
                            </td>
                            <td>{{ $value->employee->finger_id }}</td>
                            @php
                                $dept = App\Model\Department::where(
                                    'department_id',
                                    $value->employee->department_id,
                                )->first();
                            @endphp
                            <td>{{ $dept ? $dept->department_name : 'NA' }}
                            </td>

                            <td>{{ dateConvertDBtoForm($value->leave_permission_date) }}</td>
                            <td>{{ $value->from_time }}
                                <b>to</b>
                                {{ $value->to_time }}
                            </td>

                            <td>{{ dateConvertDBtoForm($value->approve_date) }}</td>
                            <td width="300px;word-wrap: break-word">
                                {{ $value->leave_permission_purpose }}</td>
                            <td>
                                <span class="text-muted">
                                    @if (isset($value->manager_remarks))
                                        {!! $value->manager_remarks !!}
                                    @else
                                        {{ '-' }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">
                                    @if (isset($value->remarks))
                                        {!! $value->remarks !!}
                                    @else
                                        {{ '-' }}
                                    @endif
                                </span>
                            </td>
                            <td>{{ $value->permission_duration }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" style="text-align:center">@lang('common.no_data_available')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
