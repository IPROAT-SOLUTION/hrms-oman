<!DOCTYPE html>
<html lang="en">

<head>
    <title> @lang('attendance.monthly_attendance')</title>
    <meta charset="utf-8">
</head>
<style>
    .printHead {
        width: 100%;
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    th {
        background-color: #D2D2D2;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #watermark {
        position: fixed;
        bottom: 25px;
        right: 25px;
        opacity: 0.2;
        z-index: 99;
        color: white;
    }
</style>

<body>
    <div class="container">
        <div style="text-align: center;width:100%;text-align:center">
            <h3 style="margin-top: 10px;"><b>WPMS SHEET {{ "($month)" }}</b></h3>
        </div>
        <div class="table-responsive">
            <div class="table-responsive">
                <img id="watermark" width="100" height="100" src="{{ asset('admin_assets/img/logo.png') }}"
                    alt="watermark">
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
</body>

</html>
