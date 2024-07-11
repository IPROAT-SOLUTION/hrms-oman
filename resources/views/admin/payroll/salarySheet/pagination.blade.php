<div class="table-responsive">
    <table id="salarySheetTable" class="table table-bordered manage-u-table">
        <thead>
            <tr class="tr_header" style="white-space: nowrap">
                <th>@lang('common.serial')</th>
                <th>@lang('common.month')</th>
                <th>@lang('payroll.name')</th>
                <th>@lang('common.employee_id')</th>
                <th>@lang('common.department')</th>
                <th>@lang('common.designation')</th>
                <th>@lang('payroll.nationality')</th>
                <th>@lang('common.branch')</th>
                <th>@lang('payroll.bank')</th>
                <th>@lang('payroll.basic_salary')</th>
                <th>@lang('payroll.increment_amount')</th>
                <th>@lang('payroll.basic_salary')</th>
                <th>@lang('payroll.housing_allowance')</th>
                <th>@lang('payroll.utility_allowance')</th>
                <th>@lang('payroll.transport_allowance')</th>
                <th>@lang('payroll.living_allowance')</th>
                <th>@lang('payroll.mobile_allowance')</th>
                <th>@lang('payroll.special_allowance')</th>
                <th>@lang('payroll.membership_allowance')</th>
                <th>@lang('payroll.education_and_club_allowance')</th>
                <th>@lang('payroll.arrears_adjustment')</th>
                <th>@lang('payroll.gross_salary')</th>
                <th>@lang('payroll.lop')</th>
                <th>@lang('payroll.pay_cut')</th>
                <th>@lang('payroll.gsm')</th>
                <th>@lang('payroll.prem_others')</th>
                <th>@lang('payroll.salary_advance')</th>
                <th>@lang('payroll.social_security')</th>
                <th>@lang('payroll.total_deduction')</th>
                <th>@lang('payroll.net_salary')</th>
                <th>@lang('common.action')</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = 0 @endphp <!-- Initialize the serial number -->
            @foreach ($results as $key => $value)
                <tr>
                    <td style="width: 100px;">{{ ++$sl }}</td> <!-- Increment the serial number -->
                    <td>
                        {{ date('F Y', strtotime($value->month_of_salary)) }}
                    </td>
                    <td>{!! $value->employee->fullname() !!}</td>
                    <td>{!! $value->employee->finger_id !!}</td>
                    <td>{!! $value->employee->departmentName() !!}</td>
                    <td>{!! $value->employee->designationName() !!}</td>
                    <td>{!! $value->employee->nationality == 0 ? 'Omani' : 'Expatriate' !!}</td>
                    <td>{!! $value->employee->branchName() !!}</td>
                    <td>{!! $value->name_of_the_bank !!}</td>
                    <td>{!! $value->basic_salary - $value->increment_amount !!}</td>
                    <td>{!! $value->increment_amount !!}</td>
                    <td>{!! $value->basic_salary !!}</td>
                    <td>{!! $value->housing_allowance !!}</td>
                    <td>{!! $value->utility_allowance !!}</td>
                    <td>{!! $value->transport_allowance !!}</td>
                    <td>{!! $value->living_allowance !!}</td>
                    <td>{!! $value->mobile_allowance !!}</td>
                    <td>{!! $value->special_allowance !!}</td>
                    <td>{!! $value->membership_allowance !!}</td>
                    <td>{!! $value->education_and_club_allowance !!}</td>
                    <td>{!! $value->arrears_adjustment !!}</td>
                    <td>{!! $value->gross_salary !!}</td>
                    <td>{!! $value->lop !!}</td>
                    <td>{!! $value->pay_cut !!}</td>
                    <td>{!! $value->gsm !!}</td>
                    <td>{!! $value->prem_others !!}</td>
                    <td>{!! $value->salary_advance !!}</td>
                    <td>{!! $value->social_security !!}</td>
                    <td>{!! $value->total_deductions !!}</td>
                    <td>{!! $value->net_salary !!}</td>
                    <td> <button class="btn btn-xs btn-primary payslip" title="download payslip" type="button"
                            data-status={{ $value->salary_details_id }}>Payslip</button> </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
