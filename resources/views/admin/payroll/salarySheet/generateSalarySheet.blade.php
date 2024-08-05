@extends('admin.master')
@section('content')
@section('title')
    @lang('salary_sheet.generate_salary_sheet')
@endsection
<style>
    .table>tbody>tr>td {
        padding: 5px 7px;
    }

    .address {
        margin-top: 22px;
    }

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

    .icon-question {
        color: #7460ee;
        font-size: 16px;
        vertical-align: text-bottom;
    }
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>

            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{ route('generateSalarySheet.index') }}"
                class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-caret-left" style="padding: 0px  8px 0px 0px; aria-hidden=" true"></i>
                @lang('salary_sheet.back_button')</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">×</span></button>
                                @foreach ($errors->all() as $error)
                                    <strong>{!! $error !!}</strong><br>
                                @endforeach
                            </div>
                        @endif
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
                                &nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        <div class="bg-title"
                            style="border: 1px solid #EFEEEF; border-radius:4px;margin: 12px;padding:12px">
                            <div class="row">
                                <p class="border" style="margin-left:30px">
                                    <span><i class="fa fa-upload"></i></span>
                                    <span style="margin-left: 4px"><b>Upload Document Here (.xlsx).</b></span>
                                </p>
                                <form class="col-md-8" action="{{ route('generateSalarySheet.uploadSalarySheet') }}"
                                    method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="file" name="select_file"
                                        class="form-control custom-file-upload col-md-8" style="width: 250px;">
                                    <button class="btn btn-success btn-sm col-md-1" style="margin: 1px 16px;width:90px"
                                        type="submit"><span><i class="fa fa-upload" aria-hidden="true"></i></span>
                                        Upload</button>
                                </form>
                                <form class="row col-md-4 text-right"
                                    action="{{ route('generateSalarySheet.salarySheetTemplate') }}" method="get"
                                    enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <button class="col-md-2 btn btn-info btn-sm pull-right waves-effect waves-light"
                                        type="submit" style="margin-top: 2px;min-width: 100px;">
                                        <i class="fa fa-download" style="margin-right: 2px;"
                                            aria-hidden="true"></i><span>
                                            Template</span>
                                    </button>
                                    <div class="col-md-3 form-group pull-right" style="min-width: 120px;">
                                        <input class="form-control monthField" style="height: 32px;background:#fff"
                                            required readonly placeholder="@lang('common.month')" id="month"
                                            name="month"
                                            value="@if (isset($month)) {{ $month }}@else {{ date('Y-m') }} @endif">
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="bg-title border" style="margin: 12px;padding:12px">
                            {{ Form::open(['route' => 'generateSalarySheet.calculateEmployeeSalary', 'method' => 'GET', 'id' => 'calculateEmployeeSalaryForm']) }}
                            <div class="form-body">
                                <div class="row col-md-offset-2">
                                    <div class="col-md-4">
                                        <label for="exampleInput">@lang('common.month')<span
                                                class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            {!! Form::text(
                                                'month',
                                                isset($month) ? $month : '',
                                                $attributes = [
                                                    'class' => 'form-control required monthField',
                                                    'autocomplete' => 'off',
                                                    'id' => 'month',
                                                    'placeholder' => __('common.month'),
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-4">
                                        <div class="form-group employeeName">
                                            <label for="exampleInput">@lang('common.employee')<span
                                                    class="validateRq">*</span></label>
                                            {{ Form::select(
                                                'employee_id',
                                                $employeeList,
                                                isset($employee_id) ? $employee_id : '',
                                                $attributes = ['class' => 'form-control employee_id select2 required', 'required' => 'required'],
                                            ) }}
                                        </div>
                                    </div> --}}
                                    <div class="col-md-4">
                                        <div class="form-group employeeName">
                                            <label class="control-label" for="email">@lang('common.employee')<span
                                                    class="validateRq">*</span></label>
                                            <select class="form-control employee_id select2 required" required
                                                name="employee_id">
                                                <option value="">---- @lang('common.please_select') ----</option>
                                                @foreach ($employeeList as $value)
                                                    <option value="{{ $value->employee_id }}"
                                                        @if (isset($employee_id) && $employee_id == $value->employee_id) {{ 'selected' }} @endif>
                                                        {{ $value->first_name }} {{ $value->last_name }}
                                                        {{ " ({$value->finger_id})" }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-info "
                                                style="margin-top: 26px;width:180px">
                                                @lang('salary_sheet.generate_salary')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>


            @if (isset($employeeDetails))
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>
                        @lang('salary_sheet.salary_sheet') <span>{{ '- ' . $employeeDetails->detailname() }}</span></div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        {{ Form::open(['route' => 'saveEmployeeSalaryDetails.store']) }}
                        <div class="panel-body" style="padding: 18px 49px;">
                            <br>
                            <div class="row" style="border: 1px solid #ddd;padding: 26px 9px">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-bordered table-hover table-striped">
                                            <tbody>
                                                <tr style="font-weight: bold">
                                                    <td class="col-md-6">
                                                        {{ 'Employee Info' }}
                                                    </td>
                                                    <td class="col-md-6 text-right"> <b>{{ '#' }}</b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6">@lang('common.name')</td>
                                                    <td class="col-md-6 text-right">
                                                        <b>{{ $employeeDetails->fullname() }}</b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6">@lang('common.employee_id')</td>
                                                    <td class="col-md-6 text-right">
                                                        <b>{{ $employeeDetails->finger_id }}</b>
                                                    </td>
                                                    <input type="text" name="employee_id" hidden
                                                        value="{{ $employeeDetails->employee_id }}">
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6">@lang('employee.department')</td>
                                                    <td class="col-md-6 text-right"><b>
                                                            @if (isset($employeeDetails->department->department_name))
                                                                {{ $employeeDetails->department->department_name }}
                                                            @endif
                                                        </b></td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6">@lang('employee.designation')</td>
                                                    <td class="col-md-6 text-right"><b>
                                                            @if (isset($employeeDetails->designation->designation_name))
                                                                {{ $employeeDetails->designation->designation_name }}
                                                            @endif
                                                        </b></td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6">@lang('employee.date_of_joining') </td>
                                                    <td class="col-md-6 text-right"><b>
                                                            {{ date('d-M-Y', strtotime($employeeDetails->date_of_joining)) }}</b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6">@lang('common.month') </td>
                                                    <td class="col-md-6 text-right"><b>
                                                            {{ date('Y-m', strtotime($month)) }}</b>
                                                    </td>
                                                    <input type="text" name="month_of_salary" hidden
                                                        value="{{ date('Y-m', strtotime($month)) }}">
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6">
                                                        @lang('payroll.basic_salary')
                                                    </td>
                                                    <td class="col-md-6 text-right">
                                                        <b><input type="text" class="text-right" readonly
                                                                value="{{ $basic }}"
                                                                style="border:none;background:#f9f9f9;"
                                                                onkeypress="return onlyNumberKey(event)">
                                                        </b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6">
                                                        @lang('payroll.increment')
                                                    </td>
                                                    <td class="col-md-6 text-right">
                                                        <b><input type="text" class="text-right" readonly
                                                                value="{{ $increment }}" name="increment"
                                                                style="border:none;background:#f9f9f9;"
                                                                onkeypress="return onlyNumberKey(event)">
                                                        </b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6">
                                                        @lang('payroll.increment_amount')
                                                    </td>
                                                    <td class="col-md-6 text-right">
                                                        <b><input type="text" class="text-right" readonly
                                                                value="{{ $increment_amount }}"
                                                                name="increment_amount"
                                                                style="border:none;background:#f9f9f9;"
                                                                onkeypress="return onlyNumberKey(event)">
                                                        </b>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-6">
                                        <table class="table table-bordered table-hover table-striped">
                                            <tbody>
                                                <tr style="font-weight: bold">
                                                    <td class="col-md-6">
                                                        {{ 'Attendance Info' }}
                                                    </td>
                                                    <td class="col-md-6 text-right"> <b>{{ 'Days/Hrs' }}</b>
                                                    </td>
                                                </tr>
                                                @foreach ($attendanceInfo as $key => $value)
                                                    <tr>
                                                        <td class="col-md-6">
                                                            {{-- {{ ucwords(str_replace('_', ' ', $key)) }} --}}
                                                            @lang('payroll.' . $key)
                                                        </td>
                                                        <td class="col-md-6 text-right"> <b><input readonly
                                                                    type="text" name="{{ $key }}"
                                                                    class="text-right"
                                                                    style="border:none;background:#f9f9f9;"
                                                                    value="{{ $value }}"
                                                                    onkeypress="return onlyNumberKey(event)">
                                                            </b>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-bordered table-hover table-striped">
                                            <tbody>
                                                <tr style="font-weight: bold">
                                                    <td class="col-md-6">
                                                        @lang('payroll.allowances')
                                                    </td>
                                                    <td class="col-md-6 text-right"> <b>{{ 'OMR' }}</b>
                                                    </td>
                                                </tr>
                                                <input type="hidden" name="employer_contribution"
                                                    id="employer_contribution">

                                                @foreach ($allowances as $key => $value)
                                                    @if ($value > 0)
                                                        <tr>
                                                            <td class="col-md-6">
                                                                {{-- {{ ucwords(str_replace('_', ' ', $key)) }} --}}
                                                                @lang('payroll.' . $key)
                                                            </td>
                                                            <td class="col-md-6 text-right">
                                                                <b><input readonly type="text"
                                                                        name="{{ $key }}"
                                                                        class="text-right allowances"
                                                                        style="border:none;background:#f9f9f9;"
                                                                        value="{{ number_format($value, 3, '.', '') }}"
                                                                        onkeypress="return onlyNumberKey(event)"></b>
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <input readonly type="text" name="{{ $key }}"
                                                            hidden class="text-right allowances"
                                                            style="border:none;background:#f9f9f9;"
                                                            value="{{ number_format($value, 3, '.', '') }}"
                                                            onkeypress="return onlyNumberKey(event)">
                                                    @endif
                                                @endforeach

                                                @if ($over_time_amount > 0)
                                                    <tr>
                                                        <td class="col-md-6">
                                                            @lang('payroll.extra_amount')
                                                            <i class="" style="padding: 6px 12px;"></i>
                                                        </td>
                                                        <td class="col-md-6 text-right">
                                                            <b><input type="text" class="text-right allowances"
                                                                    readonly value="{{ $over_time_amount }}"
                                                                    name="extra_amount"
                                                                    style="border:none;background:#f9f9f9;"></b>
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td class="col-md-6" style="font-weight:500">
                                                        @lang('payroll.gross_salary')
                                                    </td>
                                                    <td class="col-md-6 text-right">
                                                        <b><input type="text" readonly
                                                                class="text-right total_allowances"
                                                                name="total_allowances"
                                                                style="border:none;background:#f9f9f9;"
                                                                value="{{ number_format(array_sum($allowances), 3, '.', '') }}">
                                                        </b>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-6">
                                        <table class="table table-bordered table-hover table-striped">
                                            <tbody>
                                                <tr style="font-weight: bold">
                                                    <td class="col-md-6">
                                                        @lang('payroll.deductions')
                                                    </td>
                                                    <td class="col-md-6 text-right"> <b>{{ 'OMR' }}</b>
                                                    </td>
                                                </tr>
                                                @foreach ($deductions as $key => $value)
                                                    @if ($value > 0)
                                                        <tr>
                                                            <td class="col-md-6">
                                                                {{-- {{ ucwords(str_replace('_', ' ', $key)) }} --}}
                                                                @lang('payroll.' . $key)
                                                            </td>
                                                            <td class="col-md-6 text-right">
                                                                <b><input readonly type="text"
                                                                        name="{{ $key }}"
                                                                        class="text-right deductions"
                                                                        style="border:none;background:#f9f9f9;"
                                                                        value="{{ number_format($value, 3, '.', '') }}"
                                                                        onkeypress="return onlyNumberKey(event)"></b>
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <input readonly type="text" name="{{ $key }}"
                                                            hidden class="text-right deductions"
                                                            style="border:none;background:#f9f9f9;"
                                                            value="{{ number_format($value, 3, '.', '') }}"
                                                            onkeypress="return onlyNumberKey(event)">
                                                    @endif
                                                @endforeach
                                                <tr>
                                                    <td class="col-md-6">
                                                        @lang('payroll.lop')
                                                        <i class="fa fa-edit" style="padding: 6px 12px;"></i>
                                                    </td>
                                                    <td class="col-md-6 text-right">
                                                        <b><input type="text" name="lop"
                                                                value="{{ $salaryDetails->lop ?? '0.000' }}"
                                                                class="text-right lop deductions" name="lop"
                                                                style="border:none;background:#f9f9f9;"
                                                                onkeypress="return onlyNumberKey(event)"></b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6">
                                                        @lang('payroll.pay_cut')
                                                        <i class="fa fa-edit" style="padding: 6px 12px;"></i>
                                                    </td>
                                                    <td class="col-md-6 text-right">
                                                        <b><input type="text" name="pay_cut"
                                                                value="{{ $salaryDetails->pay_cut ?? '0.000' }}"
                                                                class="text-right deductions" name="pay_cut"
                                                                style="border:none;background:#f9f9f9;"
                                                                onkeypress="return onlyNumberKey(event)"></b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6">
                                                        @lang('payroll.gsm')
                                                        <i class="fa fa-edit" style="padding: 6px 12px;"></i>
                                                    </td>
                                                    <td class="col-md-6 text-right">
                                                        <b><input type="text" class="text-right deductions"
                                                                name="gsm" style="border:none;background:#f9f9f9;"
                                                                value="{{ $salaryDetails->gsm ?? '0.000' }}"
                                                                onkeypress="return onlyNumberKey(event)"></b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6" style="font-weight:500">
                                                        @lang('payroll.total_deduction')
                                                    </td>
                                                    <td class="col-md-6 text-right">
                                                        <b><input type="text" readonly
                                                                class="text-right total_deductions"
                                                                name="total_deductions"
                                                                style="border:none;background:#f9f9f9;"
                                                                value="{{ number_format(array_sum($deductions), 3, '.', '') }}">
                                                        </b>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-6">
                                        <table class="table table-bordered table-hover table-striped">
                                            <tbody class="tr_header">
                                                <tr>
                                                    <td class="col-md-6">
                                                        {{-- {{ 'Arrears/Adjustment' }} --}}
                                                        @lang('payroll.arrears_adjustment')
                                                        <i class="fa fa-edit" style="padding: 6px 12px;"></i>
                                                    </td>
                                                    <td class="col-md-6 text-right">
                                                        <b><input type="text" class="text-right arrears_adjustment"
                                                                placeholder="0" name="arrears_adjustment"
                                                                value="{{ $salaryDetails->arrears_adjustment ?? 0 }}"
                                                                style="border:none;background:#f9f9f9;"
                                                                onkeypress="return numberInputPayroll(event)"></b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6"
                                                        style="color:black;font-size:16px;font-weight:bold">
                                                        @lang('payroll.net_salary')
                                                    </td>
                                                    <td class="col-md-6 text-right">
                                                        <b><input type="text" readonly placeholder="0"
                                                                name="net_salary" class="text-right net_salary"
                                                                style="border:none;color:black;background:#f9f9f9;font-weight:bold"
                                                                value="{{ number_format(array_sum($allowances) - array_sum($deductions), 3, '.', '') }}">
                                                        </b>
                                                    </td>
                                                </tr>
                                                <tr>

                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="col-md-12 text-center">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info btn_style" title="submit"><i
                                            class="fa fa-check"></i>
                                        @lang($salaryDetails ? 'common.update' : 'common.save')</button>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script type="text/javascript">
    jQuery(function() {
        $("#calculateEmployeeSalaryForm").validate();
        calculateLop();
    });

    //iterate through each textboxes and add keyup
    //handler to trigger sum event
    $(document).on('keyup', '.deductions', function() {
        $(this).each(function() {
            calculateSumDeduction();
        });
    });

    $(document).on('keyup', '.arrears_adjustment', function() {
        calculateSumEarning();
        calculateSumDeduction();
        calculateNetsalary();
    });

    function calculateNetsalary() {
        var arrears_adjustment = $(".arrears_adjustment").val();
        var net_amount = $(".net_salary").val();
        var net_salary = parseFloat(arrears_adjustment) + parseFloat(net_amount);
        $(".net_salary").val(net_salary.toFixed(3));
    }

    function calculateLop() {
        var current_lop = $("input[name='lop']").val();
        var total_allowance = $(".total_allowances").val();
        var total_lop_calcualted = {!! isset($salaryDetails->lop) ? $salaryDetails->lop : 'undefined' !!};
        var lop_amount = parseFloat(total_allowance / 30) * parseFloat("{{ $lop_days ?? 0 }}");
        var total_lop = $("input[name='lop']").val(lop_amount.toFixed(3));

        calculateSumEarning();
        calculateSumDeduction();
        calculateNetsalary();

    }

    function calculateSumDeduction() {
        var deduction = 0;
        //iterate through each textboxes and add the values
        $(".deductions").each(function() {
            var currency = this.value;
            var number = Number(currency.replace(/[^0-9.-]+/g, ""));
            //add only if the value is number
            if (!isNaN(number) && this.value.length != 0) {
                deduction += parseFloat(number);
            }
        });
        //.toFixed() method will roundoff the final sum to 2 decimal places

        var total_allowance = $(".total_allowances").val();

        var total_deductions = $(".total_deductions").val(deduction.toFixed(3));
        var net_salary = $(".net_salary").val((parseFloat(total_allowance) - deduction).toFixed(3));
    }

    //iterate through each textboxes and add keyup
    //handler to trigger sum event
    $(document).on('keyup', '.allowances', function() {
        $(this).each(function() {
            calculateSumEarning();
        });
    });

    var socialSecurity = {!! isset($socialSecurity) ? $socialSecurity : 'undefined' !!};

    function calculateSumEarning() {
        var earning = 0;

        //iterate through each textboxes and add the values
        $(".allowances").each(function() {
            var currency = this.value;
            var number = Number(currency.replace(/[^0-9.-]+/g, ""));
            //add only if the value is number
            if (!isNaN(number) && this.value.length != 0) {
                earning += parseFloat(number);
            }
        });

        // recalculate pasi
        var social_security_percentage = 0;
        var employer_contribution_percentage = 0;


        if (socialSecurity != 'undefined') {
            social_security_percentage = socialSecurity.percentage;
            employer_contribution_percentage = socialSecurity.employer_contribution;
        }

        //.toFixed() method will roundoff the final sum to 2 decimal places
        var total_allowance = $(".total_allowances").val(earning.toFixed(3));

        var ss_calculation = (social_security_percentage / 100) * earning.toFixed(3);
        var employee_contribution_calculation = (employer_contribution_percentage / 100) * earning.toFixed(3);

        var social_security = $('input[name="social_security"]').val(ss_calculation.toFixed(3));
        var employer_contribution_amount = $('input[name="employer_contribution"]').val(
            employee_contribution_calculation.toFixed(3));

        calculateSumDeduction();

        var total_deductions = $(".total_deductions").val();
        var net_salary = $(".net_salary").val((earning - parseFloat(total_deductions)).toFixed(3));
    }
</script>
@endsection
