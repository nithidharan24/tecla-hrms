<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">
    
    {{-- Head --}}
    @include('super_admin.includes.head')
	
    <body>
		<!-- Main Wrapper -->
        <div class="main-wrapper">
		
			<!-- Header -->
            @include('super_admin.includes.header')
			<!-- /Header -->
			
			<!-- Sidebar -->
			@include('super_admin.includes.sidebar')
			<!-- /Sidebar -->

			<!-- Page Wrapper -->
            <div class="page-wrapper">

				{{-- Content --}}
				@yield('content')
				{{-- /Content --}}

				{{-- Footer --}}
				@include('super_admin.includes.footer')

            </div>
			<!-- /Page Wrapper -->
   
        </div>
		<!-- /Main Wrapper -->
		
        @include('super_admin.includes.scripts')
    </body>

</html>
