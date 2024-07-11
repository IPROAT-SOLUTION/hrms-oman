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
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class SocialSecuritySummaryExport implements FromCollection, WithHeadings, WithEvents

{
    use Exportable;
    protected $data;
    protected $heading;
    public $merge;

    public function __construct($data, $heading, $merge)
    {
        $this->data = $data;
        $this->heading = $heading;
        $this->merge = $merge;
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
        return [
            AfterSheet::class    => function (AfterSheet $event) {

                $event->sheet->getDelegate()->setMergeCells($this->merge);
                foreach($this->merge as $cell){
                    $event->sheet->getDelegate()->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

            }
        ];
    }
}
