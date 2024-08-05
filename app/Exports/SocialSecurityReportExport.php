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

class SocialSecurityReportExport implements WithHeadings, FromCollection, WithProperties, WithEvents, WithColumnFormatting
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
        return $this->extraData;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function registerEvents(): array
    {

        

        return [
            
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

