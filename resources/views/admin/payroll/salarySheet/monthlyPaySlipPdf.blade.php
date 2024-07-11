<!DOCTYPE html>
<html lang="en">

<head>
    <title>@lang('salary_sheet.employee_payslip')</title>
    <meta charset="utf-8">
</head>
<style>
    table {
        margin: 0 0 40px 0;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        display: table;
        border-spacing: 0px;
    }

    table,
    td,
    th {
        border: 1px solid #9295e6;
    }

    td {
        padding: 3px;
    }

    th {
        padding: 3px;
    }

    .text-center {
        text-align: center;
    }

    .companyAddress {
        width: 367px;
        margin: 0 auto;
    }

    .container {
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
        width: 95%;
    }

    .row {
        margin-right: -15px;
        margin-left: -15px;
    }

    .col-md-6 {
        width: 46.5%;
        float: left;
        padding: 12px;
    }

    .div1 {
        position: relative;
    }

    .div2 {
        position: absolute;
        width: 100%;
        /* border: 0.1px solid; */
        padding: 30px 12px 0px 12px;
    }

    .col-md-4 {
        width: 33.33333333%;
        float: left;
    }

    .col-md-2 {
        width: 19%;
        float: left;
    }

    .col-md-5 {
        width: 39%;
        float: left;
    }

    .col-md-8 {
        width: 58.5%;
        float: left;
    }

    .col-md-3 {
        width: 25%;
        float: left;
    }

    .clearFix {
        clear: both;
    }

    .padding {
        margin-bottom: 32px;

    }
</style>

