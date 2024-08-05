<?php

namespace App\Http\Controllers\Employee;

use App\Model\Employee;
use Illuminate\Http\Request;
use App\Imports\EmployeeImport;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Http\Requests\FileUploadRequest;
use App\Repositories\EmployeeRepository;
use Maatwebsite\Excel\Facades\Excel as Excel;

class EmployeeImportController extends Controller
{
    protected $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function import(FileUploadRequest $request)
    {
        try {
            $file = $request->file('select_file');
            $sheet_finger_ids = $sheet_email_ids = $duplicate_email_ids = $duplicate_finger_ids = [];
            $data = Excel::toArray(new EmployeeImport, $file);

            if (isset($data[0])) { // sheet one
                $sheet = $data[0]; // get sheet one by default
                $sheet_finger_ids = array_column($sheet, '3');   // get finger_id value column as a array #cell D - No 3
                $sheet_email_ids = array_column($sheet, '10');   // get email ids value column as a array #cell K - No 10
                $sheet_finger_ids = array_filter($sheet_finger_ids, static function ($val) {
                    return $val !== null;
                });
                $sheet_email_ids = array_filter($sheet_email_ids, static function ($val) {
                    return $val !== null;
                });
            }


            /* ------------------------Duplicate Employee Finger Id----------------------------- */
            $sheet_finger_id_count = array_count_values($sheet_finger_ids);

            foreach ($sheet_finger_id_count as $key => $value) {
                if ($value > 1) {
                    $duplicate_finger_ids[] = $key;
                }
            }

            if (!empty($duplicate_finger_ids)) {
                return redirect('employee')->with('error', 'Duplicate employee ids found, ' . implode(',', $duplicate_finger_ids));
            }
            /* ------------------------Duplicate Employee Finger Id------------------------------- */


            /* ------------------------Duplicate Employee Email Id--------------------------------- */
            $sheet_email_count = array_count_values($sheet_email_ids);

            foreach ($sheet_email_count as $key => $value) {
                if ($value > 1) {
                    $duplicate_email_ids[] = $key;
                }
            }
           
            if (!empty($duplicate_email_ids)) {
                return redirect('employee')->with('error', 'Duplicate emails found, ' . implode(',', $duplicate_email_ids));
            }
            /* -------------------------Duplicate Employee Email Id--------------------------------- */

            Excel::import(new EmployeeImport($request->all()), $file);
            return back()->with('success', 'Employee information imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $import = new EmployeeImport();
            $import->import($file);

            foreach ($import->failures() as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }
        return back()->with('success', 'Employee information imported successfully.');
    }
}
