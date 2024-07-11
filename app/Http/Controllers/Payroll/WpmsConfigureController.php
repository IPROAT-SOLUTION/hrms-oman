<?php

namespace App\Http\Controllers\Payroll;

use App\Model\Branch;
use App\Exports\WpmsExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\Wps;
use App\Model\SalaryDetails;
use Maatwebsite\Excel\Facades\Excel;

class WpmsConfigureController extends Controller
{
    public function index(Request $request)
    {
        $results = [];
        $branchList = Branch::get();

        if ($_POST) {

            $branch = $request->branch_id;
            $month = $request->month;
            $results = SalaryDetails::with(['employee'])->where('net_salary', '>=', 1);

            if ($branch) {
                $results =  $results->where('branch_id', $branch);
            }

            $results =  $results->where('month_of_salary', $month)->get();

            foreach ($results as $key => $value) {
                if ($value->employee->document_title11) {
                    $value['employee_document_type'] = $value->employee->document_title11;
                    $value['employee_id_type'] = Wps::$CITIZENSHIP_CARD;
                } else {
                    $value['employee_document_type'] = $value->employee->document_title8;
                    $value['employee_id_type'] = Wps::$PASSPORT;
                }

                $value->total_deductions = number_format($value->total_deductions - $value->social_security, 3, '.', '');

                if (abs($value->social_security) == 0 && abs($value->total_deductions) == 0) {
                    $value['notes_comments'] = Wps::$SALARY;
                } elseif (abs($value->social_security) == 0 &&  abs($value->total_deductions) != 0) {
                    $value['notes_comments'] = Wps::$SALARY_WITH_DEDUCTIONS;
                } elseif (abs($value->social_security) != 0 &&  abs($value->total_deductions) != 0) {
                    $value['notes_comments'] =  Wps::$SALARY_WITH_DEDUCTIONS_AND_SOCIAL_SECURITY;
                } elseif (abs($value->social_security) != 0) {
                    $value['notes_comments'] =  Wps::$SALARY_WITH_SOCIALSECURITY;
                } else {
                    $value['notes_comments'] = Wps::$DEFAULT;
                }
            }

            return view('admin.payroll.wpms.index', ['results' => $results, 'branchList' => $branchList, 'branch_id' => $request->branch_id, 'month' => $request->month]);
        }
        return view('admin.payroll.wpms.index', ['results' => $results, 'branchList' => $branchList, 'branch_id' => $request->branch_id, 'month' => $request->month]);
    }

