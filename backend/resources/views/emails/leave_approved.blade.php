@component('mail::message')
# Leave Request Approved

Dear {{ $studentName }},

Your leave request for **{{ $leaveType }}** from **{{ $fromDate }}** to **{{ $toDate }}** has been approved by **{{ $approverName }}**.

Thank you for your submission.

Best regards,  
Education Team

@component('mail::button', ['url' => url('/dashboard')])
View Dashboard
@endcomponent
@endcomponent