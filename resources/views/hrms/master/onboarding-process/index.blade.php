@extends('layouts.index')

@section('content')
<style>
    /* General Layout & Spacing */
    .container {
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding: 1.5rem;
    }
    .shadow-md {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .rounded-lg {
        border-radius: 0.5rem;
    }
    .p-6 {
        padding: 1.5rem;
    }
    .mb-6 {
        margin-bottom: 1.5rem;
    }
    .flex {
        display: flex;
    }
    .items-center {
        align-items: center;
    }
    .justify-between {
        justify-content: space-between;
    }
    .gap-4 {
        gap: 1rem;
    }
    .grid {
        display: grid;
    }
    .grid-cols-1 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
    @media (min-width: 768px) { /* md breakpoint */
        .md\:grid-cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .md\:col-span-3 {
            grid-column: span 3 / span 3;
        }
    }
    /* Typography */
    .text-2xl {
        font-size: 1.5rem;
    }
    .text-3xl {
        font-size: 1.875rem;
    }
    .font-bold {
        font-weight: 700;
    }
    .text-sm {
        font-size: 0.875rem;
    }
    .text-xs {
        font-size: 0.75rem;
    }
    .font-medium {
        font-weight: 500;
    }
    .text-gray-800 {
        color: #1f2937;
    }
    .text-gray-600 {
        color: #4b5563;
    }
    .text-gray-700 {
        color: #374151;
    }
    .text-gray-500 {
        color: #6b7280;
    }
    .text-gray-900 {
        color: #111827;
    }
    .uppercase {
        text-transform: uppercase;
    }
    .tracking-wider {
        letter-spacing: 0.05em;
    }
    .text-left {
        text-align: left;
    }
    .text-center {
        text-align: center;
    }
    .whitespace-nowrap {
        white-space: nowrap;
    }
    /* Colors & Backgrounds */
    .bg-white {
        background-color: #fff;
    }
    .bg-gray-50 {
        background-color: #f9fafb;
    }
    .bg-green-500 {
        background-color: #22c55e;
    }
    .hover\:bg-green-600:hover {
        background-color: #16a34a;
    }
    .bg-blue-500 {
        background-color: #3b82f6;
    }
    .hover\:bg-blue-600:hover {
        background-color: #2563eb;
    }
    .text-white {
        color: #fff;
    }
    .text-red-500 {
        color: #ef4444;
    }
    .text-green-700 {
        color: #047857;
    }
    .text-red-700 {
        color: #b91c1c;
    }
    .text-blue-500 {
        color: #3b82f6;
    }
    .hover\:underline:hover {
        text-decoration-line: underline;
    }
    /* Borders & Shadows */
    .border {
        border-width: 1px;
    }
    .border-gray-300 {
        border-color: #d1d5db;
    }
    .border-gray-200 {
        border-color: #e5e7eb;
    }
    .border-b {
        border-bottom-width: 1px;
    }
    .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    /* Form Elements */
    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
        display: block;
        width: 100%;
        padding: 0.625rem 0.75rem; /* Increased padding */
        margin-top: 0.25rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        font-size: 0.875rem;
        color: #374151;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus,
    select:focus,
    textarea:focus {
        border-color: #3b82f6;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
    }
    label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.25rem; /* Space between label and input */
    }
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }
    /* Buttons */
    button, .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px;
        border-radius: 0.375rem;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .bg-green-500 {
        background-color: #22c55e;
        color: #fff;
        border: 1px solid transparent;
    }
    .bg-green-500:hover {
        background-color: #16a34a;
    }
    .bg-blue-500 {
        background-color: #3b82f6;
        color: #fff;
        border: 1px solid transparent;
    }
    .bg-blue-500:hover {
        background-color: #2563eb;
    }
    .text-gray-400 {
        color: #9ca3af;
    }
    .hover\:text-gray-600:hover {
        color: #4b5563;
    }
    /* Table Styling */
    .min-w-full {
        min-width: 100%;
    }
    .table-auto {
        width: 100%;
        border-collapse: collapse;
    }
    thead th {
        padding: 0.75rem 1.5rem;
        background-color: #f9fafb;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: #4b5563;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e5e7eb;
    }
    tbody td {
        padding: 1rem 1.5rem; /* Increased padding */
        font-size: 0.875rem;
        color: #374151;
        border-bottom: 1px solid #f3f4f6; /* Lighter border for rows */
    }
    tbody tr:hover {
        background-color: #f9fafb; /* Subtle hover effect */
    }
    /* Dropdown Menus (Status & Actions) */
    .dropdown-menu {
        position: absolute;
        right: 0;
        margin-top: 0.5rem;
        width: 10rem; /* Adjusted width */
        border-radius: 0.375rem;
        background-color: #fff;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 10;
        border: 1px solid #e5e7eb;
        padding: 0.25rem 0; /* Added padding */
    }
    .dropdown-item {
        display: block;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        color: #374151;
        text-decoration: none;
        transition: background-color 0.15s ease-in-out;
    }
    .dropdown-item:hover {
        background-color: #f3f4f6;
    }
    .hidden {
        display: none;
    }
    .relative {
        position: relative;
    }
    .inline-block {
        display: inline-block;
    }
    .w-2 {
        width: 0.5rem;
    }
    .h-2 {
        height: 0.5rem;
    }
    .rounded-full {
        border-radius: 9999px;
    }
    .mr-2 {
        margin-right: 0.5rem;
    }
    .ml-1 {
        margin-left: 0.25rem;
    }
    .align-middle {
        vertical-align: middle;
    }
    .p-1 {
        padding: 0.25rem;
    }
    .w-20 {
        width: 5rem;
    }
    .h-5 {
        height: 1.25rem;
    }
    .w-5 {
        width: 1.25rem;
    }
    .h-4 {
        height: 1rem;
    }
    .w-4 {
        width: 1rem;
    }
