@extends('admin.master')
@section('content')
@section('title')
@lang('attendance.attendance_detailed_report')
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
        font-weight: 700;
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
        $("#attendanceMusterReport").validate();
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
                                    'route' => 'attendanceMusterReport.attendanceMusterReport',
                                    'id' => 'attendanceMusterReport',
                                ]) }}
                                <br>
                                <div class="row col-md-offset-1">
                                    {{-- <div class="col-md-3 col-sm-3" hidden>
                                        <div class="form-group">
                                            <label class="control-label" for="branch_id">@lang('common.branch'):</label>
                                            <select name="branch_id" class="form-control branch_id  select2">
                                                <option value="">--- @lang('common.all') ---</option>
                                                @foreach ($branchList as $value)
                                                <option value="{{ $value->branch_id }}" @if ($value->branch_id == $branch_id) {{ 'selected' }} @endif>
                                                    {{ $value->branch_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-3 col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label" for="department_id">@lang('common.department'):</label>
                                            <select name="department_id" class="form-control department_id  select2">
                                                <option value="">--- @lang('common.all') ---</option>
                                                @foreach ($departmentList as $value)
                                                <option value="{{ $value->department_id }}" @if ($value->department_id == $department_id) {{ 'selected' }} @endif>
                                                    {{ $value->department_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-3 col-sm-3" hidden>
                                        <div class="form-group">
                                            <label class="control-label" for="employee_id">@lang('common.employee'):</label>
                                            <select name="employee_id" class="form-control employee_id  select2">
                                                <option value="">--- @lang('common.all') ---</option>
                                                @foreach ($employeeList as $value)
                                                <option value="{{ $value->employee_id }}" @if ($value->employee_id == $employee_id) {{ 'selected' }} @endif>
                                                    {{ $value->first_name . ' ' . $value->last_name . '(' . $value->finger_id . ')' }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-3 col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label" for="email">@lang('common.from_date')<span
                                                    class="validateRq">*</span></label>
                                            <input type="text" class="form-control dateField from_date required"
                                                readonly placeholder="@lang('common.from_date')" name="from_date"
                                                value="@if (isset($from_date)) {{ $from_date }}@else {{ dateConvertDBtoForm(date('Y-m-01')) }} @endif">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label" for="email">@lang('common.to_date')<span
                                                    class="validateRq">*</span></label>
                                            <input type="text" class="form-control dateField to_date required"
                                                readonly placeholder="@lang('common.to_date')" name="to_date"
                                                value="@if (isset($to_date)) {{ $to_date }}@else {{ dateConvertDBtoForm(date('Y-m-t', strtotime(date('Y-m-01')))) }} @endif">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2">
                                        <div class="form-group">
                                            <input type="submit" id="filter" style="margin-top: 28px;width:100px" class="btn btn-instagram" value="@lang('common.filter')">
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        {{-- @if (count($results) > 0 && $results != '')
                            <h4 class="text-right" hidden>
                                <a class="btn btn-success download-csv" style="color: #fff"
                                    href="{{ URL('downloadMusterAttendanceExcel/?employee_id=' . $employee_id . '&from_date=' . $from_date . '&to_date=' . $to_date . '&department_id=' . $department_id . '&branch_id=' . $branch_id) }}">CSV</a>
                                <a class="btn btn-success" style="color: #fff"
                                    href="{{ URL('downloadMusterAttendancePdf/?employee_id=' . $employee_id . '&from_date=' . $from_date . '&to_date=' . $to_date . '&department_id=' . $department_id . '&branch_id=' . $branch_id) }}"><i
                                        class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download')
                                    PDF</a>
                            </h4>
                        @endif --}}
                        <div class="row text-center">
                            <p class="text-center font-bold" style="text-decoration: underline;">
                                @if (isset($from_date) && isset($to_date))
                                    @if (date('Y-m', strtotime(dateConvertFormToDB($from_date))) == date('Y-m', strtotime(dateConvertFormToDB($to_date))))
                                        <p class="col-md-12 text-center font-bold">
                                            <span>{{ 'Month - ' . date('F', strtotime(dateConvertFormToDB($from_date))) . ' ' }}</span>
                                            <span>{{ '(' . (dateConvertFormToDB($from_date) ?: '') . ' to ' . (dateConvertFormToDB($to_date) ?: '') . ')' }}</span>
                                        </p>
                                    @else
                                        {{ (date('F d', strtotime(dateConvertFormToDB($from_date))) ?: '') . ' to ' . (date('F d', strtotime(dateConvertFormToDB($to_date))) ?: '') }}
                                    @endif
                                @endif
                            </p>
                            <p class="text-center font-bold"><span style="color: green">P</span>- Present, <span
                                    style="color: red">AA</span>- Absent,
                                <span style="color: #F6BE00">WH</span style="color: #F6BE00">- Weekly Holiday, <span
                                    style="color: #F6BE00">PH</span>- Public
                                Holiday, <span style="color: blue">FL</span>-
                                Full Day Leave, <span style="color: blue">HL</span>- Half Day Leave, <span
                                    style="color: green">Other</span>- Shift Short Name.
                            </p>

                        </div>

                        <div class="table-responsive">
                            <table id="musterAttendance" class="table table-bordered table-hover manage-u-table"
                                style="font-size: 12px;">
                                <thead>
                                    <tr class="tr_header">
                                        <th style="width: 32px">@lang('common.serial')</th>
                                        <th style="width: 100px">@lang('common.branch')</th>
                                        <th style="width: 100px">@lang('common.id')</th>
                                        <th style="width: 100px">@lang('common.name')</th>
                                        <th style="width: 100px">@lang('common.department')</th>
                                        <th style="width: 100px">@lang('common.title')</th>
                                        @foreach ($monthToDate as $head)
                                            <th>{{ $head['day'] . '/' . $head['day_name'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    {{ $sl = null }}
                                    @foreach ($results as $fingerID => $attendance)
                                    <tr rowspan="5">

                                            <td>{{ ++$sl }}</td>
                                            <td>{{ $attendance[0]['branch_name'] }}</td>
                                            <td>{{ $fingerID }}</td>
                                            <td>{{ $attendance[0]['fullName'] }}</td>
                                            <td>{{ $attendance[0]['department_name'] }}</td>
                                            <td class="text-center">
                                                {{ 'Shift Name' }}
                                                <br>
                                                {{ 'In Time' }}
                                                <br>
                                                {{ 'Out Time' }}
                                                <br>
                                                {{ 'Working.Hrs' }}
                                                <br>
                                                {{ 'Over Time' }}
                                                <br>
                                            </td>

                                            @foreach ($attendance as $data)
                                                @if (strtotime($data['date']) <= strtotime(date('Y-m-d')))
                                                    <td class="text-center" style="width:250px;">
                                                        <span
                                                            style="color: {{ getColorForAttendance($data['shift_name'] != null ? $data['shift_name'] : $data['attendance_status'], $shift_name) }};font-weight:bold;">
                                                            {{ $data['shift_name'] != null ? $data['shift_name'] : $data['attendance_status'] }}
                                                        </span>
                                                        <br>
                                                        {{ $data['in_time'] != null ? date('H:i', strtotime($data['in_time'])) : '-:-' }}
                                                        <br>
                                                        {{ $data['out_time'] != null ? date('H:i', strtotime($data['out_time'])) : '-:-' }}
                                                        <br>
                                                        {{ $data['working_time'] != null ? date('H:i', strtotime($data['working_time'])) : '-:-' }}
                                                        <br>
                                                        {{ $data['approved_over_time'] != null ? date('H:i', strtotime($data['approved_over_time'])) : '-:-' }}
                                                        <br>
                                                    </td>
                                                @else
                                                    <td></td>
                                                @endif
                                            @endforeach

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
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#musterAttendance').DataTable({
            autoFill: true,
            ordering: false,
            processing: false,
            colReorder: false,
            keys: true,
            select: true,
            // scrollX: true,
            // scrollY: false,
            // paging: true,
            // scrollCollapse: true,
            // fixedColumns: false,
            // sScrollXInner: "100%",
            // autoWidth: true,
            select: {
                style: 'multi'
            },
            dom: 'lBfrtip',

            // dom: 'QlBfrtip',

            // columnDefs: [{
            //     searchBuilderTitle: 'DataTable',
            //     targets: [1]
            // }],

            // aLengthMenu: [
            //     [10, 25, 50, 100, 200, -1],
            //     [10, 25, 50, 100, 200, "All"]
            // ],

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
                $("#musterAttendance").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

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

        function downloadExcel() {
            $department_id = $('.department_id').val();
            $from_date = $('.from_date').val();
            $to_date = $('.to_date').val();
            window.location.href = "{{ url('/downloadMusterAttendanceExcel') }}" + "?from_date=" +
                $from_date + '&to_date=' +
                $to_date + '&department_id=' + $department_id;
        }

        function downloadPdf() {
            $department_id = $('.department_id').val();
            $from_date = $('.from_date').val();
            $to_date = $('.to_date').val();
            window.location.href = "{{ url('/downloadMusterReport') }}" + "?from_date=" +
                $from_date + '&to_date=' +
                $to_date + '&department_id=' + $department_id;
        }

    });
</script>
@endsection