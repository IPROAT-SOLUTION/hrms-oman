@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.attendance_summary_report')
@endsection
<style>
    .present {
        color: #7ace4c;
        font-weight: 700;
        cursor: pointer;
    }

    .absence {
        color: #f33155;
        font-weight: 700;
        cursor: pointer;
    }

    .leave {
        color: #41b3f9;
        font-weight: 700;
        cursor: pointer;
    }

    .bolt {
        font-weight: 300;
    }

    .dataTables_scrollHeadInner {
        width: 100% !important;
    }

    .dataTables_scrollHeadInner table {
        width: 100% !important;
    }
</style>

<script>
    jQuery(function() {
        $("#attendanceSummaryReport").validate();
    });
</script>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div id="searchBox">
                                {{ Form::open([
                                    'route' => 'attendanceSummaryReport.attendanceSummaryReport',
                                    'id' => 'attendanceSummaryReport',
                                ]) }}
                                <div class="col-md-3"></div>

                                <div class="col-md-3">
                                    <label class="control-label" for="email">@lang('common.from_date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control dateField from_date required" readonly
                                            placeholder="@lang('common.from_date')" name="from_date"
                                            value="@if (isset($from_date)) {{ $from_date }}@else {{ dateConvertDBtoForm(date('Y-m-01')) }} @endif">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="control-label" for="email">@lang('common.to_date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control dateField to_date required" readonly
                                            placeholder="@lang('common.to_date')" name="to_date"
                                            value="@if (isset($to_date)) {{ $to_date }}@else {{ dateConvertDBtoForm(date('Y-m-t', strtotime(date('Y-m-01')))) }} @endif">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="submit" id="filter" style="margin-top: 26px;"
                                            class="btn btn-info btn-md" value="@lang('common.filter')">
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <p class="font-bold">
                                    P- Present, AA- Absent, WH- Weekly Holiday, PH- Public
                                    Holiday, FL-
                                    Full Day Leave, HL- Half Day Leave, Other- Shift Short Name,
                                    @foreach ($leaveTypes as $key => $leaveType)
                                        <span>{{ acronym($leaveType->leave_type_name) . ' - ' . $leaveType->leave_type_name }}{{ $loop->last ? '.' : ',' }}</span>
                                    @endforeach
                                </p>
                                <hr>

                                <p class="text-center font-bold" style="text-decoration: underline;">
                                    @if (isset($from_date) && isset($to_date))
                                        @if (date('Y-m', strtotime(dateConvertFormToDB($from_date))) == date('Y-m', strtotime(dateConvertFormToDB($to_date))))
                                            <p class="text-center font-bold">
                                                <span>{{ 'Month - ' . date('F', strtotime(dateConvertFormToDB($from_date))) . ' ' }}</span>
                                                <span>{{ '(' . (dateConvertFormToDB($from_date) ?: '') . ' to ' . (dateConvertFormToDB($to_date) ?: '') . ')' }}</span>
                                            </p>
                                        @else
                                            {{ (date('F d', strtotime(dateConvertFormToDB($from_date))) ?: '') . ' to ' . (date('F d', strtotime(dateConvertFormToDB($to_date))) ?: '') }}
                                        @endif
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="summaryAttendance"
                                class="table table-bordered table-striped table-hover manage-u-table"
                                style="font-size: 12px;">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('employee.employee_id')</th>
                                        <th>@lang('common.name')</th>
                                        <th>@lang('employee.designation')</th>
                                        <th>@lang('employee.department')</th>
                                        @foreach ($monthToDate as $head)
                                            <th class="text-center">{{ $head['day'] . ' ' . $head['day_name'] }}</th>
                                        @endforeach
                                        <th>@lang('attendance.day_of_worked')</th>
                                        <th>@lang('attendance.ph')</th>
                                        @foreach ($leaveTypes as $leaveType)
                                            <th>{{ acronym($leaveType->leave_type_name) }}</th>
                                        @endforeach
                                        <th>@lang('attendance.total_paid_days')</th>
                                        <th>@lang('attendance.wh') </th>
                                        <th>@lang('attendance.total_days')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sl = null;
                                        $totalPresent = 0;
                                        $leaveData = [];
                                        $totalCol = 0;
                                        $totalWorkHour = 0;
                                        $totalWeeklyHoliday = 0;
                                        $totalGovtHoliday = 0;
                                        $totalAbsent = 0;
                                        $totalLeave = 0;
                                    @endphp
                                    @foreach ($results as $key => $value)
                                        <tr>
                                            <td>{{ ++$sl }}</td>
                                            <td>{{ $value[0]['finger_id'] }}</td>
                                            <td>{{ $value[0]['fullName'] }}</td>
                                            <td>{{ $value[0]['designation_name'] }}</td>
                                            <td>{{ $value[0]['department_name'] }}</td>
                                            @foreach ($value as $v)
                                                @php
                                                    if ($sl == 1) {
                                                        $totalCol++;
                                                    }
                                                    if ($v['attendance_status'] == 'present') {
                                                        $totalPresent++;
                                                        if ($v['shift_name'] != '' && $v['shift_name'] != null) {
                                                            $shiftName = acronym($v['shift_name']);
                                                        } else {
                                                            $shiftName = 'NA';
                                                        }

                                                        if ($v['inout_status'] == 'O') {
                                                            echo "<td class='text-center'><span style='color:black ;font-weight:bold'>" .
                                                                $v['inout_status'] .
                                                                '' .
                                                                $shiftName .
                                                                '</span></td>';
                                                        } else {
                                                            echo "<td class='text-center'><span style='color:black ;font-weight:bold'>" .
                                                                $shiftName .
                                                                '</span></td>';
                                                        }
                                                    } elseif ($v['attendance_status'] == 'absence') {
                                                        $totalAbsent++;
                                                        echo "<td class='text-center'><span style='color:'#D1D1D1';'>AA</span></td>";
                                                    } elseif ($v['attendance_status'] == 'leave') {
                                                        if ($v['day'] == 'FL') {
                                                            $totalLeave += 1;
                                                            $leaveData[$key][$v['leave_type']]['day'][] = 1;
                                                        }
                                                        if ($v['day'] == 'HL') {
                                                            $totalLeave += 0.5;
                                                            $leaveData[$key][$v['leave_type']]['day'][] = 0.5;
                                                        }

                                                        $leaveData[$key][$v['leave_type']][] = $v['leave_type'];

                                                        echo "<td class='text-center'><span style='color:black ;font-weight:bold'>" .
                                                            $v['day'] .
                                                            '(' .
                                                            acronym($v['leave_type']) .
                                                            ')' ??
                                                            'NA' . '</span></td>';
                                                    } elseif ($v['attendance_status'] == 'holiday') {
                                                        $totalWeeklyHoliday++;
                                                        echo "<td class='text-center'><span style='color:black ;font-weight:bold'>WH</span></td>";
                                                    } elseif ($v['attendance_status'] == 'publicHoliday') {
                                                        $totalGovtHoliday++;
                                                        echo "<td class='text-center'><span style='color: black ;font-weight:bold'>PH</span></td>";
                                                    } elseif ($v['attendance_status'] == 'left') {
                                                        echo "<td class='text-center'><span style='color:black ;font-weight:bold'></span></td>";
                                                    } else {
                                                        echo '<td></td>';
                                                    }
                                                @endphp
                                            @endforeach
                                            <td class='text-center'><span class="bolt">{{ $totalPresent }}</span>
                                            </td>
                                            <td class='text-center'><span class="bolt">{{ $totalGovtHoliday }}</span>
                                            </td>
                                            @foreach ($leaveTypes as $leaveType)
                                                <td class='text-center'>

                                                    @php
                                                        if ($sl == 1) {
                                                            $totalCol++;
                                                        }
                                                        if (isset($leaveData[$key][$leaveType->leave_type_name])) {
                                                            $c = array_sum(
                                                                $leaveData[$key][$leaveType->leave_type_name]['day'],
                                                            );
                                                        } else {
                                                            $c = 0;
                                                        }
                                                    @endphp
                                                    <span class="bolt">
                                                        {{ $c }}
                                                    </span>
                                                </td>
                                            @endforeach
                                            <td class='text-center'><span
                                                    class="bolt">{{ $totalPresent + $totalLeave + $totalGovtHoliday }}</span>
                                            </td>
                                            <td class='text-center'><span
                                                    class="bolt">{{ $totalWeeklyHoliday }}</span></td>
                                            <td class='text-center'><span
                                                    class="bolt">{{ $totalPresent + $totalWeeklyHoliday + $totalAbsent + $totalLeave + $totalGovtHoliday }}</span>
                                            </td>
                                            @php
                                                $totalPresent = 0;
                                                $totalWeeklyHoliday = 0;
                                                $totalAbsent = 0;
                                                $totalLeave = 0;
                                                $totalGovtHoliday = 0;
                                            @endphp
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        var table = $('#summaryAttendance').DataTable({
            autoFill: true,
            ordering: false,
            processing: false,
            colReorder: false,
            keys: true,
            select: true,
            select: {
                style: 'multi'
            },
            dom: 'lBfrtip',
            buttons: [{
                    text: 'CSV',
                    className: 'dt-button buttons-custom-csv buttons-html5',
                    action: function(e, dt, node, config) {
                        downloadExcel();
                    }
                },
                {
                    className: 'dt-button buttons-custom-pdf buttons-html5',
                    text: 'PDF',
                    action: function(e, dt, node, config) {
                        downloadPdf();
                    }
                }
            ],
            initComplete: function(settings, json) {
                $("#summaryAttendance").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
        });
    });

    function downloadExcel() {
        $from_date = $('.from_date').val();
        $to_date = $('.to_date').val();
        window.location.href = "{{ url('/downloadSummaryAttendanceExcel') }}" + "?from_date=" +
            $from_date + '&to_date=' +
            $to_date;
    }

    function downloadPdf() {
        $from_date = $('.from_date').val();
        $to_date = $('.to_date').val();
        window.location.href = "{{ url('/downloadSummaryAttendancePdf') }}" + "?from_date=" +
            $from_date + '&to_date=' +
            $to_date;
    }
</script>
@endsection
