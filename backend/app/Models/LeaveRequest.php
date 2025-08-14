<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'leave_type_id',
        'reason',
        'from_date',
        'to_date',
        'contact_info',
        'supporting_documents',
        'status',
    ];

    // Add this protected property
    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
    ];

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }


    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }


public function approver()
{
    return $this->belongsTo(User::class, 'approved_by');
}



}
