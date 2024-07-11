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
            @if (count($results) > 0)
                {{ $sl = null }}
                @foreach ($results as $value)
                    <tr>
                        <td>{{ ++$sl }}</td>
                        <td>{{ $value->employee->first_name . ' ' . $value->employee->last_name }}
                        </td>
                        <td>{{ $value->employee->finger_id }}</td>
                        <td>{{ $value->employee->department->department_name ?? '-' }}
                        </td>

                        <td>{{ dateConvertDBtoForm($value->leave_permission_date) }}</td>
                        <td>{{ $value->from_time }} <br>
                            <b>to</b> <br>
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
                @endforeach
            @else
                <tr>
                    <td colspan="10">@lang('common.no_data_available') !</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
