@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Clients</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Clients</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{route('client.create')}}" class="btn add-btn"> Add Client</a>
                <div class="view-icons">
                    <a href="{{route('clientlist')}}" class="grid-view btn btn-link"><i class="fa fa-th"></i></a>
                    <a href="{{route('client.index')}}" class="list-view btn btn-link active"><i class="fa-solid fa-bars"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    
    <!-- Search Filter -->
    <div class="row filter-row">
        <div class="col-sm-6 col-md-3">  
            <div class="input-block mb-3 form-focus">
                <input type="text" id="client_Id" class="form-control floating">
                <label class="focus-label">Client ID</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">  
            <div class="input-block mb-3 form-focus">
                <input type="text" id="client_Name" class="form-control floating">
                <label class="focus-label">Client Name</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3"> 
            <div class="input-block mb-3 form-focus select-focus">
                <select id="company_Name" class="select floating"> 
                    <option value="">Select Company</option>
                    @foreach ($company as $id => $name)
                        <option value="{{ $name }}">{{ $name }}</option>
                    @endforeach
                </select>
                <label class="focus-label">Company</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">  
            <div class="d-grid">
                <a class="btn btn-success" id="search_Button"> Search </a>  
            </div>
        </div>     
    </div>
    <!-- Search Filter -->

    <div class="row staff-grid-row">
        @foreach ($clients as $cs)
        <div id="client-row-{{$cs->client_id}}" class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3 d-flex" data-company="{{ strtolower($cs->company_name) }}" data-client-id="{{ $cs->client_id }}" data-client-name="{{ strtolower($cs->first_name.' '.$cs->last_name) }}">
            <div class="profile-widget w-100">
                <div class="profile-img">
                    <a href="{{ route('clientprofile', $cs->client_id) }}" class="avatar"><img src="{{ asset('admin/assets/img/user.jpg') }}" alt="User Image"></a>
                </div>
                <div class="dropdown profile-action">
                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="{{ route('client.edit', $cs->client_id) }}"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                        <button class="dropdown-item" onclick="deleteclient('{{ $cs->client_id }}')"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</button>
                    </div>
                </div>
                <h4 class="user-name-cname m-t-10 mb-0"><a href="{{ route('clientprofile', $cs->client_id) }}" class="text-dark">{{ $cs->company_name }}</a></h4>
                <h5 class="user-name-name m-t-10 mb-0"><a href="{{ route('clientprofile', $cs->client_id) }}" class="text-dark">{{ ucFirst($cs->first_name.' '.$cs->last_name) }}</a></h5>
                <h6 class="user-name-id small text-muted">{{$cs->client_id}}</h6>
                <a href="{{ route('clientprofile', $cs->client_id) }}" class="btn btn-white btn-sm m-t-10">View Profile</a>
            </div>
        </div>
        @endforeach
    </div>
    
</div>
<!-- /Page Content -->

<script>
    function deleteclient(id) {
            Swal.fire({
                title: "Are you sure you want to delete this item?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                allowOutsideClick: false,
            }).then(function(result) {
                if (result.isConfirmed) {
                    var url = "{{ route('client.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "client has been deleted.",
                                icon: "success"
                            }).then(() => {
                                // Remove the table row
                                $('#client-row-' + id).remove();
                            });
                        },
                        error: function (error) {
                            console.error("Error deleting client:", error);
                            Swal.fire({
                                title: "Error",
                                text: "Failed to delete client.",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        }
</script>

@if (session('message'))
    <script>
        Swal.fire({
            icon: '{{ session("messageType") }}', // 'success' or other message types
            title: '{{ session("messageType") }}',
            text: '{{ session("message") }}', // The message text
            timer: 2500
        });
    </script>
@endif

@endsection