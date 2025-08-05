@component('mail::message')
# Leave Request Rejected

Dear {{ $studentName }},

We regret to inform you that your leave request for **{{ $leaveType }}** from **{{ $fromDate }}** to **{{ $toDate }}** has been rejected by **{{ $approverName }}**.

**Reason for rejection**: {{ $rejectionReason }}

Please contact the education team if you have any questions.

Best regards,  
Education Team

@component('mail::button', ['url' => url('/dashboard')])
View Dashboard
@endcomponent
@endcomponent