<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class ReportFromBladeView implements FromView, WithEvents
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


        return [
            AfterSheet::class => function (AfterSheet $event) use (
                $styleArray1,
                $styleArray2,
                $styleArray3
            ) {

                $cellRange = 'A2:M2';
                $cellRangeTitle = 'A1:M1';

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
