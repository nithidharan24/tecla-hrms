@extends('layouts.index')

@section('content')
@php
    $templateCards = [
        [
            'title' => 'Offer Letter',
            'description' => 'Manage offer letter templates and previews.',
            'icon' => 'fa-file-contract',
            'route' => route('offer-letter.index'),
        ],
        [
            'title' => 'Promotion Letter',
            'description' => 'Create and maintain promotion letters.',
            'icon' => 'fa-level-up-alt',
            'route' => route('promotion-letter.index'),
        ],
        [
            'title' => 'Termination Letter',
            'description' => 'Configure termination letter templates.',
            'icon' => 'fa-user-times',
            'route' => route('termination-letter-templates.index'),
        ],
        [
            'title' => 'Hike Letter',
            'description' => 'Prepare salary revision and hike letters.',
            'icon' => 'fa-money-bill-wave',
            'route' => route('hike-letter.index'),
        ],
        [
            'title' => 'Appointment Letter',
            'description' => 'Build appointment letter templates.',
            'icon' => 'fa-handshake',
            'route' => route('appointment-letter.index'),
        ],
        [
            'title' => 'Memo Letter',
            'description' => 'Manage employee memo letter templates.',
            'icon' => 'fa-sticky-note',
            'route' => route('memo.index'),
        ],
        [
            'title' => 'Invoice Template',
            'description' => 'Open invoice template settings.',
            'icon' => 'fa-file-invoice-dollar',
            'route' => route('invoice-template.index'),
        ],
        [
            'title' => 'Payroll Template',
            'description' => 'Manage payroll template formats.',
            'icon' => 'fa-file-invoice',
            'route' => route('payroll-template.index'),
        ],
    ];

    $signatureCount = $signatures->count();
@endphp

