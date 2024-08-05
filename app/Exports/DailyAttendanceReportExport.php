<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class DailyAttendanceReportExport implements FromView, WithEvents
{
    use RegistersEventListeners;

    public $data;
    public $view;

    public function __construct($view, $data)
    {
        $this->data = $data;
        $this->view = $view;
    }

    public function view(): View
    {
        \set_time_limit(0);
        return view($this->view, $this->data);
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

        return [
            AfterSheet::class => function (AfterSheet $event) use (
                $styleArray1,
                $styleArray2,
                $styleArray3,
                $styleArray6,
                $styleArray7,
                $styleArray8,
                $styleArray11
            ) {

                $cellRange = 'A2:N2';
                $cellRangeTitle = 'A1:N1';

                $event->sheet->getDelegate()->mergeCells($cellRangeTitle);
                $event->sheet->getStyle($cellRangeTitle)->ApplyFromArray($styleArray2);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(11);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray1);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray3);
                $event->sheet->setAutoFilter($cellRange);

                $rowLength =  0;
                $columnLength = 13;

                foreach ($this->data ?? [] as $key => $department) {
                    $array = (array) $department;
                    foreach ($array as $key => $value) {
                        $rowLength++;
                    }
                }

                for ($i = 1; $i <= $rowLength; $i++) {
                    for ($j = 1; $j <= $columnLength; $j++) {
                        $column = Coordinate::stringFromColumnIndex($j);
                        $event->sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }
        ];
    }
}
