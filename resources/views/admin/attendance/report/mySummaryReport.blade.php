@extends('admin.master')
@section('content')
@section('title')
    @lang('attendance.my_attendance_report')
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
                                {{ Form::open(['route' => 'myAttendanceReport.myAttendanceReport', 'id' => 'monthlyAttendance']) }}
                                <div class="col-md-2"></div>
                                <div class="col-md-2">
                                    <div class="form-group employeeName">
                                        <label class="control-label" for="email">@lang('common.employee')<span
                                                class="validateRq">*</span></label>
                                        <select class="form-control employee_id select2 required" required
                                            name="employee_id">
                                            @foreach ($employeeList as $value)
                                                <option value="{{ $value->employee_id }}">{{ $value->first_name }}
                                                    {{ $value->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label" for="email">@lang('common.from_date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control dateField required" readonly
                                            placeholder="@lang('common.from_date')" name="from_date"
                                            value="@if (isset($from_date)) {{ $from_date }}@else {{ dateConvertDBtoForm(date('Y-m-01')) }} @endif">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <label class="control-label" for="email">@lang('common.to_date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control dateField required" readonly
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
                        <hr>
                        <h4 class="text-right" hidden>
                            @if (isset($from_date))
                                @if (count($results) > 0)
                                    <a class="btn btn-success" style="color: #fff"
                                        href="{{ URL('downloadMyAttendance/?employee_id=' . $employee_id . '&from_date=' . $from_date . '&to_date=' . $to_date) }}"><i
                                            class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download')
                                        PDF</a>
                                @endif
                            @else
                                @if (count($results) > 0)
                                    <a class="btn btn-success" style="color: #fff"
                                        href="{{ URL('downloadMyAttendance/?employee_id=' . decrypt(session('logged_session_data.employee_id')) . '&from_date=' . dateConvertDBtoForm(date('Y-m-01')) . '&to_date=' . dateConvertDBtoForm(date('Y-m-t', strtotime(date('Y-m-01'))))) }}"><i
                                            class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download')
                                        PDF</a>
                                @endif
                            @endif
                        </h4>
                        <div class="table-responsive">
                            <table id="dailyAttendance" class="table table-bordered" style="font-size: 12px">
                                <thead class="tr_header">
                                    <tr>
                                        <th style="width:100px;">@lang('common.serial')</th>
                                        <th>@lang('common.date')</th>
                                        <th>@lang('attendance.in_time')</th>
                                        <th>@lang('attendance.out_time')</th>
                                        <th>@lang('attendance.working_time')</th>
                                        <th>@lang('attendance.over_time')</th>
                                        <th>@lang('attendance.permission_duration')</th>
                                        <th>@lang('common.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                    @foreach ($results as $value)
                                        <tr>
                                            <td style="width:100px;">{{ ++$serial }}</td>
                                            <td>{{ $value['date'] }}</td>
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
                                                        {{ 'One Time Punch' }}
                                                    @endif
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
                                        <td>{{ 'Total OT Hour' }}</td>
                                        <td>{{ sumTimeArr($otHour) }}</td>
                                    </tr>
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
        var table = $('#dailyAttendance').DataTable({
            autoFill: true,
            ordering: false,
            processing: false,
            colReorder: false,
            keys: true,
            select: true,
            select: {
                style: 'multi'
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

            buttons: ['csv', {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'A4',
                title: 'My Attendance',
                text: 'PDF',
                // customize: function(doc) { //costomization code for chnageing the column colour 
                //     var bodyRows = doc.content[1].table.body;
                //     for (var i = 1; i < bodyRows.length; i++) {
                //         if (bodyRows[i][4].text === "RI") {
                //             bodyRows[i][4].fillColor = '#ffb6c1';
                //         }
                //     }
                // }
                customize: function(doc) {
                    // Adjust alignment and width
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                        .length + 1).join('*').split('');
                    doc.defaultStyle.alignment = 'center';
                }


            }],

        });
        $("#excelexport").click(function(e) {
            //getting values of current time for generating the file name
            var dt = new Date();
            var day = dt.getDate();
            var month = dt.getMonth() + 1;
            var year = dt.getFullYear();
            var hour = dt.getHours();
            var mins = dt.getMinutes();
            var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
            //creating a temporary HTML link element (they support setting file names)
            var a = document.createElement('a');
            //getting data from our div that contains the HTML table
            var data_type = 'data:application/vnd.ms-excel';
            var table_div = document.getElementById('btableData');
            var table_html = table_div.outerHTML.replace(/ /g, '%20');
            a.href = data_type + ', ' + table_html;
            //setting the file name
            a.download = 'attendance_details_' + postfix + '.xls';
            //triggering the function
            a.click();
            //just in case, prevent default behaviour
            e.preventDefault();
        });


    });
</script>
@endsection