<style>
    .letters-page {
        color: #1f2937;
    }

    .letters-shell {
        max-width: 1180px;
        margin: 0 auto;
    }

    .letters-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 18px;
        padding: 22px 0 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .letters-title {
        margin: 0;
        font-size: 26px;
        font-weight: 700;
        color: #111827;
    }

    .letters-subtitle {
        margin: 8px 0 0;
        color: #6b7280;
        font-size: 14px;
    }

    .letters-stats {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .letters-stat {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 14px;
        min-width: 132px;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
    }

    .letters-stat strong {
        display: block;
        font-size: 20px;
        line-height: 1;
        color: #f97316;
    }

    .letters-stat span {
        display: block;
        margin-top: 5px;
        color: #6b7280;
        font-size: 12px;
    }

    .letters-tabs {
        display: flex;
        gap: 8px;
        margin: 22px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .letters-tab {
        border: 0;
        background: transparent;
        color: #6b7280;
        padding: 11px 14px;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 3px solid transparent;
        cursor: pointer;
    }

    .letters-tab i {
        margin-right: 7px;
    }

    .letters-tab.active {
        color: #f97316;
        border-bottom-color: #f97316;
    }

    .letters-panel {
        display: none;
    }

    .letters-panel.active {
        display: block;
    }

    .template-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(245px, 1fr));
        gap: 16px;
    }

    .template-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 18px;
        color: #1f2937 !important;
        text-decoration: none !important;
        display: flex;
        gap: 14px;
        min-height: 126px;
        transition: box-shadow 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    }

    .template-card:hover {
        border-color: #fb923c;
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.1);
    }

    .template-icon {
        width: 46px;
        height: 46px;
        border-radius: 8px;
        background: #fff7ed;
        color: #f97316;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        font-size: 20px;
    }

    .template-title {
        margin: 0 0 8px;
        font-size: 16px;
        font-weight: 700;
        color: #111827;
    }

    .template-description {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.45;
    }

    .signature-layout {
        display: grid;
        grid-template-columns: minmax(280px, 420px) 1fr;
        gap: 18px;
        align-items: start;
    }

    .signature-card,
    .signature-list {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    }

    .signature-card {
        padding: 20px;
    }

    .section-heading {
        margin: 0 0 6px;
        color: #111827;
        font-size: 18px;
        font-weight: 700;
    }

    .section-note {
        margin: 0 0 18px;
        color: #6b7280;
        font-size: 13px;
    }

    .form-label {
        color: #374151;
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 7px;
    }

    .signature-preview {
        min-height: 126px;
        border: 1px dashed #d1d5db;
        border-radius: 8px;
        background: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 14px 0;
        padding: 14px;
        overflow: hidden;
    }

    .signature-preview img {
        max-width: 100%;
        max-height: 104px;
        object-fit: contain;
    }

    .signature-preview span {
        color: #9ca3af;
        font-size: 13px;
    }

    .save-signature-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 0;
        border-radius: 8px;
        background: #f97316;
        color: #fff;
        padding: 10px 16px;
        font-weight: 700;
        min-height: 42px;
        width: 100%;
    }

    .save-signature-btn:hover {
        background: #ea580c;
    }

    .signature-list {
        overflow: hidden;
    }

    .signature-list-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .signature-table {
        width: 100%;
        margin: 0;
    }

    .signature-table th {
        background: #f9fafb;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .signature-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }

    .signature-thumb {
        width: 126px;
        height: 54px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .signature-thumb img {
        max-width: 112px;
        max-height: 42px;
        object-fit: contain;
    }

    .empty-signature {
        color: #9ca3af;
        font-size: 12px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-pill.ready {
        color: #047857;
        background: #ecfdf5;
    }

    .status-pill.missing {
        color: #92400e;
        background: #fffbeb;
    }

    @media (max-width: 900px) {
        .letters-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .letters-stats {
            justify-content: flex-start;
            width: 100%;
        }

        .signature-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .content.container-fluid {
            padding-left: 12px;
            padding-right: 12px;
        }

        .letters-tabs {
            overflow-x: auto;
        }

        .letters-tab {
            white-space: nowrap;
        }

        .signature-list {
            overflow-x: auto;
        }

        .signature-table {
            min-width: 620px;
        }
    }
</style>

<div class="content container-fluid letters-page">
    <div class="letters-shell">
        <div class="letters-header">
            <div>
                <h3 class="letters-title">Letters Master</h3>
                <p class="letters-subtitle">Manage templates and assign signature images for every letter type.</p>
            </div>
            <div class="letters-stats">
                <div class="letters-stat">
                    <strong>{{ count($templateCards) }}</strong>
                    <span>Template areas</span>
                </div>
                <div class="letters-stat">
                    <strong>{{ $signatureCount }}</strong>
                    <span>Signatures uploaded</span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mt-3 mb-0">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mt-3 mb-0">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger mt-3 mb-0">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="letters-tabs" role="tablist">
            <button class="letters-tab active" type="button" data-tab="templates-panel">
                <i class="fas fa-layer-group"></i> Letter Templates
            </button>
            <button class="letters-tab" type="button" data-tab="signature-panel">
                <i class="fas fa-signature"></i> Signature Master
            </button>
        </div>

        <div id="templates-panel" class="letters-panel active">
            <div class="template-grid">
                @foreach($templateCards as $card)
                    <a href="{{ $card['route'] }}" class="template-card">
                        <span class="template-icon">
                            <i class="fas {{ $card['icon'] }}"></i>
                        </span>
                        <span>
                            <h4 class="template-title">{{ $card['title'] }}</h4>
                            <p class="template-description">{{ $card['description'] }}</p>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <div id="signature-panel" class="letters-panel">
            <div class="signature-layout">
                <div class="signature-card">
                    <h4 class="section-heading">Upload Signature</h4>
                    <p class="section-note">Choose a letter type and upload a JPG, PNG, GIF, or WEBP signature image up to 2 MB.</p>

                    <form action="{{ route('letters.signature-master.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="letter_type">Letter Type</label>
                            <select class="form-control" id="letter_type" name="letter_type" required>
                                <option value="">Select letter type</option>
                                @foreach($letterTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('letter_type') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="signature_image">Signature Image</label>
                            <input class="form-control" id="signature_image" name="signature_image" type="file" accept="image/*" required>
                        </div>

                        <div class="signature-preview" id="signaturePreview">
                            <span>Signature preview will appear here</span>
                        </div>

                        <button type="submit" class="save-signature-btn">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Save Signature
                        </button>
                    </form>
                </div>

                <div class="signature-list">
                    <div class="signature-list-header">
                        <div>
                            <h4 class="section-heading">Current Signatures</h4>
                            <p class="section-note mb-0">Review which letter types are ready for signed output.</p>
                        </div>
                    </div>

                    <table class="signature-table">
                        <thead>
                            <tr>
                                <th>Letter Type</th>
                                <th>Preview</th>
                                <th>Status</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($letterTypes as $key => $label)
                                @php $signature = $signatures->get($key); @endphp
                                <tr>
                                    <td><strong>{{ $label }}</strong></td>
                                    <td>
                                        <div class="signature-thumb">
                                            @if($signature)
                                                <img src="{{ asset($signature->signature_path) }}" alt="{{ $label }} signature">
                                            @else
                                                <span class="empty-signature">No image</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($signature)
                                            <span class="status-pill ready">
                                                <i class="fas fa-check-circle"></i> Uploaded
                                            </span>
                                        @else
                                            <span class="status-pill missing">
                                                <i class="fas fa-exclamation-circle"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $signature ? \Carbon\Carbon::parse($signature->updated_at)->format('d M Y, h:i A') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tabs = document.querySelectorAll('.letters-tab');
        var panels = document.querySelectorAll('.letters-panel');
        var fileInput = document.getElementById('signature_image');
        var preview = document.getElementById('signaturePreview');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(function (item) {
                    item.classList.remove('active');
                });

                panels.forEach(function (panel) {
                    panel.classList.remove('active');
                });

                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });

        if (fileInput) {
            fileInput.addEventListener('change', function () {
                var file = fileInput.files[0];

                if (!file) {
                    preview.innerHTML = '<span>Signature preview will appear here</span>';
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (event) {
                    preview.innerHTML = '<img src="' + event.target.result + '" alt="Selected signature preview">';
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>
@endsection
