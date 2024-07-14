@extends('admin.master')
@section('content')
@section('title')
    @lang('leave.leave_summary_report')
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

    .grid-container {
        display: grid;
        grid-template-columns: auto auto auto auto;
        grid-gap: 10px;
        background: #EDF1F5;
    }

    .grid-container>div {
        background-color: rgba(255, 255, 255, 0.8);
        text-align: center;
        font-size: 30px;
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
        $("#leaveReport").validate();
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
                                {{ Form::open(['route' => 'summaryReport.summaryReport', 'id' => 'leaveReport']) }}
                                <div class="col-md-1"></div>
                                <div class="col-md-3">
                                    <div class="form-group employeeName">
                                        <label class="control-label" for="email">@lang('common.employee_name')<span
                                                class="validateRq">*</span></label>
                                        <select class="form-control employee_id select2 required" required
                                            name="employee_id">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            @foreach ($employeeList as $value)
                                                <option value="{{ $value->employee_id }}"
                                                    @if ($value->employee_id == $employee_id) {{ 'selected' }} @endif>
                                                    {{ $value->first_name }} {{ $value->last_name }}
                                                    {{ " ($value->finger_id)" }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="email">@lang('common.from_month')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control monthField from_date required"
                                            readonly placeholder="@lang('common.from_date')" name="from_date"
                                            value="@if (isset($from_date)) {{ $from_date }}@else {{ date('Y-01') }} @endif">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="control-label" for="email">@lang('common.to_month')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control monthField to_date required" readonly
                                            placeholder="@lang('common.to_date')" name="to_date"
                                            value="@if (isset($to_date)) {{ $to_date }}@else {{ date('Y-m') }} @endif">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="submit" id="filter" style="margin-top: 25px; width: 100px;"
                                            class="btn btn-info" value="@lang('common.filter')">
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold text-center">
                                    @foreach ($leaveTypes as $leaveType)
                                        <span>{{ acronym($leaveType->leave_type_name) . '-' . ucwords($leaveType->leave_type_name) . ($loop->last ? '.' : ',') }}</span>
                                    @endforeach
                                </p>
                            </div>
                            <p class="font-bold text-center">@lang('common.month') {{ "($from_date to $to_date)" }}</p>
                        </div>

                        <div class="table-responsive">
                            <table id="leaveSummaryReport" class="table table-bordered" style="white-space: nowrap">
                                <thead class="tr_header">
                                    <tr>
                                        <th class="col-md-1 text-center">@lang('common.month')</th>
                                        <th class="text-center">@lang('employee.employee_id')</th>
                                        <th class="text-center">@lang('leave.employee_name')</th>
                                        <th class="text-center">@lang('leave.department')</th>
                                        <th class="text-center">@lang('leave.designation')</th>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    var table = $('#leaveSummaryReport').DataTable({
        ordering: false,
        dom: 'lBfrtip',
        buttons: [{
            title: 'Employee Leave Report',
            className: 'dt-button buttons-custom-csv buttons-html5',
            text: 'CSV',
            action: function(e, dt, node, config) {
                downloadExcel();
            }
        }, {
            title: 'Employee Leave Report',
            className: 'dt-button buttons-custom-pdf buttons-html5',
            text: 'PDF',
            action: function(e, dt, node, config) {
                downloadPdf();
            }
        }],

        initComplete: function(settings, json) {
            $("#leaveSummaryReport").wrap(
                "<div style='overflow:auto; width:100%;position:relative;'></div>");
        },

    });

    function downloadExcel() {
        $employee_id = $('.employee_id').val();
        $from_date = $('.from_date').val();
        $to_date = $('.to_date').val();
        window.location.href = "{{ url('/downloadSummaryReportExcel') }}" + "?from_date=" + $from_date +
            '&to_date=' + $to_date + '&employee_id=' + $employee_id;
    }

    function downloadPdf() {
        $employee_id = $('.employee_id').val();
        $from_date = $('.from_date').val();
        $to_date = $('.to_date').val();
        window.location.href = "{{ url('/downloadSummaryReport') }}" + "?from_date=" + $from_date +
            '&to_date=' + $to_date + '&employee_id=' + $employee_id;
    }
</script>
@endsection
