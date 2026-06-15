@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Salary Settings</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('salary-settings.edit') }}" class="breadcrumb-link">Salary Settings</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Salary Settings</h4>
                    <div class="card-body">
                        <form id="salary-form" action="{{ route('salary-settings.update') }}" method="POST">
                            @csrf

                            <!-- DA and HRA Settings -->
                            <div class="settings-widget mb-4">
                                <div class="card-title">DA and HRA</div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="col-form-label">DA (%)</label>
                                        <div class="input-group">
                                            <input type="number" name="da_percentage" class="form-control" value="{{ $salarySettings->da_percentage }}" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="col-form-label">HRA (%)</label>
                                        <div class="input-group">
                                            <input type="number" name="hra_percentage" class="form-control" value="{{ $salarySettings->hra_percentage }}" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Provident Fund Settings -->
                            <div class="settings-widget mb-4">
                                <div class="card-title">Provident Fund Settings</div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="col-form-label">Employee Share (%)</label>
                                        <div class="input-group">
                                            <input type="number" name="pf_employee_share" class="form-control" value="{{ $salarySettings->pf_employee_share }}" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="col-form-label">Organization Share (%)</label>
                                        <div class="input-group">
                                            <input type="number" name="pf_organization_share" class="form-control" value="{{ $salarySettings->pf_organization_share }}" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ESI Settings -->
                            <div class="settings-widget mb-4">
                                <div class="card-title">ESI Settings</div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="col-form-label">Employee Share (%)</label>
                                        <div class="input-group">
                                            <input type="number" name="esi_employee_share" class="form-control" value="{{ $salarySettings->esi_employee_share }}" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="col-form-label">Organization Share (%)</label>
                                        <div class="input-group">
                                            <input type="number" name="esi_organization_share" class="form-control" value="{{ $salarySettings->esi_organization_share }}" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TDS Settings -->
                            <div class="settings-widget mb-4">
                                <div class="card-title">TDS Entries</div>
                                <div id="tds-entries-container">
                                    @if(isset($salarySettings->tds_entries) && is_array($salarySettings->tds_entries))
                                        @foreach ($salarySettings->tds_entries as $index => $entry)
                                            <div class="row tds-entry mb-2">
                                                <div class="col-sm-4">
                                                    <label class="col-form-label">Salary From</label>
                                                    <input type="text" name="tds_salary_from[]" class="form-control" value="{{ $entry['tds_salary_from'] }}" required>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="col-form-label">Salary To</label>
                                                    <input type="text" name="tds_salary_to[]" class="form-control" value="{{ $entry['tds_salary_to'] }}" required>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label class="col-form-label">%</label>
                                                    <input type="number" name="tds_percentage[]" class="form-control" value="{{ $entry['tds_percentage'] }}" required>
                                                </div>
                                                <div class="col-sm-2">
                                                    <button class="btn btn-danger remove-entry" type="button" data-index="{{ $index }}">Delete</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button id="add-entry" class="btn btn-primary" type="button">Add Entry</button>
                            </div>

                            <!-- Submit Button with Processing -->
                            <button id="submit-btn" class="btn btn-primary btn-lg btn-block" type="submit">
                                <span class="btn-text">Save</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Add TDS Entry
    document.getElementById('add-entry').addEventListener('click', function () {
        const container = document.getElementById('tds-entries-container');
        const newEntry = document.createElement('div');
        newEntry.classList.add('row', 'tds-entry', 'mb-2');
        newEntry.innerHTML = `
            <div class="col-sm-4">
                <label class="col-form-label">Salary From</label>
                <input type="text" name="tds_salary_from[]" class="form-control" required>
            </div>
            <div class="col-sm-4">
                <label class="col-form-label">Salary To</label>
                <input type="text" name="tds_salary_to[]" class="form-control" required>
            </div>
            <div class="col-sm-2">
                <label class="col-form-label">%</label>
                <input type="number" name="tds_percentage[]" class="form-control" required>
            </div>
            <div class="col-sm-2">
                <button class="btn btn-danger remove-entry" type="button">Delete</button>
            </div>
        `;
        container.appendChild(newEntry);
    });

    // Remove TDS Entry
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-entry')) {
            const index = e.target.getAttribute('data-index');
            if (index !== undefined) {
                fetch(`/salary-settings/tds-entry/${index}`, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            e.target.closest('.tds-entry').remove();
                        }
                    });
            } else {
                e.target.closest('.tds-entry').remove();
            }
        }
    });

    // Processing Button Script
    document.getElementById('submit-btn').addEventListener('click', function () {
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
        document.getElementById('salary-form').submit();
    });
</script>
@endsection
