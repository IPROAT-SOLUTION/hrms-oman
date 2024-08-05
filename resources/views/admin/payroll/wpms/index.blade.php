@extends('admin.master')
@section('content')
@section('title')
    @lang('paygrade.wpms_configure')
@endsection

<script>
    jQuery(function() {
        $("#wpsgeneration").validate();
    });
</script>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
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
                        <div id="searchBox">
                            <div class="col-md-3"></div>
                            {{ Form::open([
                                'route' => 'wpms.index',
                                'id' => 'wpsgeneration',
                                'class' => 'form-horizontal',
                            ]) }}

                            <div class="form-group">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label" for="branch_id">@lang('common.branch'):</label>
                                        <select name="branch_id" class="form-control branch_id select2">
                                            <option value="">--- @lang('common.all') ---</option>
                                            @foreach ($branchList as $value)
                                                <option value="{{ $value->branch_id }}"
                                                    @if ($value->branch_id == $branch_id) {{ 'selected' }} @endif>
                                                    {{ $value->branch_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label" for="month">@lang('common.month')<span
                                            class="validateRq">*</span>:</label>
                                    <input class="form-control monthField" style="height: 32px;" required readonly
                                        placeholder="@lang('common.month')" id="month" name="month"
                                        value="@if (isset($month)) {{ $month }}@else {{ date('Y-m') }} @endif">
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="submit" id="filter" style="margin-top: 28px;"
                                            class="btn btn-info btn-md" value="@lang('common.filter')">
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}

                        </div>

                        <div class="table-responsive">
                            <table id="wpmsSheetTable" class="table table-bordered manage-u-table">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('paygrade.employee_id_type')</th>
                                        <th>@lang('paygrade.employee_id')</th>
                                        <th>@lang('paygrade.reference_number')</th>
                                        <th>@lang('paygrade.employee_name')</th>
                                        <th>@lang('paygrade.employee_bic')</th>
                                        <th>@lang('paygrade.employee_account')</th>
                                        <th>@lang('paygrade.salary_frequency')</th>
                                        <th>@lang('paygrade.number_of_working_days')</th>
                                        <th>@lang('paygrade.net_salary')</th>
                                        <th>@lang('paygrade.basic_salary')</th>
                                        <th>@lang('paygrade.extra_hours')</th>
                                        <th>@lang('paygrade.extra_income')</th>
                                        <th>@lang('paygrade.deductions')</th>
                                        <th>@lang('paygrade.social_security_deductions')</th>
                                        <th>@lang('paygrade.notes_comments')</th>
                                    </tr>
                                </thead>
                                @if (count($results) > 0)
                                    <tbody>
                                        {!! $sl = null !!}
                                        @foreach ($results as $value)
                                            <tr class="{!! $value->wpms_id !!}">
                                                <td style="width: 100px;">{!! ++$sl !!}</td>
                                                <td>{!! $value->employee_id_type !!}</td>
                                                <td>{!! $value->employee_document_type !!}</td>
                                                <td>{!! trim('Salary ' . date('F Y', strtotime($value->month_of_salary))) !!}</td>
                                                <td>{!! $value->employee->fullname() !!}</td>
                                                <td>{!! $value->employee->ifsc_number !!}</td>
                                                <td>{!! $value->employee->account_number !!}</td>
                                                <td>{!! 'M' !!}</td>
                                                <td>{!! $value->total_working_days !!}</td>
                                                <td>{!! $value->net_salary !!}</td>
                                                <td>{!! $value->basic_salary !!}</td>
                                                <td>{!! $value->extra_hours !!}</td>
                                                <td>{!! $value->extra_amount !!}</td>
                                                <td>{!! $value->total_deductions !!}</td>
                                                <td>{!! $value->social_security !!}</td>
                                                <td>{!! $value->notes_comments !!}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @endif
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
    $(function() {
        var table = $('#wpmsSheetTable').DataTable({
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
                },
                {
                    text: 'PDF',
                    className: 'dt-button buttons-custom-pdf buttons-html5',
                    action: function(e, dt, node, config) {
                        downloadPdf();
                    },
                    customize: function(doc) {
                        doc.content.splice(0, 0, {
                            margin: [0, 0, 0, 12],
                            alignment: 'center',
                            image: base64_encode(file_get_contents('admin_assets/img/logo.png'))
                        });
                    }
                }
            ],

            initComplete: function(settings, json) {
                $("#wpmsSheetTable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        function downloadExcel() {
            $month = $('.monthField').val();
            window.location.href =
                "{{ url('wpms/excelDownload') }}" + '?month=' + $month;
        };

        function downloadPdf() {
            $month = $('.monthField').val();
            window.location.href =
                "{{ url('wpms/pdfDownload') }}" + '?month=' + $month;
        };
    });
</script>
@endsection
