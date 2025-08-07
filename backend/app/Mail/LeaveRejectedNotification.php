<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveRejectedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveRequest;
    public $rejectionReason;
    public $approverName;

    public function __construct(LeaveRequest $leaveRequest, $rejectionReason, $approverName)
    {
        $this->leaveRequest = $leaveRequest;
        $this->rejectionReason = $rejectionReason;
        $this->approverName = $approverName ?: 'An Educator'; // Fallback if name is null
    }

    public function build()
    {
        return $this->subject('Your Leave Request Has Been Rejected')
                    ->markdown('emails.leave_rejected')
                    ->with([
                        'studentName' => $this->leaveRequest->student->name,
                        'leaveType' => $this->leaveRequest->leaveType->name,
                        'fromDate' => $this->leaveRequest->from_date,
                        'toDate' => $this->leaveRequest->to_date,
                        'rejectionReason' => $this->rejectionReason,
                        'approverName' => $this->approverName,
                    ]);
    }
}