</style>
<div class="container mx-auto p-6">
   @if (session('success'))
    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>

    <script>
        setTimeout(() => {
            const alert = document.getElementById('success-alert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500); 
            }
        }, 3000); 
    </script>
@endif


    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Onboarding Records</h1>
        <a href="{{ route('onboarding.create') }}" class="btn bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add New Onboarding
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label for="client_name" class="block text-sm font-medium text-gray-700">Client Name</label>
                <input type="text" name="client_name" id="client_name" placeholder="Enter Client Name" class="mt-1">
            </div>
        </div>
        <div class="flex justify-end">
            <button type="button" class="btn bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md">
                Search
            </button>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200">
            <div style="margin-left: 25px" class=" flex items-center gap-2 text-sm text-gray-700">
                Show
                <select class="border border-gray-300 rounded-md p-1">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                    <option>100</option>
                </select>
                entries
            </div>
        </div>
      <div class="overflow-x-auto">
    <table class="min-w-full table-auto">
        <thead style="background-color: #9fcefc !important;">
            <tr>
              
                <th>Name</th>
                <th>Gender</th>
                <th>Contact Person</th>
                <th>Email</th>
                <th>Mobile</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @forelse ($onboardings as $onboarding)
            <tr>
                <td data-label="Full Name" class="high">{{ $onboarding->full_name }}</td>
<td data-label="Gender">{{ $onboarding->gender }}</td>
<td data-label="Emergency Contact" class="od-chip-highlight">
    <span>{{ $onboarding->emergency_contact_name ?? 'N/A' }}</span>
</td>
<td data-label="Email">{{ $onboarding->personal_email_id }}</td>
<td data-label="Mobile">{{ $onboarding->mobile_number }}</td>
<td data-label="Actions">
    <div class="relative inline-block text-left">
        <button type="button" class="btn flex items-center text-gray-400 hover:text-gray-600 focus:outline-none" id="actions-menu-button-{{ $onboarding->id }}">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
            </svg>
        </button>
        <div class="dropdown-menu hidden absolute right-0 z-10 mt-2 w-36 bg-white border border-gray-200 rounded-md shadow-lg">
            <div class="py-1">
                <a href="{{ route('onboarding.show', $onboarding->id) }}" class="dropdown-item block px-4 py-2 hover:bg-gray-100">View</a>
                <a href="{{ route('onboarding.edit', $onboarding->id) }}" class="dropdown-item block px-4 py-2 hover:bg-gray-100">Edit</a>
                <a href="{{ route('onboarding.downloadPdf', $onboarding->id) }}" class="dropdown-item block px-4 py-2 hover:bg-gray-100">PDF</a>
                <form action="{{ route('onboarding.destroy', $onboarding->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">Delete</button>
                </form>
            </div>
        </div>
    </div>
</td>

            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No onboarding records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const rowChecks = document.querySelectorAll('.row-check');

    // Toggle all checkboxes
    checkAll.addEventListener('change', function() {
        rowChecks.forEach(chk => {
            chk.checked = this.checked;
            chk.closest('tr').classList.toggle('row-selected', chk.checked);
        });
    });

    // Toggle individual row highlight
    rowChecks.forEach(chk => {
        chk.addEventListener('change', function() {
            this.closest('tr').classList.toggle('row-selected', this.checked);
        });
    });
});
</script>

    </div>
 
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Dropdown toggle for Status and Actions
        $('[id^="status-menu-button-"], [id^="actions-menu-button-"]').on('click', function() {
            const dropdownMenu = $(this).next('.dropdown-menu');
            const isExpanded = $(this).attr('aria-expanded') === 'true';

            // Hide all other dropdowns
            $('.dropdown-menu').not(dropdownMenu).slideUp(150).addClass('hidden');
            $('[id^="status-menu-button-"], [id^="actions-menu-button-"]').not(this).attr('aria-expanded', 'false');

            // Toggle the clicked dropdown
            dropdownMenu.slideToggle(150, function() {
                $(this).toggleClass('hidden', !$(this).is(':visible'));
            });
            $(this).attr('aria-expanded', !isExpanded);
        });

        // Close dropdowns when clicking outside
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.relative.inline-block').length) {
                $('.dropdown-menu').slideUp(150).addClass('hidden');
                $('[id^="status-menu-button-"], [id^="actions-menu-button-"]').attr('aria-expanded', 'false');
            }
        });
    });
</script>
@endsection
