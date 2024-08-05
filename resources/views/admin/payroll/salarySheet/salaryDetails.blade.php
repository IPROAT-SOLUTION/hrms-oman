@extends('admin.master')
@section('content')
@section('title')
    @lang('salary_sheet.salary_info')
@endsection

<style>
    .dataTables_scrollHeadInner {
        width: 100% !important;
    }

    .dataTables_scrollHeadInner table {
        width: 100% !important;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>

        <div class="ml-auto">
            <a href="{{ route('generateSalarySheet.create') }}"
                class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-plus-circle text-white" aria-hidden="true"></i>
                <span class="text-white" style="padding-left: 4px">@lang('salary_sheet.generate_salary_sheet')</span></a>
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
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="row">

                            <form action="{{ route('generateSalarySheet.index') }}" method="get">
                                <div class="col-md-3"></div>
                                <div class="row">
                                    <div class="col-md-3" style="padding-left: 50px">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('common.month')</label>
                                            {!! Form::text(
                                                'month',
                                                $month ?? date('Y-m', strtotime('-1 month')),
                                                $attributes = [
                                                    'class' => 'form-control monthField',
                                                    'id' => 'month',
                                                    'placeholder' => __('common.month'),
                                                    'readonly' => 'readonly',
                                                ],
                                            ) !!}
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="submit" id="filter"
                                                style="margin-top: 26px; width: 100px;margin-left:18px;"
                                                class="btn btn-info " value="@lang('common.filter')">
                                        </div>
                                    </div>
                                </div>
                            </form>

                            {{-- <div class="ml-auto" style="padding: 24px 24px;">
                                <a href="{{ route('generateSalarySheet.downloadSalarySheet', ['month' => $_REQUEST['month'] ?? date('Y-m', strtotime('-1 month'))]) }}"
                                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                                    <i class="fa fa-download text-white" aria-hidden="true"></i>
                                    <span class="text-white" style="padding-left: 4px">@lang('salary_sheet.download_salary_sheet')</span></a>
                            </div> --}}
                        </div>
                        {{-- <br> --}}

                        <div class="data">
                            @include('admin.payroll.salarySheet.pagination')
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><b>@lang('salary_sheet.payment_for')<span class="monthAndYearName"></span></b></h4>
            </div>
            <div class="modal-body">
                <form>
                    {{ csrf_field() }}
                    <input type="hidden" class="salary_details_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">@lang('common.employee_name')</label>
                                <input type="text" class="form-control employee_name" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">@lang('paygrade.basic_salary')</label>
                                <input type="text" class="form-control basic_salary" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">@lang('salary_sheet.total_allowance')</label>
                                <input type="text" class="form-control total_allowance" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">@lang('salary_sheet.total_deduction')</label>
                                <input type="text" class="form-control total_deduction" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">@lang('paygrade.gross_salary')</label>
                                <input type="text" class="form-control gross_salary" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">@lang('salary_sheet.payment_method')</label>
                                <select class="form-control paymentMethod">
                                    <option value="Cash">@lang('salary_sheet.cash')</option>
                                    <option value="Cheque">@lang('salary_sheet.cheque')</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="message-text" class="control-label">@lang('salary_sheet.comments')</label>
                                <textarea class="form-control comment"></textarea>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect"
                    data-dismiss="modal"><b>@lang('common.close')</b></button>
                <button type="button" class="btn btn-info btn_style waves-effect waves-light makePayment"
                    data-dismiss="modal"> <b>@lang('salary_sheet.pay')</b></button>

            </div>
        </div>
    </div>
</div>
@endsection


@section('page_scripts')
<script type="text/javascript">
    $(document).on('click', '.payslip', function() {
        var id = $(this).attr('data-status');
        var btnColor = "#2cabe3";
        swal({
                title: 'Now loading! \n Please Wait...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showCancelButton: false,
                showConfirmButton: false,
            }),
            $.ajax({
                type: "get",
                url: "{{ route('getEmployeeEmail') }}",
                data: {
                    'id': id,
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == true) {
                        swal({
                            title: "View Payslip",
                            text: "To view the Payslip, please enter the OTP sent to this email: " +
                                response.mail,
                            type: "input",
                            buttons: {
                                cancel: true,
                                confirm: {
                                    text: "Submit",
                                    className: "btn-primary",
                                },
                            },
                            showCancelButton: true,
                        }, function(value) {
                            if (value) {
                                var token = '{{ csrf_token() }}';
                                $.ajax({
                                    type: 'POST',
                                    url: "{{ route('verifyOtp') }}",
                                    data: {
                                        'id': id,
                                        'otp': value, // Pass the entered OTP
                                        'mail': response.mail, // Pass the entered OTP
                                        _token: token
                                    },
                                    success: function(data) {
                                        if (data.status == true) {
                                            var url =
                                                "downloadPayslipPdf/" + id;
                                            var newTab = window.open(url, '_blank');
                                            if (newTab) {
                                                newTab.focus();
                                            } else {
                                                // Pop-up blockers may prevent opening the new tab, handle accordingly
                                                alert(
                                                    'Please allow pop-ups for this site to open the link in a new tab.'
                                                );
                                            }
                                            // Handle success case, e.g., display the payslip
                                        } else {
                                            swal({
                                                title: "Rejected!",
                                                text: "Wrong OTP",
                                                type: "error"
                                            }).then(() => {
                                                location.reload();
                                            });
                                        }
                                    }
                                });
                            } else {
                                swal("Cancelled", "You cancelled the operation.", "error");
                            }
                        });

                    }
                }
            });
        return false;
    });


    $(function() {
        var table = $('#salarySheetTable').DataTable({
            ordering: false,
            dom: 'lBfrtip',
            buttons: [{
                text: 'CSV',
                className: 'dt-button buttons-custom-csv buttons-html5',
                action: function(e, dt, node, config) {
                    downloadExcel();
                }
            }, {
                text: 'PDF',
                className: 'dt-button buttons-custom-pdf buttons-html5',
                action: function(e, dt, node, config) {
                    downloadPdf();
                }
            }],

            initComplete: function(settings, json) {
                $("#salarySheetTable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        function downloadExcel() {
            $month = $('.monthField').val();
            window.location.href =
                "{{ url('generateSalarySheet/downloadSalarySheet') }}" + '?monthField=' + $month;
        };

        function downloadPdf() {
            $month = $('.monthField').val();
            window.location.href =
                "{{ url('generateSalarySheet/downloadSalarySheetPdf') }}" + '?monthField=' + $month;
        };
    });
</script>
@endsection
