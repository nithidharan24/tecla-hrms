@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Invoice</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('invoice.index')}}">Invoice</a></li>
                    <li class="breadcrumb-item active">Invoice</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <div class="btn-group btn-group-sm">
                    <a href="{{route('invoices.csv',$invoices->invoice_id)}}" class="btn btn-white">CSV</a>
                    <a href="{{route('invoice.pdf',$invoices->invoice_id)}}" class="btn btn-white">PDF</a>
                    <a href="{{ route('invoice.print.pdf', ['id' =>$invoices->invoice_id]) }}" target="_blank" id="print-button" class="btn btn-white" onclick="openAndPrint(event)">
                        <i class="fa-solid fa-print fa-lg"></i> Print
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 m-b-20">
                            <img src="{{asset('admin/assets/img/company.png')}}" class="inv-logo" alt="Logo">
                             <ul class="list-unstyled">
                                <li>Tecla</li>
                                <li>GREETA TOWERS, Greeta Techpark, 99</li>
                                <li>Perungudi, Chennai-96</li>
                                <li>GST No:</li>
                            </ul>
                        </div>
                        <div class="col-sm-6 m-b-20">
                            <div class="invoice-details">
                                <h3 class="text-uppercase">Invoice{{ substr($invoices->invoice_id, strlen('EST-')) }}</h3>
                                <ul class="list-unstyled">
                                    <li>Create Date: <span>{{\Carbon\Carbon::parse($invoices->invoice_date)->format('d M Y')}}</span></li>
                                    <li>Due date: <span>{{\Carbon\Carbon::parse($invoices->due_date)->format('d M Y')}}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-lg-12 m-b-20">
                            <h5>Estimate to:</h5>
                             <ul class="list-unstyled">
                                <li><h5><strong>{{$invoices->client_name}}</strong></h5></li>
                                <li>
                                    @php
                                        $billingChunks = str_split($invoices->billing_address, 30);
                                    @endphp
                                </li>
                                @foreach ($billingChunks as $chunk)
                                    <li>{{ $chunk }}</li>
                                @endforeach
                                <li><a href="tel:{{$invoices->phone}}"><strong class="text-dark">{{$invoices->phone}}</strong></a></li>
                                <li><a href="mailto:{{$invoices->email}}"><span class="__cf_email__" data-cfemail="ea888b989893899f8e8baa8f928b879a868fc4898587">[email&#160;protected]</span></a></li>
                            </ul>
                        </div>
                    </div>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>ITEM</th>
                            <th class="d-none d-sm-table-cell">DESCRIPTION</th>
                                <th>UNIT COST</th>
                                <th>QUANTITY</th>
                                <th class="text-end">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($invoice_items) > 0)
                                @foreach ($invoice_items as $item)
                                    <tr>
                                        <td>1</td>
                                        <td>{{ UcFirst($item->item) }}</td>
                                        <td class="d-none d-sm-table-cell">{{ $item->description }}</td>
                                        <td>${{number_format($item->unit_cost, 0)}}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td class="text-end">${{number_format($item->amount, 0)}}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6">No Items</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div>
                        <div class="row invoice-payment">
                            <div class="col-sm-7">
                            </div>
                            <div class="col-sm-5">
                                <div class="m-b-20">
                                    <div class="table-responsive no-border">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th>Subtotal:</th>
                                                    <td class="text-end">${{number_format($invoices->total, 0)}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tax: 
                                                        @if (count($taxDetails) > 0)
                                                            @foreach ($taxDetails as $index => $tx)
                                                                <span class="text-regular">{{ $tx->name }} ({{ $tx->percentage }}%)</span>
                                                            @endforeach
                                                    
                                                        @else
                                                            <span class="text-regular">(0%)</span>
                                                        @endif
                                                    </th>
                                                    <td class="text-end">${{number_format($invoices->tax_amt, 0)}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Discount:</th>
                                                    <td class="text-end text-primary"><h5>- $ {{ number_format(($invoices->total * $invoices->discount) / 100, 2) }}</h5></td>
                                                </tr>
                                                <tr>
                                                    <th>Total:</th>
                                                    <td class="text-end text-primary"><h5>${{number_format($invoices->grant_amt, 0)}}</h5></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-info">
                            @if (!is_null($invoices->other_information))
                                <h5>Other Information</h5>
                                <p class="text-muted">{{ $invoices->other_information }}</p>
                            @else
                                <p class="text-muted text-center">...This is a Computer Generated Statement...</p>
                            @endif                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 

</div>
<!-- /Page Content -->


<script>
    function openAndPrint(event) {
        event.preventDefault();
        const url = event.currentTarget.href;
        const newWindow = window.open(url);
        newWindow.onload = function() {
            newWindow.print();
        };
    }
</script>



@endsection
