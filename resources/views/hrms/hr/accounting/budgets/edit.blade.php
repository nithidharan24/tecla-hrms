@extends('layouts.index')

@section('content')

<!-- Edit Budget Form -->
<div class=" mt-4" style="min-height: 395px;">
<div class="p-5 mb-4">
    <h5 class="modal-title fs-5">Edit Budget</h5>
    
    <!-- Form Start -->
    <form action="{{ route('budgets.update', $budget->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Budget Title and Type -->
        <div class="input-block mb-3">
            <label class="col-form-label">Budget Title</label>
            <input class="form-control" type="text" name="budget_title" value="{{ $budget->budget_title }}" placeholder="Budget Title" required>
        </div>

        <label class="col-form-label">Choose Budget Type</label>
        <div class="input-block mb-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="budget_type" id="project" value="project" {{ $budget->budget_type == 'project' ? 'checked' : '' }} required>
                <label class="form-check-label" for="project">Project</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="budget_type" id="category" value="category" {{ $budget->budget_type == 'category' ? 'checked' : '' }} required>
                <label class="form-check-label" for="category">Category</label>
            </div>
        </div>

        <!-- Dates -->
        <div class="input-block mb-3">
            <label class="col-form-label">Start Date</label>
            <input class="form-control" type="date" name="start_date" 
                   value="{{ isset($budget) ? \Carbon\Carbon::parse($budget->start_date)->format('Y-m-d') : '' }}" required>
        </div>
        <div class="input-block mb-3">
            <label class="col-form-label">End Date</label>
            <input class="form-control" type="date" name="end_date" 
                   value="{{ isset($budget) ? \Carbon\Carbon::parse($budget->end_date)->format('Y-m-d') : '' }}" required>
        </div>

        <!-- Revenues Section -->
        <div class="input-block mb-3">
            <label class="col-form-label">Expected Revenues <span class="text-danger">*</span></label>
            <div class="d-flex justify-content-between align-items-center">
                <span></span>
                <button type="button" class="btn btn-primary" id="addRevenueBtn">+</button>
            </div>
        </div>

        <div class="AllRevenuesClass">
            @foreach (json_decode($budget->revenue_title) as $key => $revenueTitle)
            <div class="row AlLRevenues mb-3">
                <div class="col-sm-6">
                    <div class="input-block">
                        <label class="col-form-label">Revenue Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="revenue_title[]" value="{{ $revenueTitle }}" required>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="input-block">
                        <label class="col-form-label">Revenue Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="revenue_amount[]" value="{{ json_decode($budget->revenue_amount)[$key] }}" required>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="input-block mb-3">
            <label class="col-form-label">Overall Revenues <span class="text-danger">(A)</span></label>
            <input class="form-control" type="text" name="overall_revenues" id="overall_revenues" value="{{ $budget->total_revenue }}" readonly>
        </div>

        <!-- Expenses Section -->
        <div class="input-block mb-3">
            <label class="col-form-label">Expected Expenses <span class="text-danger">*</span></label>
            <div class="d-flex justify-content-between align-items-center">
                <span></span>
                <button type="button" class="btn btn-primary" id="addExpenseBtn">+</button>
            </div>
        </div>

        <div class="AllExpensesClass">
            @foreach (json_decode($budget->expenses_title) as $key => $expenseTitle)
            <div class="row AlLExpenses mb-3">
                <div class="col-sm-6">
                    <div class="input-block">
                        <label class="col-form-label">Expense Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="expenses_title[]" value="{{ $expenseTitle }}" required>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="input-block">
                        <label class="col-form-label">Expense Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="expenses_amount[]" value="{{ json_decode($budget->expenses_amount)[$key] }}" required>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="input-block mb-3">
            <label class="col-form-label">Overall Expenses <span class="text-danger">(B)</span></label>
            <input class="form-control" type="text" name="overall_expenses" id="overall_expenses" value="{{ $budget->total_expenses }}" readonly>
        </div>

        <div class="input-block mb-3">
            <label class="col-form-label">Expected Profit (C = A - B)</label>
            <input class="form-control" type="text" name="expected_profit" id="expected_profit" value="{{ $budget->expected_profit }}" readonly>
        </div>

        <!-- Tax and Calculation -->
        <div class="input-block mb-3">
            <label class="col-form-label">Tax Amount</label>
            <input class="form-control" type="number" name="tax_amount" id="tax_amount" value="{{ $budget->tax_amount }}" placeholder="Tax Amount">
        </div>

        <div class="input-block mb-3">
            <label class="col-form-label">Budget Amount</label>
            <input class="form-control" type="text" name="budget_amount" id="budget_amount" value="{{ $budget->budget_amount }}" readonly>
        </div>

        <div class="submit-section">
            <button type="submit" class="btn btn-primary submit-btn">Update Budget</button>
        </div>
    </form>
    <!-- /Form End -->
