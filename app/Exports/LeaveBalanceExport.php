<?php

namespace App\Exports;

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
use Maatwebsite\Excel\Concerns\WithColumnFormatting;


class LeaveBalanceExport implements WithHeadings, FromCollection, WithProperties, WithEvents, WithColumnFormatting
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

        //border style
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    //'color' => ['argb' => 'FFFF0000'],
                ],
            ],
        ];

        //font style
        $styleArray1 = [
            'font' => [
                'bold' => true,
            ],
        ];

        //column  text alignment
        $styleArray2 = array(
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ),
        );

        //$styleArray3 used for vertical alignment
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
                'color' => array('argb' => 'DDEBF7'),
                // 'startColor' => array('argb' => 'DDEBF7'),
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

        $styleArray13 = [
            'font' => [
                'bold' => true,
                'color' => array('argb' => 'FFFFFF'),
            ],
            'fill' => array(
                'fillType' => Fill::FILL_SOLID,
                // 'startColor' => array('argb' => 'ed7b7b'),
                'color' => array('argb' => 'ed7b7b'),
            ),
        ];

        return [
            AfterSheet::class => function (AfterSheet $event) use (
                $styleArray,
                $styleArray1,
                $styleArray2,
                $styleArray3,
                $styleArray4,
                $styleArray5,
                $styleArray6,
                $styleArray7,
                $styleArray8,
                $styleArray9,
                $styleArray10,
                $styleArray11,
                $styleArray13
            ) {

                $from_cell = 'A';
                $to_cell = null;

                if (isset($this->data[array_key_first($this->data)])) {
                    $to_cell =  numberToColumnName(count($this->data[array_key_first($this->data)]));
                }

                if ($to_cell) {
                    $cell_range = "{$from_cell}1:{$to_cell}1"; // All headers
                    $event->sheet->getDelegate()->getStyle($cell_range)->getFont()->setSize(11);
                    $event->sheet->getStyle($cell_range)->ApplyFromArray($styleArray1);
                    $event->sheet->getStyle($cell_range)->ApplyFromArray($styleArray2);
                    $event->sheet->getStyle($cell_range)->ApplyFromArray($styleArray3);
                    $event->sheet->getStyle($cell_range)->ApplyFromArray($styleArray8);
                    $event->sheet->setAutoFilter($cell_range);

                    $nonMandCell = [
                        'A', 'B', 'D', 'E', 'F', 'G', 'H', 'I'
                    ];

                    $column_count = isset($this->data[array_key_first($this->data)]) ? count($this->data[array_key_first($this->data)]) : 0;
                    $row_count = count($this->data) + 1;
                    $cell_length = [];

                    for ($x = 'A'; $x < 'ZZ'; $x++) {
                        array_push($cell_length, $x);
                        if ($x == "$to_cell") {
                            break;
                        }
                    }

                    for ($i = 1; $i <= $row_count; $i++) {
                        $cell_range = "A{$i}:$to_cell{$i}";
                        $event->sheet->getStyle($cell_range)->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    }

                    foreach ($cell_length as $key => $value) {
                        if (!in_array($value, $nonMandCell)) {
                            $event->sheet->getStyle("{$value}1")->ApplyFromArray($styleArray13);
                        } else {
                            $event->sheet->getStyle("{$value}1")->ApplyFromArray($styleArray7);
                        }
                    }

                    // set columns to autosize
                    for ($i = 1; $i <= $column_count; $i++) {
                        $column = Coordinate::stringFromColumnIndex($i);
                        $event->sheet->getColumnDimension($column)->setAutoSize(true);
                    }

                    for ($i = 1; $i <= $row_count; $i++) {
                        for ($j = 1; $j <= $column_count; $j++) {
                            $column = Coordinate::stringFromColumnIndex($j);
                            $event->sheet->getColumnDimension($column)->setAutoSize(true);
                        }
                    }
                }
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_GENERAL,
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
