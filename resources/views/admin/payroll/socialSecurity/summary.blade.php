@extends('admin.master')
@section('content')
@section('title')
    @lang('socialSecurity.socialSecurity_list')
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
                                {{ Form::open(['route' => 'socialSecuritySummaryReport.summary', 'id' => 'socialSecurityReport', 'method' => 'post']) }}
                                <div class="col-md-1"></div>
                                <div class="col-md-2">
                                    <label class="control-label" for="email">@lang('common.branch')</label>
                                    <div class="form-group">
                                        <select class="form-control branchName select2 " name="branch_id">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            @foreach ($branchList as $key => $value)
                                                <option value="{{ $value->branch_id }}"
                                                    @if (isset($_REQUEST['branch_id'])) @if ($_REQUEST['branch_id'] == $value->branch_id) {{ 'selected' }} @endif
                                                    @endif>
                                                    {{ $value->branch_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label" for="email">@lang('common.department')</label>
                                    <div class="form-group">
                                        <select class="form-control department_id select2 " name="department_id">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            @foreach ($departmentList as $key => $value)
                                                <option value="{{ $value->department_id }}"
                                                    @if (isset($_REQUEST['department_id'])) @if ($_REQUEST['department_id'] == $value->department_id) {{ 'selected' }} @endif
                                                    @endif>
                                                    {{ $value->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label" for="email">@lang('common.designation')</label>
                                    <div class="form-group">
                                        <select class="form-control department_id select2 " name="designation_id">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            @foreach ($designationList as $key => $value)
                                                <option value="{{ $value->designation_id }}"
                                                    @if (isset($_REQUEST['designation_id'])) @if ($_REQUEST['designation_id'] == $value->designation_id) {{ 'selected' }} @endif
                                                    @endif>
                                                    {{ $value->designation_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label" for="email">@lang('common.year')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="year" value="{{ $year }}"
                                            class="form-control yearPicker required" id="passing_year"
                                            placeholder="@lang('common.year')">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="submit" id="filter" style="margin-top: 25px; width: 100px;"
                                            class="btn btn-info " value="@lang('common.filter')">
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        @if (count($results) > 0)
                            <h4 class="text-right">

                                <a class="btn btn-success" style="color: #fff"
                                    href="{{ URL('downloadSocicalSecuritySummaryReport/' . $year . '/' . $_REQUEST['branch_id'] . '/' . $_REQUEST['department_id'] . '/' . $_REQUEST['designation_id']) }}"><i
                                        class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download')
                                </a>


                            </h4>
                        @endif

                        <div class="row">
                            <div class="text-center font-bold col-md-12" style="text-align: center;">
                                <p>
                                    <span class="bg-success text-success"
                                        style="padding: 2px 4px;margin:0 4px;">Color</span><span
                                        class="text-dark">Employee Contribution,</span>
                                    <span class="bg-info text-info"
                                        style="padding: 2px 4px;margin:0 4px;">Color</span><span
                                        class="text-dark">Employer Contribution</span>
                                </p>

                            </div>
                        </div>

                        @include('admin.payroll.socialSecurity.pagination')

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    $(function() {
        var table = $('#socialSecuritySummaryTable').DataTable({
            autoFill: true,
            ordering: false,
            processing: false,
            colReorder: false,
            keys: true,
            dom: 'lBfrtip',
            buttons: [{
                text: 'CSV',
                className: 'dt-button buttons-custom-csv buttons-html5',
                action: function(e, dt, node, config) {
                    downloadExcel();
                }
            }, {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                title: 'Social Security Report',
                text: 'PDF',
            }],

            initComplete: function(settings, json) {
                $("#socialSecuritySummaryTable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        function downloadExcel() {
            $month = $('.monthField').val();
            $branch_id = $('.branch_id').val();
            $department_id = $('.department_id').val();
            $designation_id = $('.designation_id').val();

            window.location.href =
                "{{ url('wpms/downloadSocicalSecurityReport') }}" + '?month=' + $month + '&branch_id=' +
                $branch_id + '&department_id=' + $department_id + '&designation_id=' + $designation_id;
        };
    });
</script>
@endsection
