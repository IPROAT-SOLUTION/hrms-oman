<div class="table-responsive">
    <table id="socialSecurityTable" class="table table-bordered manage-u-table" style="white-space:nowrap">
        <thead>
            <tr class="tr_header">
                <th>@lang('common.serial')</th>
                <th>@lang('common.month')</th>
                <th>@lang('employee.name')</th>
                <th>@lang('socialSecurity.finger_id')</th>
                <th>@lang('socialSecurity.branch')</th>
                <th>@lang('socialSecurity.department')</th>
                <th>@lang('socialSecurity.designation')</th>
                <th>Employee Contribution</th>
                <th>Employer Contribution</th>
            </tr>
        </thead>
        <tbody>
            {!! $sl = null !!}
            @foreach ($results as $value)
                <tr class="{!! $value->salary_details_id !!}">
                    <td style="width: 100px;">{!! ++$sl !!}</td>
                    <td>{!! $value->month_of_salary !!}</td>
                    <td>{!! $value->employee->fullname() !!}</td>
                    <td>{!! $value->employee->finger_id !!}</td>
                    <td>{!! $value->employee->branchName() !!}</td>
                    <td>{!! $value->employee->departmentName() !!}</td>
                    <td>{!! $value->employee->designationName() !!}</td>
                    <td>{!! number_format($value->social_security, 2, '.', '') !!}</td>
                    <td>{!! number_format($value->employer_contribution, 2, '.', '') !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
