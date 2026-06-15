@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Family Information </h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Employee List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Family Information</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Family Information</h4>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        
                        <form action="{{ route('family.update', $employee->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div id="family_members_container">
                                @foreach($familyInfo as $member)
                                <div class="family-member mb-3" data-id="{{ $member->id }}">
                                    <h4>Family Member
                                        <a href="javascript:void(0);" class="text-danger remove-family-member d-none"><i class="fa fa-trash"></i></a>
                                    </h4>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="col-form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" name="family_members[{{ $loop->index }}][name]" class="form-control" value="{{ $member->name }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="col-form-label">Relationship <span class="text-danger">*</span></label>
                                            <input type="text" name="family_members[{{ $loop->index }}][relationship]" class="form-control" value="{{ $member->relationship }}" required>
                                        </div>
                                      
                                        <div class="col-md-6 mb-3">
                                            <label class="col-form-label">Phone</label>
                                            <input type="text" name="family_members[{{ $loop->index }}][phone]" class="form-control" value="{{ $member->phone }}">
                                        </div>
                                        <input type="hidden" name="family_members[{{ $loop->index }}][id]" value="{{ $member->id }}">
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <button type="button" id="add_family_member" class="btn btn-primary">Add Family Member</button>
                            <button type="submit" class="btn btn-success">Update Family Information</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

<script>
    // Function to dynamically add new family member form block
    document.getElementById('add_family_member').onclick = function() {
        const container = document.getElementById('family_members_container');
        const index = container.children.length;

        const newMember = `
            <div class="family-member mb-3">
                <h4>Family Member
                    <a href="javascript:void(0);" class="text-danger remove-family-member"><i class="fa fa-trash"></i></a>
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="family_members[${index}][name]" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Relationship <span class="text-danger">*</span></label>
                        <input type="text" name="family_members[${index}][relationship]" class="form-control" required>
                    </div>
                
                    <div class="col-md-6 mb-3">
                        <label class="col-form-label">Phone</label>
                        <input type="text" name="family_members[${index}][phone]" class="form-control">
                    </div>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', newMember);
        attachRemoveHandlers();  // Attach delete handlers to the new family member
    };

    // Function to attach event listeners for delete buttons
    function attachRemoveHandlers() {
        const deleteButtons = document.querySelectorAll('.remove-family-member');
        deleteButtons.forEach(button => {
            button.onclick = function() {
                this.closest('.family-member').remove(); // Remove the closest family-member div
            };
        });
    }

    // Attach event handlers for newly added family members only
    attachRemoveHandlers();
</script>
@endsection