<body>
    <div class="container">
        <div class="row">
            <div class="div1">
                <div class="div2">
                    <div class="clearFix">
                        <div class="col-md-full" style="border-top:2px solid blue;">
                           
                            <table class="table table-bordered">
                                <tr style="background:#3287ff;font-size: 20px;">
                                    <td colspan="4" class="text-center">
                                        <strong style="color: #fff">Pay Slip Summary</strong>
                                    </td>
                                </tr>
                                <tr style="color: #0045a4;background:#deecff;">
                                    <td colspan="1" class="text-left" style="font-weight:bold;">@lang('common.name')
                                    </td>
                                    <td colspan="3" class="text-left" style="font-weight:bold;">
                                        {{ $salary->employee->fullname() }}
                                    </td>
                                </tr>
                                <tr style="color: #0045a4;background:#deecff;">
                                    <td colspan="1" class="text-left" style="font-weight:bold;">@lang('common.year')
                                    </td>
                                    <td colspan="1" class="text-left" style="color: #0056ce;font-weight:normal">
                                        {{ date('Y', strtotime($salary->month_of_salary)) }}</td>
                                    <td colspan="1" class="text-left" style="font-weight:bold;">@lang('common.month')
                                    </td>
                                    <td colspan="1" class="text-left" style="color: #0056ce;font-weight:normal">
                                        {{ date('M', strtotime($salary->month_of_salary)) }}
                                    </td>
                                </tr>
                                <tr style="color: #0045a4;background:#deecff;">
                                    <td colspan="1" class="text-left" style="font-weight:bold;">@lang('common.start_date')
                                    </td>
                                    <td colspan="1" class="text-left" style="color: #0056ce;font-weight:normal">
                                        {{ date('Y-m-d', strtotime($salary->month_of_salary . '-01')) }}</td>
                                    <td colspan="1" class="text-left" style="font-weight:bold;">@lang('common.end_date')
                                    </td>
                                    <td colspan="1" class="text-left" style="color: #0056ce;font-weight:normal">
                                        {{ date('Y-m-t', strtotime($salary->month_of_salary . '-01')) }}</td>
                                </tr>
                                <tr style="color: #0045a4;background:#deecff;">
                                    <td colspan="1" class="text-left" style="font-weight:bold;">@lang('common.paid_days')
                                    </td>
                                    <td colspan="3" class="text-left" style="color: #0056ce;font-weight:normal">
                                        {{ $salary->total_working_days }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-center" style="color: green;font-weight:bold">
                                        @lang('common.earnings')</td>
                                    <td colspan="2" class="text-center" style="color: red;font-weight:bold">
                                        @lang('common.deductions')
                                    </td>
                                </tr>
                            </table>
                            <div style="border: 2px solid #9295e6">
                                <table class="table">
                                    <tbody>
                                        <tr style="background:#01265a;font-size: 20px;">
                                            <td colspan="4" class="text-center">
                                                <strong style="color: #fff">PaySlip</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div style="margin: 8px 0;">
                                    <div class="col-md-6">
                                        <table>
                                            <thead>
                                                <tr style="color: #fff;background:#01265a;font-weight:bold;">
                                                    <td colspan="1" class="text-left">#</td>
                                                    <td colspan="1" class="text-left">@lang('common.earnings')</td>
                                                    <td colspan="1" class="text-right" align="right">
                                                        @lang('common.amount')</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{ $aInc = null }}
                                                @foreach ($allowances as $key => $allowance)
                                                    @if ($allowance > 0)
                                                        <tr style="color: #003c90;background:#fff">
                                                            <td colspan="1" class="text-left">{{ $aInc += 1 }}
                                                            </td>
                                                            <td colspan="1" class="text-left">@lang('payroll.' . $key)</td>
                                                            <td colspan="1" class="text-right" align="right">
                                                                {{ number_format($allowance, 3, '.', '') }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                <tr style="color: #003c90;background:#fff;font-weight:bold">
                                                    <td colspan="1" class="text-left">#</td>
                                                    <td colspan="1" class="text-left">@lang('payroll.total')</td>
                                                    <td colspan="1" class="text-right" align="right">
                                                        {{ number_format($gross_salary, 3, '.', '') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table>
                                            <thead>
                                                <tr style="color: #fff;background:#01265a;font-weight:bold;">
                                                    <td colspan="1" class="text-left">#</td>
                                                    <td colspan="1" class="text-left">@lang('common.deductions')</td>
                                                    <td colspan="1" class="text-right" align="right">
                                                        @lang('common.amount')</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{ $dInc = null }}
                                                @foreach ($deductions as $key => $deduction)
                                                    @if ($deduction > 0)
                                                        <tr style="color: #003c90;background:#fff">
                                                            <td colspan="1" class="text-left">{{ $dInc += 1 }}
                                                            </td>
                                                            <td colspan="1" class="text-left">@lang('payroll.' . $key)
                                                            </td>
                                                            <td colspan="1" class="text-right" align="right">
                                                                {{ number_format($deduction, 3, '.', '') }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                <tr style="color: #003c90;background:#fff;font-weight:bold">
                                                    <td colspan="1" class="text-left">#</td>
                                                    <td colspan="1" class="text-left">@lang('payroll.total')</td>
                                                    <td colspan="1" class="text-right" align="right">
                                                        {{ number_format($total_deduction, 3, '.', '') }}</td>
                                                </tr>
                                                <tr style="color: #003c90;background:#fff;font-weight:bold">
                                                    <td colspan="1" class="text-left">#</td>
                                                    <td colspan="1" class="text-left">@lang('payroll.arrears_adjustment')</td>
                                                    <td colspan="1" class="text-right" align="right">
                                                        {{ number_format($arrears_adjustment, 3, '.', '') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div style="margin: 8px 0;">
                                    <div class="col-md-6">
                                        <table>
                                            <tbody>
                                                <tr style="color: #003c90; background:#fff; font-weight:bold">
                                                    <td colspan="1" class="text-left">1</td>
                                                    <td colspan="1" class="text-left">@lang('payroll.total_earnings')</td>
                                                    <td colspan="1" class="text-right" align="right">
                                                        {{ number_format($gross_salary, 3, '.', '') }}</td>
                                                </tr>
                                                <tr style="color: #003c90; background:#fff; font-weight:bold;">
                                                    <td colspan="1" class="text-left">2</td>
                                                    <td colspan="1" class="text-left">@lang('payroll.total_deductions')</td>
                                                    <td colspan="1" class="text-right" align="right">
                                                        {{ number_format($total_deduction, 3, '.', '') }}</td>
                                                </tr>
                                                <tr style="color: #003c90; background:#fff; font-weight:bold;">
                                                    <td colspan="1" class="text-left">3</td>
                                                    <td colspan="1" class="text-left">@lang('payroll.arrear_adjustment')</td>
                                                    <td colspan="1" class="text-right" align="right">
                                                        {{ number_format($arrears_adjustment, 3, '.', '') }}</td>
                                                </tr>
                                                <tr style="color: #003c90; background:#fff; font-weight:bold;">
                                                    <td colspan="1" class="text-left">4</td>
                                                    <td colspan="1" class="text-left">@lang('payroll.net_salary')</td>
                                                    <td colspan="1" class="text-right" align="right">
                                                        {{ number_format($net_salary, 3, '.', '') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <footer position="sticky">
                                    <div class="clearFix padding" style="padding: 16px 16px;">
                                        <div class="col-md-2"
                                            style="text-align: left;font-weight:bold;color:#022f6f;background:#d2e5ff;padding:4px;font-size:14px">
                                            <strong>@lang('payroll.net_salary')</strong>
                                        </div>
                                        <div class="col-md-2"
                                            style="text-align: left;font-weight:bold;color:#022f6f;background:#d2e5ff;padding:4px;font-size:14px">
                                            {{ ': OMR ' }}<strong
                                                align="right">{{ number_format($net_salary, 3, '.', '') }}</strong>
                                        </div>
                                        <div class="col-md-8"
                                            style="text-align: left;font-weight:bold;color:#014109;background:#beffb4;padding:6px;font-size:12px;height:14px;">
                                            <strong>{{ '' }}</strong>
                                        </div>
                                    </div>
                                </footer>
                            </div>

                        </div>
                    </div>
                    <br>
                    <footer position="sticky">
                        <div class="clearFix padding">
                            <div class="col-md-full" style="text-align: center;">
                                <strong>This is a computer-generated document. No signature is required.</strong>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
