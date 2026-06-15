@extends('layouts.index')

@section('content')

<!-- Add Budget Form -->
<div class="container mt-4 mb-4">
<div class="container mt-4">
    <h5 class="modal-title fs-5">Add Budget</h5>
    
    <!-- Form Start -->
    <form action="{{ route('budgets.store') }}" method="POST" id="budgetForm">
        @csrf

        <!-- Budget Title and Type -->
        <div class="input-block mb-3">
            <label class="col-form-label">Budget Title</label>
            <input class="form-control" type="text" name="budget_title" placeholder="Budget Title" required>
        </div>

        <label class="col-form-label">Choose Budget Type</label>
        <div class="input-block mb-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="budget_type" id="project" value="project" required>
                <label class="form-check-label" for="project">Project</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="budget_type" id="category" value="category" required>
                <label class="form-check-label" for="category">Category</label>
            </div>
        </div>

        <!-- Dates -->
        <div class="input-block mb-3">
            <label class="col-form-label">Start Date</label>
            <input class="form-control" type="date" name="start_date" required>
        </div>
        <div class="input-block mb-3">
            <label class="col-form-label">End Date</label>
            <input class="form-control" type="date" name="end_date" required>
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
            <div class="row AlLRevenues mb-3">
                <div class="col-sm-6">
                    <div class="input-block">
                        <label class="col-form-label">Revenue Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="revenue_title[]" placeholder="Revenue Title" required>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="input-block">
                        <label class="col-form-label">Revenue Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control revenue-amount" name="revenue_amount[]" placeholder="Amount" min="0" step="0.01" required>
                        <div class="invalid-feedback">Amount must be positive</div>
                    </div>
                </div>
                <div class="col-sm-1 align-self-end">
                    <button type="button" class="btn btn-danger btn-sm remove-btn">×</button>
                </div>
            </div>
        </div>

        <div class="input-block mb-3">
            <label class="col-form-label">Overall Revenues <span class="text-danger">(A)</span></label>
            <input class="form-control" type="text" name="overall_revenues" id="overall_revenues" placeholder="Overall Revenues" readonly>
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
            <div class="row AlLExpenses mb-3">
                <div class="col-sm-6">
                    <div class="input-block">
                        <label class="col-form-label">Expense Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="expenses_title[]" placeholder="Expense Title" required>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="input-block">
                        <label class="col-form-label">Expense Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control expense-amount" name="expenses_amount[]" placeholder="Amount" min="0" step="0.01" required>
                        <div class="invalid-feedback">Amount must be positive</div>
                    </div>
                </div>
                <div class="col-sm-1 align-self-end">
                    <button type="button" class="btn btn-danger btn-sm remove-btn">×</button>
                </div>
            </div>
        </div>

        <div class="input-block mb-3">
            <label class="col-form-label">Overall Expenses <span class="text-danger">(B)</span></label>
            <input class="form-control" type="text" name="overall_expenses" id="overall_expenses" placeholder="Overall Expenses" readonly>
        </div>
        
        <div class="input-block mb-3">
            <label class="col-form-label">Expected Profit (C = A - B)</label>
            <input class="form-control" type="text" name="expected_profit" id="expected_profit" readonly>
            <div class="invalid-feedback" id="profit-error" style="display: none;">Warning: Negative profit detected!</div>
        </div>

        <!-- Tax and Calculation -->
        <div class="input-block mb-3">
            <label class="col-form-label">Tax Amount</label>
            <input class="form-control" type="number" name="tax_amount" id="tax_amount" placeholder="Tax Amount" min="0" step="0.01">
            <div class="invalid-feedback">Tax amount must be positive</div>
        </div>

        <div class="input-block mb-3">
            <label class="col-form-label">Budget Amount</label>
            <input class="form-control" type="text" name="budget_amount" id="budget_amount" placeholder="Budget Amount" readonly>
            <div class="invalid-feedback" id="budget-error" style="display: none;">Warning: Negative budget amount detected!</div>

       <div class="submit-section">
  <button id="submitBtn" type="submit" class="btn btn-primary submit-btn" style="margin-bottom: 40px;">
    Submit
  </button>
</div>


<br>
    </form>
    <!-- /Form End -->
</div>
</div>
<!-- /Add Budget Form -->

