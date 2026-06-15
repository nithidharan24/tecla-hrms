@extends('layouts.index')

@section('content')
<!-- Page Content -->

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Create Estimate</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('estimate.index')}}">Estimate</a></li>
                    <li class="breadcrumb-item active">Create Estimate</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-sm-12">
            <form id="estimate" method="POST" action="{{route('estimate.store')}}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label class="col-form-label">Client Type</label>
                        <div>
                            <input type="radio" name="client_type" id="existingClient" value="existing" checked onclick="toggleClientFields()">
                            <label for="existingClient" class="col-form-label">Existing Client</label>
                            <input type="radio" name="client_type" id="newClient" value="new" onclick="toggleClientFields()">
                            <label for="newClient" class="col-form-label">New Client</label>
                        </div>
                    </div>

                    <div class="col-md-6" id="ProjectTypeRow">
                        <label class="col-form-label">Project Type</label>
                        <div class="input-block">
                            <input type="radio" name="project_type" id="existingProject" value="existing" checked onclick="toggleProjectFields()">
                            <label for="existingProject" class="col-form-label">Existing Project</label>
                            <input type="radio" name="project_type" id="newProjectType" value="new" onclick="toggleProjectFields()">
                            <label for="newProjectType" class="col-form-label">New Project</label>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-sm-6 col-md-3" id="NewClient">
                        <div class="input-block mb-3">
                            <label for="nw_client" class="col-form-label">Client <span class="text-danger">*</span></label>
                            <input name="nw_client" id="nw_client" class="form-control" type="text">
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3" id="NewProject">
                        <div class="input-block mb-3">
                            <label for="nw_project" class="col-form-label">Project <span class="text-danger">*</span></label>
                            <input name="nw_project" id="nw_project" class="form-control" type="text">
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3" id="ExClient">
                        <div class="input-block mb-3">
                            <label for="ex_client" class="col-form-label">Client <span class="text-danger">*</span></label>
                            <select name="ex_client" id="ex_client" class="select"  onchange="fetchProjects(this.value)">
                                <option value="">Please Select</option>
                                @foreach ($clients as $cs)
                                    <option value="{{$cs->client_id}}">{{ucFirst($cs->first_name).' '.ucFirst($cs->last_name)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3" id="ExProject">
                        <div class="input-block mb-3">
                            <label for="ex_project" class="col-form-label">Project <span class="text-danger">*</span></label>
                            <select name="ex_project" id="ex_project" class="select">
                                <option value="">Select Project</option>
                                {{-- Dynamic Change Based On Client --}}
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3" id="ExNewProject" style="display: none;">
                        <div class="input-block mb-3">
                            <label for="ex_nw_project" class="col-form-label">Project <span class="text-danger">*</span></label>
                            <input name="ex_nw_project" id="ex_nw_project" class="form-control" type="text">
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-md-3">
                        <div class="input-block mb-3">
                            <label for="email" class="col-form-label">Email <span class="text-danger">*</span></label>
                            <input name="email" id="email" class="form-control" type="email">
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="input-block mb-3">
                            <label for="mobile" class="col-form-label">Mobile <span class="text-danger">*</span></label>
                            <input name="mobile" id="mobile" class="form-control" type="text">
                        </div>
                    </div>
                                        
                    <div class="col-sm-6 col-md-3">
                        <div class="input-block mb-2">
                            <label for="cs_address" class="col-form-label">Client Address <span class="text-danger">*</span></label>
                            <textarea name="client_address" id="cs_address" class="form-control" style="resize: none;" rows="4"  oninput="updateBillingAddress()"></textarea>
                        </div>
                        <div class="input-block mb-1">
                            <input type="checkbox" id="sameAddress" onclick="toggleAddress()" />
                            <label for="sameAddress" class="col-form-label">Same as Client Address</label>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="input-block mb-3">
                            <label for="bill_address" class="col-form-label">Billing Address <span class="text-danger">*</span></label>
                            <textarea name="billing_address" id="bill_address" class="form-control" style="resize: none;" rows="4"></textarea>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="input-block mb-3">
                            <label for="esdate" class="col-form-label">Estimate Date <span class="text-danger">*</span></label>
                            <div class="cal-icon">
                                <input name="estdate" id="esdate" class="form-control datetimepicker" type="text">
                            </div>
                        </div>
                   
                        <div class="input-block mb-3">
                            <label for="exdate" class="col-form-label">Expiry Date <span class="text-danger">*</span></label>
                            <div class="cal-icon">
                                <input name="expdate" class="form-control datetimepicker" id="exdate" type="text">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="input-block mb-3">
                            <label for="tax-select" class="col-form-label">Tax</label>
                            <select name="taxes[]" id="tax-select" class="select" multiple="multiple">
                                <option value="">Select Tax</option>
                                @foreach ($tax as $tx)
                                    <option value="{{$tx->id}}" data-percentage="{{$tx->percentage}}">{{ucFirst($tx->name)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-white item-table" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th class="col-sm-2">Item</th>
                                        <th class="col-md-4">Description</th>
                                        <th>Unit Cost</th>
                                        <th >Qty</th>
                                        <th>Amount</th>
                                        <th> </th>
                                    </tr>
                                </thead>
                                <tbody class="tbodyone" id="item-rows">
                                    <tr class="item-row">
                                        <td>1</td>
                                        <td><input type="text" class="form-control item-name" name="items[0][name]"></td>
                                        <td><input type="text" class="form-control item-description" name="items[0][description]"></td>
                                        <td><input type="number" min="1" class="form-control unit-cost" name="items[0][unitCost]"></td>
                                        <td><input type="number" min="1" class="form-control quantity" name="items[0][quantity]"></td>
                                        <td><input type="number" class="form-control amount" name="items[0][amount]" readonly></td>
                                        <td><a class="text-success font-18" title="Add" id="addRow">Add</a></td>
                                    </tr>
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
                                        <td class="text-end pe-4"><input class="form-control text-end" id="total" name="total" readonly type="text"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">Tax</td>
                                        {{-- <div id="tax-details"></div> --}}
                                        <td class="text-end pe-4">
                                            <input class="form-control text-end" value="0" id="tax-Amt" name="totaltaxAmt" type="hidden">
                                            <div id="taxBreakdownDisplay"></div>
                                            <div class="form-control text-end" readonly><span id="total-tax" contenteditable="false">0.00</span></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">
                                            Discount %
                                        </td>
                                        <td class="text-end pe-4">
                                            <input class="form-control text-end" value="0" min="0" max="100" id="discount" name="discount" type="number">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end pe-4">
                                            <b>Grand Total</b>
                                        </td>
                                        <td class="text-end tdata-width pe-4">
                                            <input class="form-control text-end" name="grantAmt" id="grandTotal" type="text" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> 
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-block mb-3">
                                    <label for="others" class="col-form-label">Other Information</label>
                                    <textarea name="other" id="others" class="form-control" style="resize: none;" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
 <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Estimate Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="confirmSubmit">Confirm & Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ... existing form ... -->

  <div class="submit-section">
    <button type="button" class="btn btn-secondary submit-btn m-r-10" id="previewBtn">Preview</button>
    <button type="submit" class="btn btn-primary submit-btn m-r-10" id="formSubmitBtn">
        <span id="submitBtnText">Submit</span>
        <span id="submitBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>
    <button type="button" class="btn btn-danger submit-btn" id="cancelBtn">Cancel</button>
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
            url: "{{ route('estimate.getprojects') }}",
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

        // console.log('Base Total:', baseTotal);
        // console.log('Tax Breakdown:', taxData.taxBreakdown);      
        // console.log('Total Tax Amount:', taxData.totalTaxAmount);
        // console.log('Total Amount with Tax:', totalWithTax);

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
        // console.log('This is Total Amount',totalAmount);
        
    }

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
         

        $('#grandTotal').val(Math.round(grandTotal)+ '.00');
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

    // });

    // $(document).ready(function () {
    // Initialize form validation
    $('#estimate').validate({
        rules: {
            ex_client: {
                required: function () {
                    return $('#existingClient').is(':checked') && $("#ExClient").is(':visible');
                }
            },
            ex_project: {
                required: function () {
                    return $('#existingClient').is(':checked') && $('#existingProject').is(':checked') && $("#ExProject").is(':visible');
                }
            },
            nw_client: {
                required: function () {
                    return $('#newClient').is(':checked') && $("#NewClient").is(':visible');
                }
            },
            nw_project: {
                required: function () {
                    return $('#newClient').is(':checked') && $("#NewProject").is(':visible');
                }
            },
            ex_nw_project: {
                required: function () {
                    return $('#existingClient').is(':checked') && $('#newProjectType').is(':checked') && $("#ExNewProject").is(':visible');
                }
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
            estdate: { required: true },
            expdate: { required: true }
        },
        messages: {
            client: { required: "Client is required." },
            project: { required: "Project is required." },
            ex_nw_project: {
                required: "Project is required."
            },
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
            estdate: { required: "Please select an estimate date." },
            expdate: { required: "Please select an expiry date." }
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

    // Function to add a new row to the items table
    let itemIndex = 1; // Start indexing from 1
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

    function toggleClientFields() {
        const existingClientRadio = document.getElementById('existingClient');
        const existingProjectRadio = document.getElementById('ProjectTypeRow');
        const newClientRadio = document.getElementById('newClient');
        const exClientDiv = document.getElementById('ExClient');
        const exProjectDiv = document.getElementById('ExProject');
        const newClientDiv = document.getElementById('NewClient');
        const newProjectDiv = document.getElementById('NewProject');
        const exNewProjectDiv = document.getElementById('ExNewProject');

        if (existingClientRadio.checked) {
            exClientDiv.style.display = 'block'; // Show existing client fields
            // exProjectDiv.style.display = 'block'; // Show existing project fields
            existingProjectRadio.style.display = 'block';
            newClientDiv.style.display = 'none'; // Hide new client fields
            newProjectDiv.style.display = 'none'; // Hide new project fields
            toggleProjectFields();
            $("#nw_client, #nw_project").val('');
        } else {
            exClientDiv.style.display = 'none'; // Hide existing client fields
            exProjectDiv.style.display = 'none'; // Hide existing project fields
            existingProjectRadio.style.display = 'none';
            newClientDiv.style.display = 'block'; // Show new client fields
            newProjectDiv.style.display = 'block'; // Show new project fields
            exNewProjectDiv.style.display = 'none';
            $("#ex_client, #ex_project").val('');
            $("#ex_nw_project").val('');
            $("#email").val('');
            $("#cs_address").text('');
            $("#bill_address").text('');
            $("#mobile").val('');
        }
    }

    function toggleProjectFields() {
        const existingProjectRadio = document.getElementById('existingProject');
        const exProjectDiv = document.getElementById('ExProject');
        const exNewProjectDiv = document.getElementById('ExNewProject');

        if (existingProjectRadio.checked) {
            exProjectDiv.style.display = 'block';
            exNewProjectDiv.style.display = 'none';
            $("#ex_nw_project").val('');
        } else {
            exNewProjectDiv.style.display = 'block';
            exProjectDiv.style.display = 'none';
            $("#ex_project").val('');
        }

    }toggleClientFields();
    


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
$(document).on('submit', '#estimate', function(e) {
    e.preventDefault();
    const form = $(this);
    const submitBtn = form.find('.submit-btn');
    
    // Disable the button and show loading state
    submitBtn.prop('disabled', true);
    submitBtn.html(`
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Submitting...
    `);
    
    // Submit via AJAX
    $.ajax({
        url: form.attr('action'),
        method: form.attr('method'),
        data: form.serialize(),
        success: function(response) {
            // Handle success (e.g., redirect or show success message)
            window.location.href = "{{ route('estimate.index') }}";
        },
        error: function(xhr) {
            // Handle error (e.g., show validation errors)
            submitBtn.prop('disabled', false);
            submitBtn.text('Submit');
            
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                let errorMessages = '';
                for (const key in errors) {
                    errorMessages += errors[key][0] + '\n';
                }
                Swal.fire('Error', errorMessages, 'error');
            }
        }
    });
});

document.getElementById('your-form-id').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('formSubmitBtn');

    if (submitBtn.getAttribute('data-submitting') === 'true') {
        e.preventDefault();
        return false;
    }

    // Set submitting state
    submitBtn.setAttribute('data-submitting', 'true');
    submitBtn.disabled = true;
    document.getElementById('submitBtnText').textContent = 'Submitting...';
    document.getElementById('submitBtnSpinner').classList.remove('d-none');

    // Let the form continue submitting
});

// Reset button if invalid (modern browsers)
document.getElementById('your-form-id').addEventListener('invalid', function () {
    const submitBtn = document.getElementById('formSubmitBtn');
    submitBtn.removeAttribute('data-submitting');
    submitBtn.disabled = false;
    document.getElementById('submitBtnText').textContent = 'Submit';
    document.getElementById('submitBtnSpinner').classList.add('d-none');
}, true);
</script>

<script>
    // Preview button click handler
    $('#previewBtn').click(function() {
        // Validate form before showing preview
        if ($('#estimate').valid()) {
            generatePreview();
            $('#previewModal').modal('show');
        }
    });

    // Confirm submit button handler
    $('#confirmSubmit').click(function() {
        $('#previewModal').modal('hide');
        $('#estimate').submit();
    });

    // Function to generate preview content
    function generatePreview() {
        // Get all form data
        const formData = new FormData(document.getElementById('estimate'));
        const data = {};
        
        // Convert FormData to object
        for (let [key, value] of formData.entries()) {
            // Handle array fields (like items and taxes)
            if (key.includes('[') && key.includes(']')) {
                const matches = key.match(/(\w+)\[(\w+)\]\[(\w+)\]/);
                if (matches) {
                    const [_, arrayName, index, field] = matches;
                    if (!data[arrayName]) data[arrayName] = [];
                    if (!data[arrayName][index]) data[arrayName][index] = {};
                    data[arrayName][index][field] = value;
                }
            } else {
                data[key] = value;
            }
        }
        
        // Get selected client and project text
        const clientType = $('input[name="client_type"]:checked').val();
        let clientText = '';
        if (clientType === 'existing') {
            clientText = $('#ex_client option:selected').text();
        } else {
            clientText = data.nw_client || 'New Client';
        }
        
        const projectType = $('input[name="project_type"]:checked').val();
        let projectText = '';
        if (projectType === 'existing' && clientType === 'existing') {
            projectText = $('#ex_project option:selected').text();
        } else if (projectType === 'new' && clientType === 'existing') {
            projectText = data.ex_nw_project || 'New Project';
        } else if (clientType === 'new') {
            projectText = data.nw_project || 'New Project';
        }
        
        // Get selected taxes
        const selectedTaxes = [];
        $('#tax-select option:selected').each(function() {
            selectedTaxes.push({
                name: $(this).text(),
                percentage: $(this).data('percentage')
            });
        });
        
        // Generate HTML for preview
        let previewHtml = `
            <div class="estimate-preview">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h4>Estimate Preview</h4>
                        <p><strong>Client:</strong> ${clientText}</p>
                        <p><strong>Project:</strong> ${projectText}</p>
                        <p><strong>Client Type:</strong> ${clientType === 'existing' ? 'Existing' : 'New'}</p>
                        <p><strong>Project Type:</strong> ${projectType === 'existing' ? 'Existing' : 'New'}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p><strong>Estimate Date:</strong> ${data.estdate}</p>
                        <p><strong>Expiry Date:</strong> ${data.expdate}</p>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Client Address</h5>
                        <p>${data.client_address.replace(/\n/g, '<br>')}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Billing Address</h5>
                        <p>${data.billing_address.replace(/\n/g, '<br>')}</p>
                    </div>
                </div>
                
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Description</th>
                                <th>Unit Cost</th>
                                <th>Qty</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>`;
        
        // Add items to preview
        let totalAmount = 0;
        if (data.items) {
            data.items.forEach((item, index) => {
                const amount = parseFloat(item.unitCost || 0) * parseFloat(item.quantity || 0);
                totalAmount += amount;
                
                previewHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.name || ''}</td>
                        <td>${item.description || ''}</td>
                        <td>${parseFloat(item.unitCost || 0).toFixed(2)}</td>
                        <td>${item.quantity || ''}</td>
                        <td>${amount.toFixed(2)}</td>
                    </tr>`;
            });
        }
        
        // Calculate taxes and totals
        let totalTax = 0;
        let taxHtml = '';
        
        if (selectedTaxes.length > 0) {
            selectedTaxes.forEach(tax => {
                const taxAmount = (totalAmount * tax.percentage / 100).toFixed(2);
                totalTax += parseFloat(taxAmount);
                taxHtml += `<p>${tax.name} (${tax.percentage}%): ${taxAmount}</p>`;
            });
        }
        
        const discount = parseFloat(data.discount) || 0;
        const discountAmount = (totalAmount * discount / 100).toFixed(2);
        const grandTotal = (totalAmount - parseFloat(discountAmount) + totalTax).toFixed(2);
        
        // Continue with HTML
        previewHtml += `
                        </tbody>
                    </table>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Tax Details</h5>
                                ${selectedTaxes.length > 0 ? taxHtml : '<p>No taxes applied</p>'}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <td class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end">${totalAmount.toFixed(2)}</td>
                            </tr>
                            <tr>
                                <td class="text-end"><strong>Tax:</strong></td>
                                <td class="text-end">${totalTax.toFixed(2)}</td>
                            </tr>
                            <tr>
                                <td class="text-end"><strong>Discount (${discount}%):</strong></td>
                                <td class="text-end">${discountAmount}</td>
                            </tr>
                            <tr>
                                <td class="text-end"><strong>Grand Total:</strong></td>
                                <td class="text-end"><strong>${grandTotal}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h5>Other Information</h5>
                    <p>${data.other || 'No additional information'}</p>
                </div>
            </div>
        `;
        
        // Insert HTML into modal
        $('#previewContent').html(previewHtml);
    }


// Remove the warning if form is submitted
$('#estimate').submit(function() {
    $(window).off('beforeunload');
});
// Cancel button click handler - direct redirect without confirmation
$('#cancelBtn').click(function() {
    window.location.href = "{{ route('estimate.index') }}";
});
    // ... existing JavaScript ...
</script>

<style>
    .estimate-preview {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
    }

    .estimate-preview table {
        width: 100%;
        margin-bottom: 20px;
    }

    .estimate-preview table th {
        background-color: #f8f9fa;
        padding: 10px;
    }

    .estimate-preview table td {
        padding: 10px;
        border-top: 1px solid #dee2e6;
    }

    .estimate-preview .card {
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
    }

    .estimate-preview .card-body {
        padding: 15px;
    }

@media (max-width: 768px) {
  /* Only affect the main item table (replace .item-table with your table's class if it has one) */
  table.item-table thead th:nth-child(2),
  table.item-table tbody td:nth-child(2) { /* Item column */
    min-width: 180px;
  }

  table.item-table thead th:nth-child(3),
  table.item-table tbody td:nth-child(3) { /* Description column */
    min-width: 260px;
  }

  /* Allow horizontal scrolling only for the item table */
  .item-table-wrapper {
    overflow-x: auto;
  }
}

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
