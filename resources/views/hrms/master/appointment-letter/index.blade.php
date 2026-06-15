@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Appointment Letter Templates</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item active">Appointment Letters</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('appointment-letter.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Template</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Templates List</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Template Name</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $template)
                                <tr>
                                    <td><strong>{{ $template->name }}</strong></td>
                                    <td>{{ $template->created_at->format('d M Y, h:i A') }}</td>
                                    <td>{{ $template->updated_at->format('d M Y, h:i A') }}</td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('appointment-letter.show', $template->id) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('appointment-letter.edit', $template->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a href="{{ route('appointment-letter.preview', $template->id) }}" class="btn btn-sm btn-success" target="_blank" title="Preview">
                                                <i class="fa fa-external-link"></i>
                                            </a>
                                            <form action="{{ route('appointment-letter.destroy', $template->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <p class="text-muted">No templates found. <a href="{{ route('appointment-letter.create') }}">Create one now</a></p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
