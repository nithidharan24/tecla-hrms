@component('mail::message')
@if($data['notification_type'] == 'upcoming_schedule')
# Upcoming Schedule Change Notification
@else
# Schedule Update Notification
@endif

Dear {{ $data['employee_name'] }},

@if($data['notification_type'] == 'upcoming_schedule')
This is to inform you about an upcoming change to your work schedule that will take effect after your current schedule ends.

**Current Schedule (Valid until {{ $data['current_schedule_end'] }}):**  
Department: {{ $data['current_schedule_details']['department'] ?? 'N/A' }}  
Shift: {{ $data['current_schedule_details']['shift_name'] }}  
Hours: {{ $data['current_schedule_details']['start_time'] }} - {{ $data['current_schedule_details']['end_time'] }}  

**Upcoming Schedule (Effective from {{ $data['schedule_start'] }}):**  
@else
Your current schedule has been modified with the following details:  
@endif

Department: {{ $data['department'] }}  
Shift: {{ $data['shift_name'] }}  
Hours: {{ $data['start_time'] }} - {{ $data['end_time'] }}  
Schedule Period: {{ $data['schedule_start'] }} to {{ $data['schedule_end'] }}  

@if(isset($data['is_hr_copy']))
This is a copy of the notification sent to {{ $data['employee_email'] }}.
@endif

**Updated by:** {{ $data['updated_by'] }}  
**Update date:** {{ $data['update_date'] }}  

If you have any questions or concerns about this schedule change, please contact your manager or HR department.

Thanks,  
{{ config('app.name') }}
@endcomponent