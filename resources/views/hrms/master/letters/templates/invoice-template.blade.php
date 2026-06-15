<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TAX INVOICE</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f0f0;
            padding: 30px;
            color: #111;
            font-size: 12px;
        }

        .page {
            max-width: 860px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }

        /* LOGO / TITLE CENTER */
        .logo-area {
            text-align: center;
            margin-bottom: 14px;
        }
        .logo-area img {
            max-height: 58px;
            max-width: 180px;
            object-fit: contain;
            display: inline-block;
        }
        .logo-area .logo-name {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #111;
        }
        .logo-area .logo-sub {
            font-size: 10px;
            letter-spacing: 4px;
            color: #888;
        }
        .invoice-title {
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            color: #E8490F;
            text-decoration: underline;
            text-underline-offset: 3px;
            margin-bottom: 10px;
        }

        /* TOP BLUE BAR — invoice meta in a single row */
        .invoice-meta-bar {
            
            color: #141414;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 10px 20px;
            border-radius: 4px;
            margin-bottom: 16px;
            gap: 10px;
        }
        .invoice-meta-bar .meta-item {
            text-align: center;
            flex: 1;
        }
        .invoice-meta-bar .meta-item:not(:last-child) {
            border-right: 1px solid rgba(255,255,255,0.3);
        }
        .invoice-meta-bar .meta-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
            margin-bottom: 2px;
        }
        .invoice-meta-bar .meta-value {
            font-size: 13px;
            font-weight: 700;
        }

        /* TWO-COLUMN: Company + Bill To */
        .parties-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            margin-bottom: 18px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        .party-box {
            padding: 14px 18px;
        }
        .party-box:first-child {
            border-right: 1px solid #ddd;
        }
        .party-box h4 {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #E8490F;
            font-weight: 700;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #eee;
        }
        .party-box p {
            margin: 3px 0;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }
        .party-box p strong {
            color: #111;
            font-weight: 600;
        }

        /* ITEMS TABLE */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .items-table th {
            background: #E8490F;
            color: #fff;
            padding: 8px 10px;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid #c73d0d;
            text-align: left;
        }
        .items-table th.right { text-align: right; }
        .items-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-size: 11px;
            color: #222;
        }
        .items-table td.right { text-align: right; }
        .items-table tr:nth-child(even) td { background: #fafafa; }

        /* TOTALS + TERMS */
        .bottom-section {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 24px;
            margin-bottom: 14px;
            align-items: start;
        }
        .terms h5 {
            font-size: 11px;
            font-weight: 700;
            text-decoration: underline;
            margin-bottom: 6px;
        }
        .terms p {
            font-size: 10px;
            color: #444;
            margin: 2px 0;
            line-height: 1.5;
        }
        .totals-table { width: 100%; border-collapse: collapse; }
        .totals-table td {
            padding: 7px 10px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        .totals-table .lbl { font-weight: 600; text-align: right; background: #f5f5f5; }
        .totals-table .amt { text-align: right; }
        .totals-table .grand td {
            background: #E8490F;
            color: #fff;
            font-weight: 700;
        }

        /* AMOUNT IN WORDS */
        .amount-words {
            text-align: center;
            font-size: 11px;
            font-weight: 600;
            margin: 8px 0 14px;
            color: #222;
        }

        /* BANK DETAILS */
        .bank-box {
            background: #f7f7f7;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 14px;
            font-size: 10px;
        }
        .bank-box h5 {
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #E8490F;
        }
        .bank-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 8px;
        }
        .bank-grid div { line-height: 1.6; }
        .bank-grid strong { display: block; font-size: 9px; text-transform: uppercase; color: #555; }

        /* FOOTER */
        .footer-row {
            border-top: 2px solid #111;
            padding-top: 14px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 10px;
        }
        .declaration { font-size: 10px; color: #444; line-height: 1.5; }
        .declaration h5 { font-size: 11px; font-weight: 700; margin-bottom: 5px; text-decoration: underline; }
        .signature { text-align: right; font-size: 11px; }
        .authorized-signature {
            height: 46px;
            margin: 8px 0 4px;
            display: flex;
            justify-content: flex-end;
            align-items: flex-end;
        }
        .authorized-signature img {
            max-height: 44px;
            max-width: 170px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
        }
        .signature .sig-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 5px;
            font-weight: 600;
        }

        .pan-line { font-size: 10px; margin-bottom: 10px; color: #333; }
    </style>
</head>
<body>
<div class="page">

    <!-- Logo -->
    <div class="logo-area">
        @if(!empty($logoDataUri))
            <img src="{{ $logoDataUri }}" alt="{{ $companyName ?? 'Company' }} Logo">
        @elseif(!empty($logoPath) && file_exists($logoPath))
            <img src="data:image/{{ pathinfo($logoPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="{{ $companyName ?? 'Company' }} Logo">
        @else
            <div class="logo-name">{{ $companyName ?? 'tecla' }}</div>
            <div class="logo-sub">HRMS</div>
        @endif
    </div>

    <div class="invoice-title">TAX INVOICE</div>

    <!-- BLUE TOP BAR: Invoice Details as single row -->
    <div class="invoice-meta-bar">
       
        <div class="meta-item">
            <div class="meta-label">Invoice Date</div>
            <div class="meta-value">{{ $invoice->invoice_date }}</div>
        </div>
         <div class="meta-item">
            <div class="meta-label">INV NO</div>
            <div class="meta-value">{{ $invoice->invoice_number }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">For the Period</div>
            <div class="meta-value">{{ $invoice->period }}</div>
        </div>
    </div>

    <!-- TWO COLUMNS: Company Info | Bill To -->
    <div class="parties-row">
        <div class="party-box">
            <h4>From</h4>
            <p><strong>{{ $companyName }}</strong></p>
            <p>{{ $companyAddress }}</p>
            <p>Email: {{ $companyEmail }}</p>
            <p><strong>GST: {{ $gst }}</strong></p>
        </div>
        <div class="party-box">
            <h4>Bill To</h4>
            <p><strong>{{ $customer->name }}</strong></p>
            <p>{{ $customer->address }}</p>
            <p>Email: {{ $customer->email }}</p>
            <p>GST: {{ $customer->gst }}</p>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:60%">Product Name</th>
                <th class="right" style="width:15%">Quantity</th>
                <th class="right" style="width:25%">Amount (in INR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>$item->description</td>
                <td class="right">$item->quantity</td>
                <td class="right">8,400.00</td>
            </tr>
            <tr>
                <td>$item->description</td>
                <td class="right">$item->quantity</td>
                <td class="right">750.00</td>
            </tr>
        </tbody>
    </table>

    <!-- Totals + Terms -->
    <div class="bottom-section">
        <div class="terms">
            <h5>Terms and Conditions</h5>
            <p>1. Payments to be made within two working days of sending this invoice.</p>
            <p>2. Payments accepted via net banking, cheques, credit/debit card</p>
            <p>3. Kindly connect with us on your preferred mode of payment</p>
            <p>4. Please deposit all cheques or process NEFT to the address mentioned here:</p>
        </div>
        <table class="totals-table">
            <tr>
                <td class="lbl">Total</td>
                <td class="amt">{{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="lbl">IGST ($taxRate%)</td>
                <td class="amt">{{ number_format($taxAmount, 2) }}</td>
            </tr>
            <tr class="grand">
                <td>Grand Total</td>
                <td>{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Amount in Words -->
    <div class="amount-words">Rupees {{ $grandTotalWords }}</div>

    <!-- Bank Details -->
    <div class="bank-box">
        <h5>PAYMENT DETAILS</h5>
        <div class="bank-grid">
            <div><strong>Account Name</strong>{{ $accountName }}</div>
            <div><strong>Bank Name</strong>{{ $bankName }}</div>
            <div><strong>Account No</strong>{{ $accountNumber }}</div>
            <div><strong>IFSC Code</strong>{{ $ifscCode }}</div>
        </div>
    </div>

    <!-- PAN / GSTIN -->
    <div class="pan-line">
        <strong>PAN:</strong> [PAN Number] &nbsp;&nbsp;&nbsp; <strong>GSTIN:</strong> {{ $gstin }}
    </div>

    <!-- Footer -->
    <div class="footer-row">
        <div class="declaration">
            <h5>Declaration</h5>
            <p>Customers are required to check and satisfy themselves as to the accuracy of the invoice. Customers are requested to bring to our notice any discrepancy within 2 days from the date of invoice.</p>
            <p style="margin-top:6px">We declare that the invoice shows the actual Fees.</p>
        </div>
        <div class="signature">
            <p>For {{ $companyName }},</p>
            @if(!empty($offerSignatureDataUri))
                <div class="authorized-signature">
                    <img class="invoice-signature-img" src="{{ $offerSignatureDataUri }}" alt="Authorized Signature">
                </div>
            @endif
            <div class="sig-line">Authorized Signatory</div>
        </div>
    </div>

</div>
</body>
</html>
