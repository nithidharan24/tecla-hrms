<?php
// Moved here from app/Export/InvoiceExport.php so PSR-4 autoload matches namespace App\Exports.
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoiceExport implements FromCollection, WithHeadings
{
    protected $invoice;
    protected $items;
    protected $taxDetails;

    public function __construct($invoice, $items, $taxDetails)
    {
        $this->invoice = $invoice;
        $this->items = $items;
        $this->taxDetails = $taxDetails;
    }

    public function collection()
    {
        // Format the data for CSV export
        $data = [
            [
                'Invoice ID' => $this->invoice->invoice_id,
                'Client Name' => $this->invoice->client_name,
                'Invoice Date' => $this->invoice->invoice_date,
                'Due Date' => $this->invoice->due_date,
                'Total Amount' => $this->invoice->total,
                'Tax Amount' => $this->invoice->tax_amt,
                'Discount' => $this->invoice->discount,
                'Grand Total' => $this->invoice->grant_amt,
            ]
        ];

        // Add items
        foreach ($this->items as $item) {
            $data[] = [
                'Item' => $item->item,
                'Description' => $item->description,
                'Unit Cost' => $item->unit_cost,
                'Quantity' => $item->quantity,
                'Amount' => $item->amount,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Field',
            'Value'
        ];
    }
}
