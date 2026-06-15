@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    @if(isset($isPreview) 
                        INVOICE PREVIEW - THIS IS NOT A REAL INVOICE
                    @else
                        Invoice #{{ $invoices->invoice_id }}
                    @endif
                </div>

                <div class="card-body">
                    <!-- Your invoice HTML here -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>From:</h4>
                            <p>Your Company Name</p>
                            <p>Your Company Address</p>
                            <p>Phone: Your Phone</p>
                            <p>Email: Your Email</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h4>To:</h4>
                            <p>{{ $invoices->client_name }}</p>
                            <p>{{ $invoices->billing_address }}</p>
                            <p>Phone: {{ $invoices->phone }}</p>
                            <p>Email: {{ $invoices->email }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Invoice Date:</strong> {{ $invoices->invoice_date }}</p>
                            <p><strong>Due Date:</strong> {{ $invoices->due_date }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            @if(!isset($isPreview))
                            <p><strong>Invoice #:</strong> {{ $invoices->invoice_id }}</p>
                            @endif
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice_items as $item)
                            <tr>
                                <td>{{ $item->item }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_cost, 2) }}</td>
                                <td>{{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-md-6">
                            @if($invoices->other_information)
                            <div class="alert alert-info">
                                <p><strong>Notes:</strong></p>
                                <p>{{ $invoices->other_information }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Subtotal:</strong></td>
                                    <td class="text-end">{{ number_format($invoices->total, 2) }}</td>
                                </tr>
                                @if($invoices->tax_amt > 0)
                                <tr>
                                    <td><strong>Tax:</strong></td>
                                    <td class="text-end">{{ number_format($invoices->tax_amt, 2) }}</td>
                                </tr>
                                @endif
                                @if($invoices->discount > 0)
                                <tr>
                                    <td><strong>Discount ({{ $invoices->discount }}%):</strong></td>
                                    <td class="text-end">-{{ number_format(($invoices->total * $invoices->discount / 100), 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Total:</strong></td>
                                    <td class="text-end">{{ number_format($invoices->grant_amt, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection