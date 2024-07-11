{{-- <div class="row">
    <div class="col-md-12">
        <p class="font-bold text-center">
            @foreach ($leaveTypes as $leaveType)
                <span>{{ acronym($leaveType->leave_type_name) . '-' . ucwords($leaveType->leave_type_name) . ($loop->last ? '.' : ',') }}</span>
            @endforeach
        </p>
    </div>
</div> --}}
<div class="printHead">
    <p style="font-size: 18px;text-align:center"><b>Month {{ "($from_date to $to_date)" }}</b></p>
</div>
<div class="table-responsive">
    <table id="leaveSummaryReport" class="table table-bordered" style="white-space: nowrap">
        <thead class="tr_header">
            <tr>
                <th class="col-md-1 text-center">@lang('common.month')</th>
                <th class="text-center">{{ 'Employee ID' }}</th>
                <th class="text-center">{{ 'Name' }}</th>
                <th class="text-center">{{ 'Department' }}</th>
                <th class="text-center">{{ 'Designation' }}</th>
                @foreach ($leaveTypes as $leaveType)
                    <th class="col-md-1 text-center">{{ acronym($leaveType->leave_type_name) }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $value)
                <tr>
                    <td class="col-md-1 text-center">{{ $value['month_name'] }}</td>
                    <th class="text-center">{{ $results[0]['finger_id'] }}</th>
                    <th class="text-center">{{ $results[0]['full_name'] }}</th>
                    <th class="text-center">{{ $results[0]['department_name'] }}</th>
                    <th class="text-center">{{ $results[0]['designation_name'] }}</th>
                    @foreach ($value['leaveType'] as $key => $noOfDays)
                        @if ($noOfDays != '')
                            <td class="col-md-1 text-center">{{ $noOfDays }}</td>
                        @else
                            <td class="col-md-1 text-center">{{ '0' }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
