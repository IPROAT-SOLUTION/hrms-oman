@extends('admin.master')
@section('content')
@section('title')
    @lang('socialSecurity.socialSecurity_list')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
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
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('warning'))
                            <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('warning') }}</strong>
                            </div>
                        @endif
                        <div class="row">
                            <div id="searchBox">
                                {{ Form::open(['route' => 'socialSecurityReport.index', 'id' => 'socialSecurityReport', 'method' => 'POST']) }}
                                <div class="col-md-1"></div>
                                <div class="col-md-2">
                                    <label class="control-label" for="email">@lang('common.branch')</label>
                                    <div class="form-group">
                                        <select class="form-control branchName branch_id select2 " name="branch_id">
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
                                        <select class="form-control designation_id select2 " name="designation_id">
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

                                    <div class="form-group">
                                        <label for="exampleInput">@lang('common.month')</label>
                                        {!! Form::text(
                                            'month',
                                            $_REQUEST['month'] ?? date('Y-m'),
                                            $attributes = [
                                                'class' => 'form-control monthField',
                                                'id' => 'month',
                                                'placeholder' => __('common.month'),
                                                'autocomplete' => 'off',
                                            ],
                                        ) !!}
                                    </div>

                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="submit" id="filter" style="margin-top: 25px;height:36px"
                                            class="btn btn-info btn-md" value="@lang('common.filter')">
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>

                        @if (count($results) > 0)
                            <h4 class="text-right">

                                <a class="btn btn-success" style="color: #fff"
                                    href="{{ URL('downloadSocicalSecurityReport/' . $_REQUEST['month'] . '/' . $_REQUEST['branch_id'] . '/' . $_REQUEST['department_id'] . '/' . $_REQUEST['designation_id']) }}"><i
                                        class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download')
                                </a>


                            </h4>
                        @endif

                        @include('admin.payroll.socialSecurity.reportpagination')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_scripts')
<script type="text/javascript">
    $(".month").datepicker({
        format: "yyyy-mm",
        minViewMode: "months",
        dateFormat: 'yyyy-mm',
        duration: 'fast',
        todayHighlight: true,
        startDate: new Date(),
    }).on('changeDate', function(e) {
        $(this).datepicker('hide');
    }).focus(function() {
        // $(".datepicker-switch, .prev , .next").remove();
    });

    $(function() {
        var table = $('#socialSecurityTable').DataTable({
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
                $("#socialSecurityTable").wrap(
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
