<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class EmployeeDetailsExport implements WithHeadings, FromCollection, WithProperties, WithEvents, WithColumnFormatting
{

    public $data;
    public $extraData;

    public function __construct($data, $extraData)
    {
        $this->data = $data;
        $this->extraData = $extraData;
    }

    public function headings(): array
    {
        return $this->extraData['heading'];
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function registerEvents(): array
    {

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $styleArray1 = [
            'font' => [
                'bold' => true,
            ],
        ];

        $styleArray2 = array(
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleArray3 = array(
            'alignment' => array(
                'vertical' => Alignment::VERTICAL_CENTER,
            ),
        );

        $styleArray4 = array(
            'fill' => [
                'fillType' => Fill::FILL_GRADIENT_LINEAR,
                'startColor' => [
                    'argb' => 'FFA0A0A0',
                ],
                'endColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        );

        $styleArray5 = array(
            'fill' => [
                'fillType' => Fill::FILL_SOLID,

                'startColor' => [
                    'argb' => 'E0E0E0',
                ],
            ],
        );

        $styleArray6 = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('argb' => 'BFBFBF'),
                ),
            ),
            'fill' => array(
                'fillType' => Fill::FILL_SOLID,
                'startColor' => array('argb' => 'E2EFDA'),
            ),
        );

        $styleArray7 = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('argb' => 'BFBFBF'),
                ),
            ),
            'fill' => array(
                'fillType' => Fill::FILL_SOLID,
                'startColor' => array('argb' => 'DDEBF7'),
            ),
        );

        $styleArray8 = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => array('argb' => '000000'),
                ),
            ),
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleArray9 = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('argb' => 'BFBFBF'),
                ),
            ),
            'fill' => array(
                'fillType' => Fill::FILL_SOLID,
                'startColor' => array('argb' => '116530'),
            ),
        );

        $styleArray10 = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('argb' => 'BFBFBF'),
                ),
            ),
            'fill' => array(
                'fillType' => Fill::FILL_SOLID,
                'startColor' => array('argb' => '05445e'),
            ),
        );

        $styleArray11 = [
            'font' => [
                'bold' => true,
                'color' => array('argb' => 'FFFFFF'),
            ],
        ];

        $styleArray12 = [
            'font' => [
                'bold' => true,
                'color' => array('argb' => 'FFFFFF'),
            ],
            'fill' => array(
                'fillType' => Fill::FILL_SOLID,
                'startColor' => array('argb' => 'ed7b7b'),
            ),
        ];

        return [
            AfterSheet::class => function (AfterSheet $event) use (
                $styleArray1,
                $styleArray2,
                $styleArray3,
                $styleArray6,
                $styleArray7,
                $styleArray8,
                $styleArray11,
                $styleArray12
            ) {
                ob_start();

                $cellRange = 'A1:AY1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(11);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray1);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray2);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray3);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray8);
                $event->sheet->setAutoFilter($cellRange);

                $nonMandCell =  $mandatoryCell = [];

                $columnLength = count($this->extraData['heading'][0] ?? []);
                $rowLength = count($this->data) + 1;
                $cellLength = [];
                $cellRange = 'A1';
                $headingCellRange = 'A1';
                $i = 1;
                // dump([$rowLength, $columnLength]);

                for ($x = 'A'; $x < 'ZZ'; $x++) {
                    array_push($cellLength, $x);
                    if ($i == $columnLength) {
                        $cellRange .= ':' . $x . $rowLength;
                        $headingCellRange .= ':' . $x . '1';
                        break;
                    }

                    $i++;
                }

                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(11);
                $event->sheet->getStyle($headingCellRange)->ApplyFromArray($styleArray1);
                $event->sheet->getStyle($headingCellRange)->ApplyFromArray($styleArray2);
                $event->sheet->getStyle($headingCellRange)->ApplyFromArray($styleArray3);
                $event->sheet->getStyle($headingCellRange)->ApplyFromArray($styleArray8);
                $event->sheet->setAutoFilter($headingCellRange);

                // $mandatoryCell = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'K', 'L', "M", 'N', 'Q', 'R', 'S', 'Z', 'AA', 'AB'];

                for ($i = 1; $i <= $rowLength; $i++) {
                    $rangeFrom = $cellLength[0] ?? 'A';
                    $rangeUpTo = $cellLength[count($cellLength) - 1] ?? 'Z';
                    $cellRange = "{$rangeFrom}{$i}:{$rangeUpTo}{$i}";
                    $event->sheet->getStyle($cellRange)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }

                foreach ($cellLength as $key => $value) {
                    if (!in_array($value, $mandatoryCell)) {
                        $event->sheet->getStyle("{$value}1")->ApplyFromArray($styleArray7);
                    } else {
                        $event->sheet->getStyle("{$value}1")->ApplyFromArray($styleArray12);
                    }
                }

                for ($i = 1; $i <= $rowLength; $i++) {
                    for ($j = 1; $j <= $columnLength; $j++) {
                        $column = Coordinate::stringFromColumnIndex($j);
                        $event->sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }

                $employee_category = employee_category();
                $employee_marital_status = employee_marital_status();
                $employee_gender = employee_gender();
                $yes_or_no = yes_or_no();
                $employee_status = employee_status();
                $employee_faith = employee_faith();
                $employee_nationality = employee_nationality();

                // Master Lists
                // $role_list = DB::table('role')->pluck('role_name')->toArray();
                // $department_list = DB::table('department')->pluck('department_name')->toArray();
                // $designation_list = DB::table('designation')->limit(25)->pluck('designation_name')->toArray();
                // $branch_list = DB::table('branch')->pluck('branch_name')->toArray();
                // $employee_list = DB::table('employee')
                //     ->join('user', 'user.user_id', 'employee.user_id')->take(30)
                //     ->pluck('user.user_name')->toArray();

                $drop_column = [

                    ['cell' => 'P', 'options' => $employee_gender],
                    ['cell' => 'Q', 'options' => $employee_marital_status],
                    ['cell' => 'AX', 'options' => $yes_or_no],
                    ['cell' => 'AY', 'options' => $yes_or_no],
                    ['cell' => 'AV', 'options' => $employee_status],
                    ['cell' => 'V', 'options' => $employee_category],
                    ['cell' => 'T', 'options' => $employee_faith],
                    ['cell' => 'U', 'options' => $employee_nationality],

                    // Master Lists
                    // ['cell' => 'C', 'options' => $role_list],
                    // ['cell' => 'E', 'options' => $department_list],
                    // ['cell' => 'F', 'options' => $designation_list],
                    // ['cell' => 'G', 'options' => $branch_list],
                    // ['cell' => 'G', 'options' => $branch_list],
                    // ['cell' => 'H', 'options' => $employee_list],
                    // ['cell' => 'I', 'options' => $employee_list],
                ];

                for ($i = 2; $i <= $rowLength; $i++) {
                    foreach ($drop_column as $data) {
                        $validation1 = $event->sheet->getCell("{$data["cell"]}$i")->getDataValidation();
                        $validation1->setType(DataValidation::TYPE_LIST);
                        $validation1->setErrorStyle(DataValidation::STYLE_INFORMATION);
                        $validation1->setAllowBlank(true);
                        $validation1->setShowInputMessage(true);
                        $validation1->setShowErrorMessage(true);
                        $validation1->setShowDropDown(true);
                        $validation1->setErrorTitle('Input error');
                        $validation1->setError('Value is not in list.');
                        $validation1->setPromptTitle('Pick from list');
                        $validation1->setPrompt('Please pick a value from the drop-down list.');
                        $validation1->setFormula1(sprintf('"%s"', implode(', ', $data['options'])));
                    }
                }
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_GENERAL,
            'I' => NumberFormat::FORMAT_GENERAL,
            'J' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_TEXT,
            'X' => NumberFormat::FORMAT_TEXT,
            'Z' => NumberFormat::FORMAT_TEXT,
            'AB' => NumberFormat::FORMAT_TEXT,
            'AD' => NumberFormat::FORMAT_TEXT,
            'AF' => NumberFormat::FORMAT_TEXT,
            'AG' => NumberFormat::FORMAT_TEXT,
            'AW' => NumberFormat::FORMAT_GENERAL,
        ];
    }

    public function properties(): array
    {
        return [
            'title' => 'employee-info',
            'description' => 'muscat-insurance-EmployeeInfo',
            'subject' => 'muscat-insurance-employee-info',
            'keywords' => 'employee-info,export,spreadsheet,employee-template,bulk-export,employee-export,download-employee-info',
            'creator' => 'muscat-insurance-' . auth()->user()->user_name,
            'sheet_type' => 'employee-info',
            'company' => 'muscat-insurance',
        ];
    }
}
