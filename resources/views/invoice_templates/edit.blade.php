@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Edit Invoice Template</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('invoice-template.index') }}">Invoice Templates</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('invoice-template.update', $invoiceTemplate->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="name">Template Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $invoiceTemplate->name) }}" placeholder="Enter template name" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="content">Template Content (HTML/Blade) <span class="text-danger">*</span></label>
                            @include('hrms.master.letters.partials.load-default-template', ['templateFile' => 'invoice-template.blade.php', 'buttonText' => 'Load Default'])
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="30" required>{{ old('content', $invoiceTemplate->content) }}</textarea>
                            @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                Available variables: <code>@{{ $invoice->invoice_number }}</code>, <code>@{{ $invoice->invoice_date }}</code>, <code>@{{ $customer->name }}</code>, <code>@{{ $customer->address }}</code>, <code>@{{ $items }}</code>, <code>@{{ $grandTotal }}</code>, <code>@{{ $taxAmount }}</code>, <code>@{{ $companyName }}</code>, <code>@{{ $logoDataUri }}</code>, <code>@{{ $offerSignatureDataUri }}</code>, <code>@{{ $gst }}</code>, <code>@{{ $bankName }}</code>, etc.
                            </small>
                        </div>

                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Update</button>
                            <a href="{{ route('invoice-template.index') }}" class="btn btn-secondary submit-btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadDefaultTemplate() {
    const defaultTemplate = `<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>TAX INVOICE</title>
    <style>
        @page { margin: 20px; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #000;
            font-size: 12px;
            line-height: 1.5;
        }
        .container { max-width: 900px; margin: 0 auto; }
        .header-section { text-align: center; margin-bottom: 10px; }
        .company-logo { margin-bottom: 10px; }
        .company-logo img { max-width: 120px; height: auto; }
        .header-title { font-weight: bold; font-size: 18px; margin-bottom: 5px; color: #FF5722; text-decoration: underline; }
        .header-divider { border-top: 2px solid #FF5722; margin: 10px 0; }
        
        .invoice-header { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px; }
        .company-details { }
        .invoice-details { text-align: right; }
        .company-details p, .invoice-details p { margin: 2px 0; font-size: 11px; }
        .company-details strong, .invoice-details strong { display: block; font-weight: bold; }
        
        .customer-section { margin-bottom: 15px; }
        .customer-section h4 { margin: 0 0 5px 0; font-size: 12px; font-weight: bold; text-decoration: underline; }
        .customer-info { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .customer-info p { margin: 2px 0; font-size: 11px; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .items-table th { background-color: #FF5722; color: white; padding: 8px; text-align: left; font-weight: bold; font-size: 11px; border: 1px solid #000; }
        .items-table td { padding: 8px; border: 1px solid #000; font-size: 11px; }
        .items-table .text-right { text-align: right; }
        .items-table tr:nth-child(even) { background-color: #f9f9f9; }
        
        .totals-section { display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; margin-bottom: 15px; }
        .terms { font-size: 10px; line-height: 1.4; }
        .terms h5 { margin: 5px 0; font-size: 11px; font-weight: bold; text-decoration: underline; }
        .terms p { margin: 2px 0; }
        
        .totals-table { width: 100%; border-collapse: collapse; }
        .totals-table td { padding: 6px; border: 1px solid #000; font-size: 11px; }
        .totals-table .label { font-weight: bold; text-align: right; width: 50%; }
        .totals-table .amount { text-align: right; width: 50%; }
        .totals-table .total-row { background-color: #FF5722; color: white; font-weight: bold; }
        
        .bank-details { background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd; margin-bottom: 15px; font-size: 10px; }
        .bank-details h5 { margin: 3px 0; font-size: 11px; font-weight: bold; }
        .bank-details p { margin: 2px 0; }
        
        .footer-section { border-top: 2px solid #000; padding-top: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px; }
        .declaration { font-size: 10px; line-height: 1.4; }
        .signature-section { text-align: right; }
        .signature-line { margin-top: 40px; border-top: 1px solid #000; padding-top: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header-section">
            @@if($logo)
                <div class="company-logo">
                    <img src="data:image/png;base64,@{{ base64_encode(file_get_contents($logoPath)) }}" alt="Company Logo">
                </div>
            @@endif
            <div class="header-title">TAX INVOICE</div>
            <div class="header-divider"></div>
        </div>

        <!-- Company and Invoice Details -->
        <div class="invoice-header">
            <div class="company-details">
                <p><strong>@{{ $companyName }}</strong></p>
                <p>@{{ $companyAddress }}</p>
                <p>Email: @{{ $companyEmail }}</p>
                <p><strong>GST: @{{ $gst }}</strong></p>
            </div>
            <div class="invoice-details">
                <p><strong>INV NO:</strong> @{{ $invoice->invoice_number }}</p>
                <p><strong>Invoice Date:</strong> @{{ $invoice->invoice_date }}</p>
                <p><strong>For the Period:</strong> @{{ $invoice->period }}</p>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="customer-section">
            <h4>BILL TO</h4>
            <div class="customer-info">
                <div>
                    <p><strong>@{{ $customer->name }}</strong></p>
                    <p>@{{ $customer->address }}</p>
                    <p>Email: @{{ $customer->email }}</p>
                </div>
                <div>
                    <p><strong>Your Order</strong></p>
                    <p>GST: @{{ $customer->gst }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Product Name</th>
                    <th style="width: 15%;" class="text-right">Quantity</th>
                    <th style="width: 25%;" class="text-right">Amount (in INR)</th>
                </tr>
            </thead>
            <tbody>
                @@foreach($items as \$item)
                    <tr>
                        <td>\$item->description</td>
                        <td class="text-right">\$item->quantity</td>
                        <td class="text-right">@{{ number_format(\$item->amount, 2) }}</td>
                    </tr>
                @@endforeach
            </tbody>
        </table>

        <!-- Totals and Terms -->
        <div class="totals-section">
            <div class="terms">
                <h5>Terms and Conditions</h5>
                <p>1. Payments to be made within two working days of sending this invoice.</p>
                <p>2. Payments accepted via net banking, cheques, credit/debit card</p>
                <p>3. Kindly connect with us on your preferred mode of payment</p>
                <p>4. Please deposit all cheques or process NEFT to the address mentioned here:</p>
            </div>

            <table class="totals-table">
                <tr>
                    <td class="label">Total</td>
                    <td class="amount">@{{ number_format(\$subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">IGST (\$taxRate%)</td>
                    <td class="amount">@{{ number_format(\$taxAmount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label">Grand Total</td>
                    <td class="amount">@{{ number_format(\$grandTotal, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Grand Total in Words -->
        <p style="text-align: center; font-weight: bold; margin: 10px 0;">
            Rupees @{{ \$grandTotalWords }}
        </p>

        <!-- Bank Details -->
        <div class="bank-details">
            <h5>PAYMENT DETAILS</h5>
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="border: none;"><strong>ACCOUNT NAME</strong><br>@{{ $accountName }}</td>
                    <td style="border: none;"><strong>BANK NAME</strong><br>@{{ $bankName }}</td>
                </tr>
                <tr>
                    <td style="border: none;"><strong>ACCOUNT NO</strong><br>@{{ $accountNumber }}</td>
                    <td style="border: none;"><strong>IFSC CODE</strong><br>@{{ $ifscCode }}</td>
                </tr>
            </table>
        </div>

        <!-- PAN/GSTIN -->
        <p style="font-size: 10px; margin: 10px 0;">
            <strong>PAN:</strong> [PAN Number] &nbsp;&nbsp;&nbsp;&nbsp; <strong>GSTIN:</strong> @{{ $gstin }}
        </p>

        <!-- Footer -->
        <div class="footer-section">
            <div class="declaration">
                <h5>Declaration</h5>
                <p>Customers are required to check and satisfy themselves as to the accuracy of the invoice. Customers are requested to bring to our notice any discrepancy in the invoice within 2 days from the date of invoice.</p>
                <p style="margin-top: 8px;">We declare that the invoice shows the actual Fees.</p>
            </div>
            <div class="signature-section">
                <p style="margin-bottom: 50px;">For @{{ $companyName }},</p>
                <div class="signature-line">Authorized Signatory</div>
            </div>
        </div>
    </div>
</body>
</html>`;

    document.getElementById('content').value = defaultTemplate;
    alert('Default invoice template loaded successfully!');
}
</script>
@endsection
