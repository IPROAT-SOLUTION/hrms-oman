<div class="table-responsive">
    <table id="socialSecuritySummaryTable" class="table table-bordered manage-u-table" style="white-space:nowrap">
        <thead>
            <tr class="tr_header">
                <td>#</td>
                <th>@lang('common.name')</th>
                <th>@lang('common.finger_id')</th>
                <th>@lang('common.branch')</th>
                <th>@lang('common.department')</th>
                <th>@lang('employee.designation')</th>
                @foreach ($yearToMonth as $head)
                    <th>{{ date('M', strtotime($head)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $sl = 0;
            @endphp
            @foreach ($results as $key => $value)
                <tr>
                    <td>{{ $sl + 1 }}</td>
                    <th>{{ $value->fullName() }}</th>
                    <th>{{ $value->finger_id }}</th>
                    <th>{{ $value->branchName() }}</th>
                    <th>{{ $value->departmentName() }}</th>
                    <th>{{ $value->designationName() }}</th>
                    @foreach ($salary['salary_details'][$value->employee_id] as $key1 => $head)
                        <th>
                            <span class="text-success">{{ $head->social_security ?? '--' }}
                            </span><br>
                            <span class="text-info">{{ $head->employer_contribution ?? '--' }}</span>
                        </th>
                    @endforeach

                </tr>
                @php
                    $sl = $sl + 1;
                @endphp
            @endforeach
        </tbody>
    </table>
</div>
