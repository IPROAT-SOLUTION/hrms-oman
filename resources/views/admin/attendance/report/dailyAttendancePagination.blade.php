<div class="table-responsive">
    <div style="width:100%;">
        <h3 style="margin-top: 10px;text-align: center;"><b>Daily Attendance Report {{ "($date)" }}</b></h3>
    </div>
    <table class="table table-bordered" style="font-size: 12px;">
        <thead class="tr_header bg-title">
            <tr>
                <th style="width:50px;">@lang('common.serial')</th>
                <th style="font-size:12px;">@lang('common.date')</th>
                <th style="font-size:12px;">@lang('common.employee_name')</th>
                <th style="font-size:12px;">@lang('common.id')</th>
                <th style="font-size:12px;">@lang('attendance.department')</th>
                <th style="font-size:12px;">@lang('attendance.shift')</th>
                <th style="font-size:12px;">@lang('attendance.in_time')</th>
                <th style="font-size:12px;">@lang('attendance.out_time')</th>
                <th style="font-size:12px;">@lang('attendance.duration')</th>
                <th style="font-size:12px;">@lang('attendance.early_by')</th>
                <th style="font-size:12px;">@lang('attendance.late_by')</th>
                <th style="font-size:12px;">@lang('attendance.permission_duration')</th>
                <th style="font-size:12px;">@lang('attendance.history_of_records')</th>
                <th style="font-size:12px;;">@lang('attendance.status')</th>
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
                    <tr class="text-center">
                        <td style="font-size:12px;">{{ ++$sl }}</td>
                        <td style="font-size:12px;">{{ $value->date }}</td>
                        <td style="font-size:12px;">{{ $value->fullName }}</td>
                        <td style="font-size:12px;">{{ $value->finger_print_id }}</td>
                        <td style="font-size:12px;">{{ $dept }}</td>
                        <td style="font-size:12px;">{{ $value->shift_name ?? 'N/A' }}</td>
                        <td style="font-size:12px;">
                            @php
                                if ($value->in_time != '') {
                                    echo $value->in_time;
                                } else {
                                    echo $zero;
                                }
                            @endphp
                        </td>
                        <td style="font-size:12px;">
                            @php
                                if ($value->out_time != '') {
                                    echo $value->out_time;
                                } else {
                                    echo $zero;
                                }
                            @endphp
                        </td>
                        <td style="font-size:12px;">
                            @php
                                if ($value->working_time != null) {
                                    echo date('H:i', strtotime($value->working_time));
                                } else {
                                    echo $zero;
                                }
                            @endphp
                        </td>
                        <td style="font-size:12px;">
                            @php
                                if ($value->early_by != null) {
                                    echo date('H:i', strtotime($value->early_by));
                                } else {
                                    echo $zero;
                                }
                            @endphp
                        </td>
                        <td style="font-size:12px;">
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

                        <td style="font-size:12px;">
                            @php
                                if ($value->in_out_time != null) {
                                    echo $value->in_out_time;
                                } else {
                                    echo $zero;
                                }
                            @endphp
                        </td>

                        <td style="font-size:12px;">
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
