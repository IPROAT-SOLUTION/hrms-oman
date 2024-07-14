@extends('admin.master')
@section('content')
@section('title')
    @lang('payment.my_payroll')
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
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@lang('payment.my_payroll')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">

                        <div class="table-responsive">
                            <table id="myPayrollTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('common.month')</th>
                                        <th>@lang('employee.photo')</th>
                                        <th>@lang('common.employee_name')</th>
                                        <th>@lang('paygrade.basic_salary')</th>
                                        <th>@lang('paygrade.allowance')</th>
                                        <th>@lang('salary_sheet.gross_salary')</th>
                                        <th>@lang('paygrade.deduction')</th>
                                        <th>@lang('salary_sheet.net_salary')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        @php
                                            // dd($value);
                                        @endphp
                                        <tr>
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td>
                                                @php
                                                    $monthAndYear = explode('-', $value->month_of_salary);

                                                    $month = $monthAndYear[1];
                                                    $dateObj = DateTime::createFromFormat('!m', $month);
                                                    $monthName = $dateObj->format('F');
                                                    $year = $monthAndYear[0];

                                                    $monthAndYearName = $monthName . ' ' . $year;
                                                    echo $monthAndYearName;
                                                @endphp
                                            </td>
                                            <td>
                                                @if ($value->employee->photo != '')
                                                    <img style=" width: 70px; " src="{!! asset('uploads/employeePhoto/' . $value->employee->photo) !!}"
                                                        alt="user-img" class="img-circle">
                                                @else
                                                    <img style=" width: 70px; " src="{!! asset('admin_assets/img/default.png') !!}"
                                                        alt="user-img" class="img-circle">
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($value->employee->first_name))
                                                    {!! $value->employee->first_name !!} {{ $value->employee->last_name }}
                                                @endif
                                            </td>
                                            <td>{!! $value->basic_salary !!}</td>
                                            <td>{!! $value->total_allowances - $value->basic_salary !!}</td>
                                            <td>{!! $value->gross_salary !!}</td>
                                            <td>{!! $value->total_deductions !!}</td>
                                            <td>{!! $value->net_salary !!}</td>

                                            {{-- <td style="width: 100px">
															<a href="{{url('myPayroll/generatePayslip',$value->salary_details_id)}}" target="_blank"><button  class="btn btn-success waves-effect waves-light"><span>@lang('salary_sheet.generate_payslip')</span> </button></a>
													</td> --}}
                                            <td> <button class="btn btn-xs btn-primary payslip"
                                                    data-status={{ $value->salary_details_id }}>@lang('payroll.payslip')</button>
                                            </td>
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
        var table = $('#myPayrollTable').DataTable({
            autoFill: true,
            ordering: false,
            processing: false,
            colReorder: false,
            keys: true,
            dom: 'lfrtip',
            initComplete: function(settings, json) {
                $("#myPayrollTable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });
    });
</script>
@endsection
