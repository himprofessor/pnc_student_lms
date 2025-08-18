<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveApprovedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveRequest;
    public $approverName;

    public function __construct(LeaveRequest $leaveRequest, $approverName)
    {
        $this->leaveRequest = $leaveRequest;
        $this->approverName = $approverName ?: 'An Educator'; // Fallback if name is null
    }

    public function build()
    {
        return $this->subject('Your Leave Request Has Been Approved')
                    ->markdown('emails.leave_approved')
                    ->with([
                        'studentName' => $this->leaveRequest->student->name,
                        'leaveType' => $this->leaveRequest->leaveType->name,
                        'fromDate' => $this->leaveRequest->from_date,
                        'toDate' => $this->leaveRequest->to_date,
                        'approverName' => $this->approverName,
                    ]);
    }
};