@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.monthly_attendance_report')
@endsection
<style>
    .employeeName {
        position: relative;
    }

    #employee_id-error {
        position: absolute;
        top: 66px;
        left: 0;
        width: 100%he;
        width: 100%;
        height: 100%;
    }

    /*
  tbody {
   display:block;
   height:500px;
   overflow:auto;
  }
  thead, tbody tr {
   display:table;
   width:100%;
   table-layout:fixed;
  }
  thead {
   width: calc( 100% - 1em )
  }*/
</style>
<script>
    jQuery(function() {
        $("#monthlyAttendance").validate();
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
                                {{ Form::open(['route' => 'monthlyAttendance.monthlyAttendance', 'id' => 'monthlyAttendance']) }}
                                <div class="col-md-2 col-sm-1"></div>
                                <div class="col-md-2">
                                    <div class="form-group employeeName">
                                        <label class="control-label" for="email">@lang('common.employee')<span
                                                class="validateRq">*</span></label>
                                        <select class="form-control employee_id select2 required" required
                                            name="employee_id">
                                            <option value="allData">@lang('common.all')</option>
                                            @foreach ($employeeList as $value)
                                                <option value="{{ $value->employee_id }}"
                                                    @if ($value->employee_id == $employee_id) {{ 'selected' }} @endif>
                                                    {{ $value->first_name }} {{ $value->last_name }}
                                                    {{ " ({$value->finger_id})" }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label" for="email">@lang('common.from_date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control dateField from_date required" readonly
                                            placeholder="@lang('common.from_date')" name="from_date"
                                            value="@if (isset($from_date)) {{ $from_date }}@else {{ dateConvertDBtoForm(date('Y-m-01')) }} @endif">
                                    </div>
                                </div>

                                <div class="col-md-2">
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
                                            class="btn btn-info " value="@lang('common.filter')">
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <hr>
                        <table id="dailyAttendance" class="table table-bordered" style="font-size: 12px">
                            <thead class="tr_header">
                                <tr>
                                    <th style="width:100px;">@lang('common.serial')</th>
                                    <th>@lang('common.date')</th>
                                    <th>@lang('attendance.shift')</th>
                                    <th>@lang('attendance.in_time')</th>
                                    <th>@lang('attendance.out_time')</th>
                                    <th>@lang('attendance.working_time')</th>
                                    <th>@lang('attendance.over_time')</th>
                                    <th>@lang('attendance.permission_duration')</th>
                                    <th>@lang('common.status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $result)
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
                                    <tr>
                                        <td>Employee Name:</td>
                                        <td>
                                            <b>{{ $result[0]['fullName'] ?? '' }}</b>
                                        </td>
                                        <td></td>
                                        <td> Department:</td>
                                        <td>
                                            <b>{{ $result[0]['department_name'] ?? '' }}</b>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Employee Code:</td>
                                        <td>
                                            <b>{{ $result[0]['finger_print_id'] ?? '' }}</b>
                                        </td>
                                        <td></td>
                                        <td>Designation:</td>
                                        <td>
                                            <b>{{ $result[0]['designation_name'] ?? '' }}</b>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    @foreach ($result as $value)
                                        <tr>
                                            <td style="width:100px;">{{ ++$serial }}</td>
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
                                                @if ($value['working_time'] == '')
                                                    {{ '--' }}
                                                @else
                                                    @if ($value['working_time'] != '00:00:00')
                                                        {{ date('H:i', strtotime($value['working_time'])) }}
                                                        @php $workHour[] = $value['working_time']; @endphp
                                                    @else
                                                        {{ 'One Time Punch' }} @endif
                                                    @endif
                                            </td>
                                            <td>
                                                @if ($value['approved_over_time'] == '')
                                                    {{ '--' }}
                                                @else
                                                    @if ($value['approved_over_time'] != '00:00:00')
                                                        {{ date('H:i', strtotime($value['approved_over_time'])) }}
                                                        @php $otHour[] = $value['approved_over_time']; @endphp
                                                    @else
                                                        {{ 'One Time Punch' }}
                                                    @endif
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
                                                @elseif($value['action'] == 'HalfDayPresent')
                                                    {{ __('common.half_day_present') }}
                                                    @php $present+= 0.5; @endphp
                                                @elseif($value['action'] == 'Present')
                                                    {{ __('common.present') }}
                                                    @php $present+= 1; @endphp
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
                                        <td>{{ 'Total OT Hour' }}</td>
                                        <td>{{ sumTimeArr($otHour) }}</td>
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
@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        var table = $('#dailyAttendance').DataTable({
            autoFill: true,
            ordering: false,
            processing: false,
            colReorder: false,
            aLengthMenu: [
                [10, 15, 30, 40, 80, 120],
                [10, 15, 30, 40, 80, 120]
            ],
            keys: true,
            select: true,
            select: {
                style: 'multi'
            },
            // state save to load faster
            bStateSave: true,
            fnStateSave: function(settings, data) {
                localStorage.setItem("monthlyAttendance_dataTables_state", JSON.stringify(data));
            },
            fnStateLoad: function(settings) {
                return JSON.parse(localStorage.getItem("monthlyAttendance_dataTables_state"));
            },
            // aLengthMenu: [
            //     [10, 25, 50, 100, 200, -1],
            //     [10, 25, 50, 100, 200, "All"]
            // ],
            dom: 'lBfrtip',
            // dom: 'QlBfrtip',
            columnDefs: [{
                // searchBuilderTitle: 'DataTable',
                targets: [1]
            }],


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

        });

        function downloadExcel() {
            $employee_id = $('.employee_id').val();
            $from_date = $('.from_date').val();
            $to_date = $('.to_date').val();
            window.location.href = "{{ url('/downloadMonthlyAttendanceExcel') }}" + "?from_date=" +
                $from_date + '&to_date=' +
                $to_date + '&employee_id=' + $employee_id;
        }

        function downloadPdf() {
            $employee_id = $('.employee_id').val();
            $from_date = $('.from_date').val();
            $to_date = $('.to_date').val();
            window.location.href = "{{ url('/downloadMonthlyAttendancePdf') }}" + "?from_date=" +
                $from_date + '&to_date=' +
                $to_date + '&employee_id=' + $employee_id;
        }

    });
</script>
@endsection