</div>
</div>
<!-- /Edit Budget Form -->

<script>
    // Function to calculate overall revenues
    function calculateOverallRevenues() {
        const revenueInputs = document.querySelectorAll('input[name="revenue_amount[]"]');
        let totalRevenue = 0;
        revenueInputs.forEach(input => {
            const value = parseFloat(input.value) || 0; // Handle NaN by defaulting to 0
            totalRevenue += value;
        });
        document.getElementById('overall_revenues').value = totalRevenue; // Update overall revenues
        calculateExpectedProfit(); // Update profit after revenues are calculated
    }

    // Function to calculate overall expenses
    function calculateOverallExpenses() {
        const expenseInputs = document.querySelectorAll('input[name="expenses_amount[]"]');
        let totalExpenses = 0;
        expenseInputs.forEach(input => {
            const value = parseFloat(input.value) || 0; // Handle NaN by defaulting to 0
            totalExpenses += value;
        });
        document.getElementById('overall_expenses').value = totalExpenses; // Update overall expenses
        calculateExpectedProfit(); // Update profit after expenses are calculated
    }

    // Function to calculate expected profit
    function calculateExpectedProfit() {
        const overallRevenues = parseFloat(document.getElementById('overall_revenues').value) || 0;
        const overallExpenses = parseFloat(document.getElementById('overall_expenses').value) || 0;
        const expectedProfit = overallRevenues - overallExpenses;
        document.getElementById('expected_profit').value = expectedProfit; // Update expected profit
        calculateBudgetAmount(); // Update budget amount after profit is calculated
    }

    // Function to calculate budget amount
    function calculateBudgetAmount() {
        const expectedProfit = parseFloat(document.getElementById('expected_profit').value) || 0;
        const taxAmount = parseFloat(document.getElementById('tax_amount').value) || 0;
        const budgetAmount = expectedProfit - taxAmount;
        document.getElementById('budget_amount').value = budgetAmount; // Update budget amount
    }

    // Main function to set up event listeners
    document.addEventListener('DOMContentLoaded', function () {
        // Set up event listeners for existing inputs
        const revenueInputs = document.querySelectorAll('input[name="revenue_amount[]"]');
        revenueInputs.forEach(input => input.addEventListener('input', calculateOverallRevenues));

        const expenseInputs = document.querySelectorAll('input[name="expenses_amount[]"]');
        expenseInputs.forEach(input => input.addEventListener('input', calculateOverallExpenses));

        const taxInput = document.getElementById('tax_amount');
        if (taxInput) {
            taxInput.addEventListener('input', calculateBudgetAmount);
        }

        // Add Revenue button event listener
        document.getElementById('addRevenueBtn').addEventListener('click', function () {
            const revenuesContainer = document.querySelector('.AllRevenuesClass');
            const newRevenueRow = document.createElement('div');
            newRevenueRow.classList.add('row', 'AlLRevenues', 'mb-3');

            newRevenueRow.innerHTML = `
                <div class="col-sm-6">
                    <div class="input-block">
                        <label class="col-form-label">Revenue Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="revenue_title[]" placeholder="Revenue Title" required>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="input-block">
                        <label class="col-form-label">Revenue Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="revenue_amount[]" placeholder="Amount" required>
                    </div>
                </div>
            `;
            revenuesContainer.appendChild(newRevenueRow);
            const newRevenueInput = newRevenueRow.querySelector('input[name="revenue_amount[]"]');
            newRevenueInput.addEventListener('input', calculateOverallRevenues);
        });

        // Add Expense button event listener
        document.getElementById('addExpenseBtn').addEventListener('click', function () {
            const expensesContainer = document.querySelector('.AllExpensesClass');
            const newExpenseRow = document.createElement('div');
            newExpenseRow.classList.add('row', 'AlLExpenses', 'mb-3');

            newExpenseRow.innerHTML = `
                <div class="col-sm-6">
                    <div class="input-block">
                        <label class="col-form-label">Expense Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="expenses_title[]" placeholder="Expense Title" required>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="input-block">
                        <label class="col-form-label">Expense Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="expenses_amount[]" placeholder="Amount" required>
                    </div>
                </div>
            `;
            expensesContainer.appendChild(newExpenseRow);
            const newExpenseInput = newExpenseRow.querySelector('input[name="expenses_amount[]"]');
            newExpenseInput.addEventListener('input', calculateOverallExpenses);
        });
    });
</script>

@endsection
