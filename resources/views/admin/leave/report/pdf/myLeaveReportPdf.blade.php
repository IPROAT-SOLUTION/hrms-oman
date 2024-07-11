<!DOCTYPE html>
<html lang="en">

<head>
    <title>@lang('leave.employee_leave_report')</title>
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
</style>

<body>
    <div class="container">
        <div class="printHead">
            <p class="text-center" style="font-size: 18px"><b>My Leave Report {{ "($from_date to $to_date)" }}</b></p>
        </div>
        <div class="table-responsive">
            <table id="employeeLeaveReport" class="table table-bordered manage-u-table">
                <thead class="tr_header">
                    <tr>
                        <th style="width:50px;">@lang('common.serial')</th>
                        <th>@lang('leave.employee')</th>
                        <th>@lang('leave.employee_id')</th>
                        <th>@lang('leave.department')</th>
                        <th>@lang('leave.leave_type')</th>
                        <th>@lang('leave.applied_date')</th>
                        <th>@lang('leave.request_duration')</th>
                        <th>@lang('leave.approve_by')</th>
                        <th>@lang('leave.approve_date')</th>
                        <th>@lang('leave.purpose')</th>
                        <th>@lang('leave.manager_remarks')</th>
                        <th>@lang('leave.hr_remarks')</th>
                        <th>@lang('leave.number_of_day')</th>
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
                            <td>
                                @if ($value->leaveType->leave_type_name)
                                    {{ $value->leaveType->leave_type_name }}
                                @endif
                            </td>
                            <td>{{ dateConvertDBtoForm($value->application_date) }}</td>
                            <td>{{ dateConvertDBtoForm($value->application_from_date) }}
                                <b>to</b>
                                {{ dateConvertDBtoForm($value->application_to_date) }}
                            </td>
                            <td>

                                @if ($value->approveBy->first_name != null)
                                    {{ $value->approveBy->first_name }}
                                    {{ $value->approveBy->last_name }}
                                @endif
                            </td>
                            <td>{{ dateConvertDBtoForm($value->approve_date) }}</td>
                            <td width="300px;word-wrap: break-word">{{ $value->purpose }}</td>
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
                            <td>{{ $value->number_of_day }}</td>
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