    public function wpsPdfDownload(Request $request)
    {
        // dd(base64_encode(file_get_contents('admin_assets/img/logo.png')));
        $branch = $request->branch_id;
        $month = $request->month;
        $results = SalaryDetails::with(['employee'])->where('net_salary', '>=', 1);

        if ($branch) {
            $results =  $results->where('branch_id', $branch);
        }

        $results =  $results->where('month_of_salary', $month)->get();

        foreach ($results as $key => $value) {
            if ($value->employee->document_title11) {
                $value['employee_document_type'] = $value->employee->document_title11;
                $value['employee_id_type'] = Wps::$CITIZENSHIP_CARD;
            } else {
                $value['employee_document_type'] = $value->employee->document_title8;
                $value['employee_id_type'] = Wps::$PASSPORT;
            }

            $value->total_deductions = number_format($value->total_deductions - $value->social_security, 3, '.', '');

            if (abs($value->social_security) == 0 && abs($value->total_deductions) == 0) {
                $value['notes_comments'] = Wps::$SALARY;
            } elseif (abs($value->social_security) == 0 &&  abs($value->total_deductions) != 0) {
                $value['notes_comments'] = Wps::$SALARY_WITH_DEDUCTIONS;
            } elseif (abs($value->social_security) != 0 &&  abs($value->total_deductions) != 0) {
                $value['notes_comments'] =  Wps::$SALARY_WITH_DEDUCTIONS_AND_SOCIAL_SECURITY;
            } elseif (abs($value->social_security) != 0) {
                $value['notes_comments'] =  Wps::$SALARY_WITH_SOCIALSECURITY;
            } else {
                $value['notes_comments'] = Wps::$DEFAULT;
            }
        }

        $data = ['results' => $results, 'branch_id' => $request->branch_id, 'month' => $request->month];

        $pdf = \PDF::loadView('admin.payroll.wpms.pdf.wpmsPdf', $data);
        $pdf->setPaper('A3', 'landscape');
        return $pdf->download("wpms-report" .  $request->month . ".pdf");
    }
    public function wpsExcelDownload(Request $request)
    {
        $dataSet = [];
        $column = [
            "Employee ID Type",
            "Employee ID",
            "Reference Number",
            "Employee Name",
            "Employee BIC",
            "Employee Account/IBAN",
            "Salary Frequency",
            "No of Working Days",
            "Net Salary",
            "Basic Salary",
            "Extra Hours",
            "Extra Income",
            "Deductions",
            "Social Security Detections",
            "Notes/Comments",
        ];
        $heading[] = $column;

        if ($request->has('month')) {
            $data = SalaryDetails::with('employee')->where('month_of_salary', $request->month)->get();
        } else {
            $data = SalaryDetails::with('employee')->get();
        }
        foreach ($data as $value) {
            if ($value->net_salary != 0) {
                $employee_id_type = $this->getEmployeeIdType($value);
                $notes_comments = $this->getNotesComments($value);

                $dataSet[] = [
                    $employee_id_type['employee_id_type'],
                    $employee_id_type['employee_document_type'],
                    isset($value->month_of_salary) ? trim('Salary ' . date('F Y', strtotime($value->month_of_salary))) : 'Salary -',
                    isset($value->employee) ? $value->employee->fullname() : '-',
                    isset($value->employee) ? $value->employee->ifsc_number : '-',
                    isset($value->employee) ? $value->employee->account_number : '-',
                    'M',
                    isset($value->total_working_days) ? $value->total_working_days : '-',
                    isset($value->net_salary) ? $value->net_salary : '-',
                    isset($value->basic_salary) ? $value->basic_salary : '-',
                    isset($value->extra_hours) ? $value->extra_hours : '-',
                    isset($value->extra_amount) ? $value->extra_amount : '-',
                    isset($value->total_deductions) ? $value->total_deductions : '-',
                    isset($value->social_security) ? $value->social_security : '-',
                    $notes_comments,
                ];
            }
        }




        return  Excel::download(new WpmsExport($dataSet, $heading), 'Wpms_' . date('d_m_Y') . '.xlsx');
    }
    public function getEmployeeIdType($item)
    {
        if ($item->employee && isset($item->employee->document_title8) && $item->employee->document_title8 !== '') {
            $value['employee_document_type'] = $item->employee->document_title8;
            $value['employee_id_type'] = Wps::$CITIZENSHIP_CARD;
            return $value;
        } elseif ($item->employee && isset($item->employee->document_title11) && $item->employee->document_title11 !== '') {
            $value['employee_document_type'] = $item->employee->document_title11;
            $value['employee_id_type'] = Wps::$CITIZENSHIP_CARD;
            return $value;
        } else {
            $value['employee_document_type'] = '-';
            $value['employee_id_type'] =  Wps::$DEFAULT;
            return $value;
        }
    }


    public function getNotesComments($item)
    {
        $item->total_deductions = number_format($item->total_deductions - $item->social_security, 3, '.', '');

        if (abs($item->social_security) == 0 && abs($item->total_deductions) == 0) {
            return Wps::$SALARY;
        } elseif (abs($item->social_security) == 0 &&  abs($item->total_deductions) != 0) {
            return Wps::$SALARY_WITH_DEDUCTIONS;
        } elseif (abs($item->social_security) != 0 &&  abs($item->total_deductions) != 0) {
            return Wps::$SALARY_WITH_DEDUCTIONS_AND_SOCIAL_SECURITY;
        } elseif (abs($item->social_security) != 0) {
            return Wps::$SALARY_WITH_SOCIALSECURITY;
        } else {
            return Wps::$DEFAULT;
        }
    }
    public function SalaryDetails()
    {
    }
}
