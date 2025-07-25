<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

protected $fillable = [
    'student_id',
    'user_id',
    'reason',
    'contact_info',
    'supporting_documents',
    'from_date',
    'to_date',
    'status',
    'leave_type_id'
];


    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
    public function educator()
    {
        return $this->belongsTo(Educator::class);
    }
}
