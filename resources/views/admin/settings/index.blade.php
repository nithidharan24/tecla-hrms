@extends('layouts.index')

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section">
          <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Settings</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
          </div>

          @if (Session::has('messageType') && Session::has('message'))
              @if (Session::get('messageType') === 'success')
                  <div class="alert alert-success" id="success-alert">
                      {{ Session::get('message') }}
                  </div>
              @elseif (Session::get('messageType') === 'error')
                  <div class="alert alert-danger" id="error-alert">
                      {{ Session::get('message') }}
                  </div>
              @endif
          @endif

          <div class="section-body">
            <div class="row">
              <div class="col-12">
                <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <!-- Sidebar Tabs (Desktop) -->
                        <div class="col-lg-2 col-md-3 mb-3">
                          <div class="list-group d-none d-md-block" id="list-tab" role="tablist">
                            <a class="list-group-item list-group-item-action active" id="list-home-list" data-bs-toggle="list" href="#list-home" role="tab">General Setting</a>
                            <a class="list-group-item list-group-item-action" id="list-email-list" data-bs-toggle="list" href="#list-email" role="tab">Email Configuration</a>
                            <a class="list-group-item list-group-item-action" id="list-logo-list" data-bs-toggle="list" href="#list-logo" role="tab">Logo and Favicon</a>
                          </div>

                          <!-- Horizontal Nav (Mobile) -->
                          <ul class="nav nav-pills nav-fill d-md-none mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                              <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" href="#list-home" role="tab">General</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" id="pills-email-tab" data-bs-toggle="pill" href="#list-email" role="tab">Email</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" id="pills-logo-tab" data-bs-toggle="pill" href="#list-logo" role="tab">Logo</a>
                            </li>
                          </ul>
                        </div>

                        <!-- Tab Content -->
                        <div class="col-lg-10 col-md-9">
                          <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="list-home" role="tabpanel">
                                @include('admin.settings.general-setting')
                            </div>
                            <div class="tab-pane fade" id="list-email" role="tabpanel">
                                @include('admin.settings.email-setting')
                            </div>
                            <div class="tab-pane fade" id="list-logo" role="tabpanel">
                                @include('admin.settings.logo-setting')
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
  // Automatically close the success message after 1 second
  setTimeout(function() {
      $("#success-alert").fadeTo(500, 0).slideUp(500, function(){
          $(this).remove();
      });
  }, 1000);

  // Automatically close the error message after 3 seconds
  setTimeout(function() {
      $("#error-alert").fadeTo(500, 0).slideUp(500, function(){
          $(this).remove();
      });
  }, 3000);

  $(document).ready(function() {
    // Handle tab switching for both sidebar and mobile nav
    $('#list-tab a, #pills-tab a').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
  });
</script>
@endpush