<script>
    // Function to validate a single input for positive value
    function validatePositiveInput(input) {
        const value = parseFloat(input.value);
        if (isNaN(value) || value < 0) {
            input.classList.add('is-invalid');
            return false;
        } else {
            input.classList.remove('is-invalid');
            return true;
        }
    }

    // Function to calculate overall revenues
    function calculateOverallRevenues() {
        let totalRevenue = 0;
        const revenueAmounts = document.querySelectorAll('.revenue-amount');
        let allValid = true;

        revenueAmounts.forEach(input => {
            if (!validatePositiveInput(input)) {
                allValid = false;
            }
            const value = parseFloat(input.value) || 0;
            totalRevenue += value;
        });

        document.getElementById('overall_revenues').value = totalRevenue.toFixed(2);
        calculateExpectedProfit();
        calculateBudgetAmount();
        return allValid;
    }

    // Function to calculate overall expenses
    function calculateOverallExpenses() {
        let totalExpenses = 0;
        const expenseAmounts = document.querySelectorAll('.expense-amount');
        let allValid = true;

        expenseAmounts.forEach(input => {
            if (!validatePositiveInput(input)) {
                allValid = false;
            }
            const value = parseFloat(input.value) || 0;
            totalExpenses += value;
        });

        document.getElementById('overall_expenses').value = totalExpenses.toFixed(2);
        calculateExpectedProfit();
        calculateBudgetAmount();
        return allValid;
    }

    // Function to calculate expected profit
    function calculateExpectedProfit() {
        const overallRevenue = parseFloat(document.getElementById('overall_revenues').value) || 0;
        const overallExpenses = parseFloat(document.getElementById('overall_expenses').value) || 0;
        const expectedProfit = overallRevenue - overallExpenses;

        const profitInput = document.getElementById('expected_profit');
        profitInput.value = expectedProfit.toFixed(2);
        
        // Show warning if profit is negative
        const profitError = document.getElementById('profit-error');
        if (expectedProfit < 0) {
            profitError.style.display = 'block';
            profitInput.classList.add('is-invalid');
        } else {
            profitError.style.display = 'none';
            profitInput.classList.remove('is-invalid');
        }
    }

    // Function to calculate budget amount
    function calculateBudgetAmount() {
        const expectedProfit = parseFloat(document.getElementById('expected_profit').value) || 0;
        const taxAmount = parseFloat(document.getElementById('tax_amount').value) || 0;

        // Validate tax amount
        const taxInput = document.getElementById('tax_amount');
        const taxValid = validatePositiveInput(taxInput);

        // Budget amount = Expected Profit - Tax Amount
        const budgetAmount = expectedProfit - taxAmount;
        const budgetInput = document.getElementById('budget_amount');
        budgetInput.value = budgetAmount.toFixed(2);

        // Show warning if budget is negative
        const budgetError = document.getElementById('budget-error');
        if (budgetAmount < 0) {
            budgetError.style.display = 'block';
            budgetInput.classList.add('is-invalid');
        } else {
            budgetError.style.display = 'none';
            budgetInput.classList.remove('is-invalid');
        }

        return taxValid;
    }

    // Function to validate the entire form
    function validateForm() {
        const revenuesValid = calculateOverallRevenues();
        const expensesValid = calculateOverallExpenses();
        const taxValid = calculateBudgetAmount();
        
        const expectedProfit = parseFloat(document.getElementById('expected_profit').value) || 0;
        const budgetAmount = parseFloat(document.getElementById('budget_amount').value) || 0;
        
        // Check if any amount is negative
        let anyNegative = false;
        document.querySelectorAll('.revenue-amount, .expense-amount, #tax_amount').forEach(input => {
            const value = parseFloat(input.value);
            if (value < 0) {
                anyNegative = true;
            }
        });
        
        return revenuesValid && expensesValid && taxValid && !anyNegative;
    }

    // Add event listeners when DOM is loaded
    document.addEventListener('DOMContentLoaded', function () {
        // Event delegation for dynamic elements
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('revenue-amount')) {
                calculateOverallRevenues();
            } else if (e.target.classList.contains('expense-amount')) {
                calculateOverallExpenses();
            } else if (e.target.id === 'tax_amount') {
                calculateBudgetAmount();
            }
        });

        // Add new revenue row
        document.getElementById('addRevenueBtn').addEventListener('click', function() {
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
                        <input type="number" class="form-control revenue-amount" name="revenue_amount[]" placeholder="Amount" min="0" step="0.01" required>
                        <div class="invalid-feedback">Amount must be positive</div>
                    </div>
                </div>
                <div class="col-sm-1 align-self-end">
                    <button type="button" class="btn btn-danger btn-sm remove-btn">×</button>
                </div>
            `;

            revenuesContainer.appendChild(newRevenueRow);
        });

        // Add new expense row
        document.getElementById('addExpenseBtn').addEventListener('click', function() {
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
                        <input type="number" class="form-control expense-amount" name="expenses_amount[]" placeholder="Amount" min="0" step="0.01" required>
                        <div class="invalid-feedback">Amount must be positive</div>
                    </div>
                </div>
                <div class="col-sm-1 align-self-end">
                    <button type="button" class="btn btn-danger btn-sm remove-btn">×</button>
                </div>
            `;

            expensesContainer.appendChild(newExpenseRow);
        });

        // Remove row (using event delegation)
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-btn')) {
                const row = e.target.closest('.row');
                if (row) {
                    row.remove();
                    calculateOverallRevenues();
                    calculateOverallExpenses();
                }
            }
        });

        // Form submission validation
        document.getElementById('budgetForm').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                alert('Please fix all validation errors before submitting.');
            }
            
            // Additional check for negative profit/budget
            const expectedProfit = parseFloat(document.getElementById('expected_profit').value) || 0;
            const budgetAmount = parseFloat(document.getElementById('budget_amount').value) || 0;
            
            if (expectedProfit < 0 || budgetAmount < 0) {
                const proceed = confirm('Warning: Your budget shows a negative profit or budget amount. Are you sure you want to proceed?');
                if (!proceed) {
                    e.preventDefault();
                }
            }
        });
    });

    // Form submission validation
document.getElementById('budgetForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('submitBtn');
    
    if (!validateForm()) {
        e.preventDefault();
        alert('Please fix all validation errors before submitting.');
        return;
    }
    
    // Additional check for negative profit/budget
    const expectedProfit = parseFloat(document.getElementById('expected_profit').value) || 0;
    const budgetAmount = parseFloat(document.getElementById('budget_amount').value) || 0;
    
    if (expectedProfit < 0 || budgetAmount < 0) {
        const proceed = confirm('Warning: Your budget shows a negative profit or budget amount. Are you sure you want to proceed?');
        if (!proceed) {
            e.preventDefault();
            return;
        }
    }
    
    // Only disable if validation passes
    btn.disabled = true;
    btn.textContent = 'Processing...';
});
</script>

<style>
    .is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875em;
    }
    .remove-btn {
        font-size: 1.2rem;
        line-height: 1;
        padding: 0.25rem 0.5rem;
    }
</style>

@endsection