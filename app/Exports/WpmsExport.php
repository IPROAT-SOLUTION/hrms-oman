<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;

class WpmsExport implements FromCollection, WithHeadings, WithEvents

{
    use Exportable;
    protected $data;
    protected $heading;

    public function __construct($data, $heading)
    {
        $this->data = $data;
        $this->heading = $heading;
    }
    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->heading;
    }

    public function registerEvents(): array
    {
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
      
        $styleArray6 = array(
             //Set borders style
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('argb' => 'BFBFBF'),
                ),
            ),
            //Set font style
            'font' => [
                'name'      =>  'Calibri',
                // 'size'      =>  15,
                'bold'      =>  true,
                'color' => ['argb' => 'FF0000'],
            ],
            //Set background style
            'fill' => array(
                'fillType' => Fill::FILL_SOLID,
                'startColor' => array('argb' => 'E2EFDA'),
            ),
        );

        $styleArray7 = array(
             //Set borders style
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('argb' => 'BFBFBF'),
                ),
            ),
             //Set font style
             'font' => [
                'name'      =>  'Calibri',
                // 'size'      =>  15,
                'bold'      =>  true,
                'color' => ['argb' => '000000'],
            ],
            //Set background style
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
                $cellRange = 'A1:O1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(11);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray1);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray2);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray3);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray8);
                $event->sheet->setAutoFilter($cellRange);

                $NonMandCell = ['C', 'H', 'K', 'L', 'M', 'N', 'O'];
                $ColumnLength = isset($this->data[0]) ? count($this->data[0]) : 0;
                $RowLength = count($this->data) + 1;
                $cellLength = [];

                for ($x = 'A'; $x < 'ZZ'; $x++) {
                    array_push($cellLength, $x);
                    if ($x == 'O') {
                        break;
                    }
                }

                for ($i = 1; $i <= $RowLength; $i++) {
                    $cellRange = "A{$i}:O{$i}";
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
}
