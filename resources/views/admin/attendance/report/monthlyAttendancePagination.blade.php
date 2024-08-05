<div class="table-responsive">
    <div style="width:100%;">
        <h3 style="margin-top: 10px;text-align: center;"><b>Monthly Attendance Report
                {{ "($from_date-$to_date)" }}</b></h3>
    </div>
    <div class="container">
        <table class="table table-bordered" style="font-size: 12px;">
            <thead class="tr_header">
                <tr>
                    <th style="width:100px;"><b>@lang('common.serial')&nbsp;</b></th>
                    <th><b>@lang('common.name')&nbsp;</b></th>
                    <th><b>@lang('employee.employee_id')&nbsp;</b></th>
                    <th><b>@lang('common.date')&nbsp;</b></th>
                    <th><b>@lang('attendance.shift')&nbsp;</b></th>
                    <th><b>@lang('attendance.in_time')&nbsp;</b></th>
                    <th><b>@lang('attendance.out_time')&nbsp;</b></th>
                    <th><b>@lang('attendance.working_time')&nbsp;</b></th>
                    <th><b>@lang('attendance.over_time')&nbsp;</b></th>
                    <th><b>@lang('attendance.permission_duration')&nbsp;</b></th>
                    <th><b>@lang('common.status')&nbsp;</b></th>
                </tr>
            </thead>
            <tbody>
                @forelse($results AS $result)
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
                    $serial = null;
                    ?>
                    @forelse($result AS $value)
                        <tr>
                            <td>{{ ++$serial }}</td>

                            <td>
                                @if ($value['fullName'] != '')
                                    {{ $value['fullName'] }}
                                @else
                                    {{ '--' }}
                                @endif
                            </td>

                            <td>
                                @if ($value['finger_print_id'] != '')
                                    {{ $value['finger_print_id'] }}
                                @else
                                    {{ '--' }}
                                @endif
                            </td>

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
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ 'Total Present' }}</td>
                        <td>{{ $present }}</td>
                    </tr>
                    <tr style="font-weight: bold">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ 'Total Absent' }}</td>
                        <td>{{ $absence }}</td>
                    </tr>
                    <tr style="font-weight: bold">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ 'Total Leave' }}</td>
                        <td>{{ $fullLeave + $halfLeave }}</td>
                    </tr>
                    <tr style="font-weight: bold">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ 'Total Weekly Holiday' }}</td>
                        <td>{{ $weeklyHoliday }}</td>
                    </tr>
                    <tr style="font-weight: bold">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ 'Total Public Holiday' }}</td>
                        <td>{{ $publicHoliday }}</td>
                    </tr>
                    <tr style="font-weight: bold">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ 'Total Permission Hour' }}</td>
                        <td>{{ sumTimeArr($permissionHour) }}</td>
                    </tr>
                    <tr style="font-weight: bold">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ 'Total Working Hour' }}</td>
                        <td>{{ sumTimeArr($workHour) }}</td>
                    </tr>
                    <tr style="font-weight: bold">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ 'Total OT Hour' }}</td>
                        <td>{{ sumTimeArr($otHour) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
