<?php

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class LeaveRequestController extends Controller
{
    // Student - Show form
    public function create()
    {
        return view('leave_requests.create');
    }

    // Student - Store form data
    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|in:sick,family_emergency,personal,academic_event',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,jpg,png',
            'contact_info' => 'nullable|string',
        ]);

        $path = $request->file('document')?->store('documents', 'public');

        LeaveRequest::create([
            'user_id' => Auth::id(),
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'document_path' => $path,
            'contact_info' => $request->contact_info,
        ]);

        return redirect()->back()->with('success', 'Leave request submitted!');
    }

    // Educator - List pending
    public function index()
    {
        $pendingRequests = LeaveRequest::where('status', 'pending')->with('user')->get();
        return view('leave_requests.index', compact('pendingRequests'));
    }

    // Educator - View detail
    public function show($id)
    {
        $leaveRequest = LeaveRequest::with('user')->findOrFail($id);
        return view('leave_requests.show', compact('leaveRequest'));
    }
}
