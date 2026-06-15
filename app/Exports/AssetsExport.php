<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetsExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithTitle,
    WithEvents
{
    protected $assets;
    protected $filters;

    public function __construct($assets, $filters)
    {
        $this->assets = $assets;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->assets;
    }

    public function headings(): array
    {
        return [
            'Asset ID',
            'Asset Name',
            'Assigned To',
            'Designation',
            'Branch',
            'Purchase Date',
            'Value',
            'Status',
            'Manufacturer',
            'Model',
            'Serial Number',
            'Condition',
            'Warranty'
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->asset_id,
            $asset->asset_name,
            $asset->firstname ? $asset->firstname.' '.$asset->lastname : 'Unassigned',
            $asset->designation_name ?? 'N/A',
            $asset->name ?? 'N/A',
            $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d/m/Y') : 'N/A',
            ''.number_format($asset->value, 2),
            ucfirst($asset->status),
            $asset->manufacturer ?? 'N/A',
            $asset->model ?? 'N/A',
            $asset->serial_number ?? 'N/A',
            $asset->condition ?? 'N/A',
            $asset->warranty ?? 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            
            // Set column widths
            'A' => ['width' => 15],
            'B' => ['width' => 25],
            'C' => ['width' => 25],
            'D' => ['width' => 20],
            'E' => ['width' => 20],
            'F' => ['width' => 15],
            'G' => ['width' => 15],
            'H' => ['width' => 15],
            'I' => ['width' => 20],
            'J' => ['width' => 20],
            'K' => ['width' => 20],
            'L' => ['width' => 20],
            'M' => ['width' => 15],
            
            // Add borders
            'A1:M1' => ['borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]],
        ];
    }

    public function title(): string
    {
        return 'Assets Report';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Get the sheet object
                $sheet = $event->sheet->getDelegate();
                
                // Get the highest row number
                $highestRow = $sheet->getHighestRow();
                
                // Add filters information after the data
                $sheet->insertNewRowBefore($highestRow + 2, 7);
                
                $sheet->setCellValue('A'.($highestRow + 2), 'Filters Applied:');
                $sheet->setCellValue('A'.($highestRow + 3), 'Employee Name:');
                $sheet->setCellValue('B'.($highestRow + 3), $this->filters['employee_name'] ?? 'All');
                $sheet->setCellValue('A'.($highestRow + 4), 'Status:');
                $sheet->setCellValue('B'.($highestRow + 4), $this->filters['status'] ?? 'All');
                $sheet->setCellValue('A'.($highestRow + 5), 'Branch:');
                $sheet->setCellValue('B'.($highestRow + 5), $this->filters['branch'] ?? 'All');
                $sheet->setCellValue('A'.($highestRow + 6), 'Date Range:');
                $sheet->setCellValue('B'.($highestRow + 6), 
                    ($this->filters['from_date'] ?? 'N/A') . ' to ' . ($this->filters['to_date'] ?? 'N/A'));
                $sheet->setCellValue('A'.($highestRow + 8), 'Report Generated At:');
                $sheet->setCellValue('B'.($highestRow + 8), $this->filters['generated_at']);
                
                // Style the footer
                $sheet->getStyle('A'.($highestRow + 2).':B'.($highestRow + 8))->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
            },
        ];
    }
}