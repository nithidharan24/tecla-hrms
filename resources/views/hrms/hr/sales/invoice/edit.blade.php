@extends('layouts.index')
@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Edit Invoice</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('invoice.index')}}">Invoice</a></li>
                    <li class="breadcrumb-item active">Edit Invoice</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    <div class="row">
        <div class="col-sm-12">
            <form id="invoice" method="POST" action="{{route('invoice.update',$invoices->invoice_id)}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-sm-6 col-md-3">
                        <div class="input-block mb-3">
                            <label for="ex_client" class="col-form-label">Client <span class="text-danger">*</span></label>
                            <select name="ex_client" id="ex_client" class="select"  onchange="fetchProjects(this.value)">
                                <option value="">Please Select</option>
                                @foreach ($clients as $cs)
                                    <option value="{{$cs->client_id}}" {{ $invoices->client == $cs->client_id ? 'selected' : '' }}>{{ucFirst($cs->first_name).' '.ucFirst($cs->last_name)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="input-block mb-3">
                            <label for="ex_project" class="col-form-label">Project <span class="text-danger">*</span></label>
                            <select name="ex_project" id="ex_project" class="select">
                                <option value="">Select Project</option>
                                {{-- Dynamic Change Based On Client --}}
                                @foreach ($projects as $item)
                                <option value=" {{$item->projectid }} " {{$item->projectid==$invoices->project ? 'selected':''}}>{{ucFirst($item->projectname)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="input-block mb-3">
                            <label for="email" class="col-form-label">Email <span class="text-danger">*</span></label>
                            <input name="email" id="email" class="form-control" type="email" value="{{$invoices->email}}">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="input-block mb-3">
                            <label for="mobile" class="col-form-label">Mobile <span class="text-danger">*</span></label>
                            <input name="mobile" id="mobile" class="form-control" type="text" value="{{$invoices->phone}}">
                        </div>
                    </div>
                                                        
                    <div class="col-sm-6 col-md-3">
                        <div class="input-block mb-2">
                            <label for="cs_address" class="col-form-label">Client Address <span class="text-danger">*</span></label>
                            <textarea name="client_address" id="cs_address" class="form-control" style="resize: none;" rows="4"  oninput="updateBillingAddress()">{{$invoices->client_address}}</textarea>
                        </div>
                        <div class="input-block mb-1">
                            <input type="checkbox" id="sameAddress" onclick="toggleAddress()" 
                                {{ trim($invoices->client_address) === trim($invoices->billing_address) ? 'checked' : '' }} />
                            <label for="sameAddress" class="col-form-label">Same as Client Address</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="input-block mb-3">
                            <label for="bill_address" class="col-form-label">Billing Address <span class="text-danger">*</span></label>
                            <textarea name="billing_address" id="bill_address" class="form-control" style="resize: none;" rows="4">{{$invoices->billing_address}}</textarea>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="input-block mb-3">
                            <label for="indate" class="col-form-label">Invoice date <span class="text-danger">*</span></label>
                            <div class="cal-icon">
                                <input name="invdate" id="indate" class="form-control datetimepicker"
                                        value="{{ \Carbon\Carbon::parse($invoices->invoice_date)->format('d-m-Y') }}"
                                        type="text">
                            </div>
                        </div>
                        <div class="input-block mb-3">
                            <label for="dudate" class="col-form-label">Due Date </label>
                            <div class="cal-icon">
                                <input name="duedate" class="form-control datetimepicker"
                                        value="{{ \Carbon\Carbon::parse($invoices->due_date)->format('d-m-Y') }}"
                                        id="dudate" type="text">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="input-block mb-3">
                            <label for="tax-select" class="col-form-label">Tax</label>
                            <select name="taxes[]" id="tax-select" class="select" multiple="multiple">
                                <option value="">Select Tax</option>
                                @php
                                    $invoiceTaxIds = explode(',', $invoices->tax);
                                @endphp
                                @foreach ($tax as $tx)
                                    <option value="{{ $tx->id }}" data-percentage="{{ $tx->percentage }}"
                                         {{ in_array($tx->id, $invoiceTaxIds) ? 'selected' : '' }}>
                                        {{ ucfirst($tx->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="table-responsive">
                            @php
                                $itemIndex = count($invoice_items);
                            @endphp
                            <table class="table table-hover table-white" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th class="col-sm-2">Item</th>
                                        <th class="col-md-6">Description</th>
                                        <th>Unit Cost</th>
                                        <th>Qty</th>
                                        <th>Amount</th>
                                        <th> </th>
                                    </tr>
                                </thead>
                                <tbody class="tbodyone" id="item-rows">
                                    @foreach ($invoice_items as $index => $item)
                                        <tr class="item-row" id="{{$item->id}}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                <input type="text" class="form-control item-name" name="items[{{ $index }}][name]" value="{{ $item->item ?? '' }}"></td>
                                            <td><input type="text" class="form-control item-description" name="items[{{ $index }}][description]" value="{{ $item->description ?? '' }}"></td>
                                            <td><input type="number" min="1" class="form-control unit-cost" name="items[{{ $index }}][unitCost]" value="{{ $item->unit_cost ?? '' }}"></td>
                                            <td><input type="number" min="1" class="form-control quantity" name="items[{{ $index }}][quantity]" value="{{ $item->quantity ?? '' }}"></td>
                                            <td><input type="number" class="form-control amount" name="items[{{ $index }}][amount]" value="{{ $item->amount ?? '' }}" readonly></td>
                                            <td><a class="{{$index==0 ? 'text-success' : 'text-danger removeRow'}} font-18" title="{{$index==0 ? 'Add' : 'Remove'}}" id="{{$index==0 ? 'addRow' : ''}}"><i class="fa-solid {{$index==0 ? 'fa-plus' : 'fa-trash'}}"></i></a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Total Section -->
                        <div class="table-responsive">
                            <table class="table table-hover table-white">
                                <tbody id="table-body">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end">Total</td>
                                        <td class="text-end pe-4"><input class="form-control text-end" value="{{$invoices->total}}" id="total" name="total" readonly type="text"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">Tax</td>
                                        <td class="text-end pe-4">
                                            <input class="form-control text-end" value="{{ $invoices->tax_amt }}" id="tax-Amt" name="totaltaxAmt" type="hidden">
                                            <div id="taxBreakdownDisplay"></div>
                                            <div class="form-control text-end" readonly>
                                                <span id="total-tax" contenteditable="true">
                                                    {{ $invoices->tax_amt != 0 ? number_format($invoices->tax_amt, 0) : 0 }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">
                                            Discount %
                                        </td>
                                        <td class="text-end pe-4">
                                            <input class="form-control text-end" value="{{$invoices->discount !==0 ? $invoices->discount : '0'}}" min="0" max="100" id="discount" name="discount" type="number">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end pe-4">
                                            <b>Grand Total</b>
                                        </td>
                                        <td class="text-end tdata-width pe-4">
                                            <input class="form-control text-end" value="{{$invoices->grant_amt}}" name="grantAmt" id="grandTotal" type="text">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                         </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-block mb-3">
                                    <label for="others" class="col-form-label">Other Information</label>
                                    <textarea name="other" id="others" class="form-control" style="resize: none;" rows="4">{{$invoices->other_information!=null ? $invoices->other_information : ''}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="submit-section">
                    <button type="submit" class="btn btn-primary submit-btn m-r-10">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Page Content -->

<script>
    function fetchProjects(clientId) {
        if (clientId) {
            $.ajax({
                type: 'GET',
                url: "{{ route('invoice.getprojects') }}",
                data: { client_id: clientId },
                dataType: 'json',
                success: function(data) {
                    $('#ex_project').empty();  // Clear previous options
                    // console.log(data); // Log the received data for debugging
                    if (data.client) {
                                $('#email').val(data.client.email);
                                $("#cs_address").text(data.client.address);
                                $("#mobile").val(data.client.phone);
                    }
                    if (Array.isArray(data.projects) && data.projects.length > 0) {
                        $.each(data.projects, function(key, value) {
                                                    $('#ex_project').append('<option value="' + value.projectid + '">' + value.projectname + '</option>');
                        });
                    } else {
                        $('#ex_project').append('<option value="">No projects found</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    alert('Failed to fetch projects. Please try again.');
                }
            });
        } else {
            $('#ex_project').empty().append('<option value="">Select a projects</option>');
        }
    }

    $(document).ready(function() {
        var items = []; // Array to store items with amounts and quantities
        var selectedTaxes = {}; // Object to store currently selected tax types and their percentages
        const aggregatedTaxBreakdown = {};

        // Check if addresses are the same on page load and update checkbox state
        function initializeAddressCheckbox() {
            const clientAddress = $('#cs_address').val().trim();
            const billingAddress = $('#bill_address').val().trim();
            
            if (clientAddress === billingAddress && clientAddress !== '') {
                $('#sameAddress').prop('checked', true);
            } else {
                $('#sameAddress').prop('checked', false);
            }
        }

        // Initialize checkbox state on page load
        initializeAddressCheckbox();

        // Function to calculate tax based on amount, quantity, and selected tax types
        function calculateTax(amount, quantity, selectedTaxes) {
            var totalTaxAmount = 0;
            var taxBreakdown = {};
            var totalAmount = amount * quantity; // Calculate total amount based on quantity

            // Iterate through selected taxes to calculate individual tax amounts
            for (var taxType in selectedTaxes) {
                var percentage = selectedTaxes[taxType];
                var taxAmount = (totalAmount * (percentage||0)) / 100; // Calculate the tax for this type
                taxBreakdown[taxType] = taxAmount; // Store the individual tax amount
                totalTaxAmount += taxAmount; // Add this tax to the total
            }

            return {
                totalTaxAmount: totalTaxAmount,
                taxBreakdown: taxBreakdown
            };
        }

        // Function to calculate the total for an item including taxes
        function calculateItemTotal(amount, quantity, selectedTaxes) {
            var baseTotal = amount * quantity; // Calculate base total for item
            var taxData = calculateTax(amount, quantity, selectedTaxes); // Get tax data using the tax calculation function
            var totalWithTax = baseTotal + taxData.totalTaxAmount; // Calculate overall total including taxes

            return {
                baseTotal: baseTotal,
                totalTaxAmount: taxData.totalTaxAmount,
                totalWithTax: totalWithTax,
                taxBreakdown: taxData.taxBreakdown
            };
        }
            
        // Function to retrieve selected taxes from the dropdown
        function getSelectedTaxes() {
            var taxes = {};
            $('#tax-select option:selected').each(function() {
                var taxType = $(this).text().trim(); // Get the name of the tax (e.g., CGST, SGST)
                var percentage = parseFloat($(this).data('percentage')) || 0; // Get the percentage
                taxes[taxType] = percentage; // Store tax type and percentage         
            });
            return taxes;
        }

         // Function to update the tax calculations whenever the unit cost or quantity changes
        function updateTaxCalculations(row) {
            var unitCost = parseFloat(row.find('.unit-cost').val()) || 1; // Get the unit cost
            var quantity = parseFloat(row.find('.quantity').val()) || 1; // Get the quantity
            var amount = unitCost * quantity; // Calculate the amount

            // Get selected taxes and calculate the tax for this row
            selectedTaxes = getSelectedTaxes(); // Update selected taxes
            var taxResult = calculateItemTotal(unitCost, quantity, selectedTaxes); // Calculate total for the item
                    
            row.find('.amount').val(amount.toFixed(2)); // Display the calculated amount
            row.find('.total-with-tax').val(taxResult.totalWithTax.toFixed(2)); // Assuming you have a field for total amount including tax

            // Update the items array with the current item details
            var itemIndex = row.index(); // Assuming rows are indexed in the same order as items
            items[itemIndex] = {
                amount: amount,
                totalTaxAmount: taxResult.totalTaxAmount
            };

            updateOverallTotalTax(); // Call this to update the overall total tax amount
            calculateGrandTotal();
        }
              
        // Function to update the total amount calculation based on the amounts entered
        function updateTotalAmount() {
            let totalAmount = 0;
            $('#item-rows .amount').each(function() {
                totalAmount += parseFloat($(this).val()) || 0; // Sum up the total amount
            });
            $('#total').val(totalAmount.toFixed(2)); // Update the total input field
            calculateGrandTotal();
        }

        //Newly Added
        updateTotalAmount();
        getSelectedTaxes();

        // Function to calculate grand total (Total Amount - Discount)
        function calculateGrandTotal() {
            let totalAmount = parseFloat($('#total').val()) || 0;
            let discount = parseFloat($('#discount').val()) || 0;
            // Calculate grand total applying discount
            let totalTax = parseInt($('#total-tax').text());
            if(totalTax<0){
                totalTax = 0;
            }
            let grandTotal = totalAmount - (totalAmount * (discount / 100));         
            grandTotal+=totalTax;
                     
            $('#grandTotal').val(grandTotal.toFixed(2));
        }

        // Function to update the overall total tax
        function updateOverallTotalTax() {
            var overallTotalTax = items.reduce((sum, item) => sum + (item.totalTaxAmount || 0), 0);
            $('#total-tax').text(overallTotalTax.toFixed(2)); // Update the UI element showing total tax
            $('#tax-Amt').val(overallTotalTax.toFixed(2));
        }

        // Event listener for inputs related to unit cost and quantity
        $(document).on('input', '.unit-cost, .quantity', function() {
            let row = $(this).closest('tr');
            let unitCost = parseFloat(row.find('.unit-cost').val()) || 1;
            let quantity = parseFloat(row.find('.quantity').val()) || 1;

            // Ensure unit cost and quantity are positive
            if (unitCost <= 0) {
                unitCost = 1;
                row.find('.unit-cost').val(unitCost);
            }
            if (quantity <= 0) {
                quantity = 1;
                row.find('.quantity').val(quantity);
            }

            // Update amount and tax calculations
            updateTaxCalculations(row);
            updateTotalAmount(); // Call a function to update the overall total amount if necessary
        });

        //newly added
        updateTotalAmount();

        // Event listener for selecting or deselecting tax types
        $('#tax-select').on('change', function() {
            selectedTaxes = getSelectedTaxes(); // Update selected taxes
            $('.unit-cost, .quantity').each(function() {
                var row = $(this).closest('tr'); // Get the closest row for each item
                updateTaxCalculations(row); // Recalculate taxes for all rows
            });
            updateTotalAmount();

            // Display The Tax
            const taxBreakdownDisplay = document.getElementById('taxBreakdownDisplay');
            taxBreakdownDisplay.innerHTML = '';
            for (const [key, value] of Object.entries(selectedTaxes)) {
                const paragraph = document.createElement('p');
                if(parseInt(value)!==0){
                    paragraph.textContent = `${key} : ${value}%`;
                    taxBreakdownDisplay.appendChild(paragraph);
                }
            }
        });

        // Event listener for updating discount input
        $('#discount').on('input', function() {
            let discountValue = parseFloat($(this).val()) || 0;
            if (discountValue < 0) {
                discountValue = 0;
            } else if (discountValue > 100) {
                discountValue = 100;
            }
            $(this).val(discountValue);
            calculateGrandTotal();
        });

        // Initialize form validation
        $('#invoice').validate({
            rules: {
                ex_client: {
                    required: true,
                 },
                ex_project: {
                    required: true,
                },
                email: { required: true, email: true },
                mobile:{required:true, minlength: 10 },
                billing_address: {
                     required: true,
                    minlength: 1,
                    maxlength: 255,
                    normalizer: function(value) {
                        return $.trim(value);
                    }
                },
                client_address: {
                    required: true,
                    minlength: 1,
                    maxlength: 255,
                    normalizer: function(value) {
                        return $.trim(value);
                    }
                },
                invdate: { required: true }
            },
            messages: {
                client: { required: "Client is required." },
                project: { required: "Project is required." },
                email: {
                    required: "Please enter an email address.",
                    email: "Please enter a valid email address."
                },
                mobile: {
                    required: "Please enter a mobile number.",
                    minlength: "Mobile number must be exactly 10 digits long.",
                },
                client_address: {
                    required: "Client Address is required.",
                    minlength: "Client Address cannot be empty.",
                    maxlength: "Client Address cannot exceed 255 characters."
                },
                billing_address: {
                    required: "Billing Address is required.",
                    minlength: "Billing Address cannot be empty.",
                    maxlength: "Billing Address cannot exceed 255 characters."
                },
                invdate: { required: "Please select an invoice date." }
               
            },
            errorElement: 'div',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.input-block').append(error);
            },
            submitHandler: function (form) {
                let allItemsFilled = true;
                // Loop through all the item rows and check if each field is properly filled
                $('#itemsTable tbody tr.item-row').each(function (index, row) {
                    const itemName = $(row).find('.item-name').val().trim();
                    const itemDescription = $(row).find('.item-description').val().trim();
                    const unitCost = $(row).find('.unit-cost').val().trim();
                    const quantity = $(row).find('.quantity').val().trim();

                    if (!itemName || !itemDescription || !unitCost || !quantity || unitCost <= 0 || quantity <= 0) {
                        allItemsFilled = false;
                        return false; // Break out of the loop if any field is not filled
                    }
                });

                if (allItemsFilled) {
                    form.submit();
                } else {
                    Swal.fire({
                    title: "",
                    text: "Please fill out all item fields correctly.",
                    icon: "error",
                    });
                }
            }
        });

        let itemIndex = {{ $itemIndex }}; // Start indexing from 1
        $('#addRow').on('click', function (e) {
            e.preventDefault(); // Prevent the default action

            // Validate previous item row before adding a new one
            const lastRow = $('#itemsTable tbody tr.item-row:last');
            if (lastRow.length) {
                const lastItemName = lastRow.find('.item-name').val().trim();
                const lastItemDescription = lastRow.find('.item-description').val().trim();
                const lastUnitCost = lastRow.find('.unit-cost').val().trim();
                const lastQuantity = lastRow.find('.quantity').val().trim();

                if (!lastItemName || !lastItemDescription || !lastUnitCost || !lastQuantity || lastUnitCost <= 0 || lastQuantity <= 0) {
                    Swal.fire({
                    title: "",
                    text: "Please fill out all fields of the previous item before adding a new row.",
                    icon: "error",
                    });
                    return; // Exit the function if the last row is not filled
                }
            }

            // Append a new row
            const newRow = `
                <tr class="item-row">
                    <td>${itemIndex + 1}</td>
                    <td><input type="text" class="form-control item-name" name="items[${itemIndex}][name]" required></td>
                    <td><input type="text" class="form-control item-description" name="items[${itemIndex}][description]" required></td>
                    <td><input type="number" min="1" class="form-control unit-cost" name="items[${itemIndex}][unitCost]" required></td>
                    <td><input type="number" min="1" class="form-control quantity" name="items[${itemIndex}][quantity]" required></td>
                    <td><input type="number" class="form-control amount" name="items[${itemIndex}][amount]" readonly></td>
                    <td><a class="text-danger font-18 removeRow" title="Remove"><i class="fa-solid fa-trash"></i></a></td>
                </tr>
            `;
            $('#item-rows').append(newRow);
            itemIndex++; // Increment the index for the next row
        });

        // Function to remove a row from the items table
        $('#itemsTable').on('click', '.removeRow', function () {
            $(this).closest('tr').remove(); // Remove the row

            // Re-index the rows after removal if necessary
            $('#itemsTable tbody tr.item-row').each(function (index) {
                $(this).find('td:first').text(index + 1); // Update the row number
            });

            items.splice($(this).closest('tr').index(), 1);

            // Recalculate total amount and total tax after row deletion
            $('.unit-cost, .quantity').each(function() {
                var row = $(this).closest('tr'); // Get the closest row for each item
                updateTaxCalculations(row); // Recalculate taxes for all rows
            });
            updateOverallTotalTax();
            updateTotalAmount();
              
            itemIndex--;
        });
    });

    function updateBillingAddress() {
        const clientAddress = document.getElementById('cs_address').value;
        const checkbox = document.getElementById('sameAddress');
                
        if (checkbox.checked) {
            document.getElementById('bill_address').value = clientAddress;
        }
    }

    function toggleAddress() {
        const checkbox = document.getElementById('sameAddress');
        const billingAddress = document.getElementById('bill_address');
                
        if (checkbox.checked) {
            updateBillingAddress();
        } else {
            billingAddress.value = "";
        }
    }

    // Show validation errors using SweetAlert
    @if ($errors->any())
    var errorMessage = '';
    @foreach ($errors->all() as $error)
        errorMessage += "{{ $error }}\n";
    @endforeach
    Swal.fire({
        icon: 'error',
        title: 'Validation Errors',
        text: errorMessage,
    });
    @endif
</script>
<style>
    @media (max-width: 767px) {
    #itemsTable th, 
    #itemsTable td {
        white-space: nowrap; /* Prevents text wrapping */
        min-width: 120px;    /* Adjust width as needed */
    }

    #itemsTable th:nth-child(2), 
    #itemsTable td:nth-child(2) {
        min-width: 150px; /* Item column */
    }

    #itemsTable th:nth-child(3), 
    #itemsTable td:nth-child(3) {
        min-width: 200px; /* Description column */
    }
}

</style>
@endsection