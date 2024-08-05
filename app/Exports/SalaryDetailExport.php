<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SalaryDetailExport implements WithHeadings, FromCollection, WithProperties, WithEvents, WithColumnFormatting
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

        return [
            AfterSheet::class => function (AfterSheet $event) use (
                $styleArray1,
                $styleArray2,
                $styleArray3,
                $styleArray6,
                $styleArray7,
                $styleArray8
            ) {
                $cellLength = isset($this->extraData['heading'][0]) && gettype($this->extraData['heading'][0]) === 'array'  ? count($this->extraData['heading'][0]) : count($this->extraData['heading']);
                $cellRange = $cellLength == 13 ? 'A1:M1' : 'A1:AC1'; // All headers
                $lastCell =  $cellLength == 13 ? 'M' : 'AC';

                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(11);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray1);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray2);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray3);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray8);
                $event->sheet->setAutoFilter($cellRange);

                $NonMandCell = [];
                // dd(numberToColumnName(count($this->extraData[0])));

                $ColumnLength = isset($this->data[0]) ? count($this->data[0]) : 0;
                $RowLength = count($this->data ?? []) + 1;
                $cellLength = [];

                for ($x = 'A'; $x < 'ZZ'; $x++) {
                    array_push($cellLength, $x);
                    if ($x == $lastCell) {
                        break;
                    }
                }

                for ($i = 1; $i <= $RowLength; $i++) {
                    $cellRange = "A{$i}:$lastCell{$i}";
                    $event->sheet->getStyle($cellRange)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }

                foreach ($cellLength as $key => $value) {
                    if (in_array($value, $NonMandCell)) {
                        $event->sheet->getStyle("{$value}1")->ApplyFromArray($styleArray7);
                    } else {
                        $event->sheet->getStyle("{$value}1")->ApplyFromArray($styleArray6);
                    }
                }

                for ($i = 1; $i <= $RowLength; $i++) {
                    for ($j = 1; $j <= $ColumnLength; $j++) {
                        $column = Coordinate::stringFromColumnIndex($j);
                        $event->sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL,
        ];
    }

    public function properties(): array
    {
        return [
            'creator' => 'muscat-insurance' . auth()->user()->user_name,
            'lastModifiedBy' => 'muscat-insurance ' . auth()->user()->user_name,
            'title' => 'EmployeeInfo',
            'description' => 'muscat-insurance  - EmployeeInfo',
            'subject' => 'muscat-insurance - EmployeeInfo',
            'keywords' => 'EmployeeInfo,export,spreadsheet',
            'category' => 'EmployeeInfo',
            'manager' => 'muscat-insurance',
            'company' => 'muscat-insurance',
        ];
    }
}
