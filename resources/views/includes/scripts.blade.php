<script src="{{ asset('admin/assets/js/bootstrap.bundle.js')}}"></script>
<script src="{{asset('admin/assets/js/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('admin/assets/js/app.js')}}"></script>
<script src="{{asset('admin/assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('admin/assets/js/dataTables.bootstrap4.min.js')}}"></script>
<!-- Feather Icon JS -->
<script src="{{asset('admin/assets/js/feather.min.js')}}"></script>

<!-- charts script -->
@if(request()->route()->getName() === 'dashboard')
<script data-cfasync="false" src="{{asset('admin/assets/js/email-decode.min.js')}}"></script>
<!-- morris js -->
<script src="{{asset('admin/assets/plugins/morris/morris.min.js')}}"></script>
<script src="{{asset('admin/assets/plugins/raphael/raphael.min.js')}}"></script> 
<!-- chart js -->
<script src="{{asset('admin/assets/js/chart.js')}}"></script>
<script src="{{asset('admin/assets/js/greedynav.js')}}"></script>
@endif

<!-- Datetimepicker JS -->
<script src="{{asset('admin/assets/js/moment.min.js')}}"></script>
<script src="{{asset('admin/assets/js/bootstrap-datetimepicker.min.js')}}"></script>

<!-- Theme Settings JS -->
<script src="{{asset('admin/assets/js/theme-settings.js')}}"></script>
{{-- <script src="{{asset('admin/assets/js/layout.js')}}"></script> --}}