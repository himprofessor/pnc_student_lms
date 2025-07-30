<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function viewLeaveRequests()
    {
        $leaveRequests = LeaveRequest::with('student')->where('status', 'pending')->get();
        return response()->json($leaveRequests);
    }

    public function approve($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->status = 'approved';
        $leave->save();

        return response()->json(['message' => 'Leave approved']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string'
        ]);

        $leave = LeaveRequest::findOrFail($id);
        $leave->status = 'rejected';
        $leave->comment = $request->comment;
        $leave->save();

        return response()->json(['message' => 'Leave rejected with comment']);
    }

    public function studentsList()
    {
        $students = User::whereHas('role', function ($q) {
            $q->where('name', 'student');
        })->get();

        return response()->json($students);
    }

    public function getAuthenticatedTeacher(Request $request)
{
    $user = Auth::user();

    if (!$user || ($user->role && $user->role->name !== 'teacher')) {
        return response()->json(['error' => 'Unauthorized or not a teacher'], 403);
    }

    return response()->json([
        'id'    => $user->id,
        'name'  => $user->name,
        'email' => $user->email,
        'role'  => $user->role->name ?? null,
    ]);
}

    // -------------------------------
    // 🆕 CRUD for Teacher
    // -------------------------------

    // List all teachers
    public function index()
    {
        $teachers = User::whereHas('role', function ($q) {
            $q->where('name', 'teacher');
        })->get();

        return response()->json($teachers);
    }

    // Create a new teacher
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $teacher = new User();
        $teacher->name = $validated['name'];
        $teacher->email = $validated['email'];
        $teacher->password = bcrypt($validated['password']);
        $teacher->role_id = 2; // 👈 make sure this is the correct role_id for teacher
        $teacher->save();

        return response()->json(['message' => 'Teacher created successfully', 'teacher' => $teacher]);
    }

    // Show a single teacher
    public function show($id)
    {
        $teacher = User::findOrFail($id);

        if (!$teacher->role || $teacher->role->name !== 'teacher') {
            return response()->json(['error' => 'User is not a teacher'], 404);
        }

        return response()->json($teacher);
    }

    // Update teacher
    public function update(Request $request, $id)
    {
        $teacher = User::findOrFail($id);

        if (!$teacher->role || $teacher->role->name !== 'teacher') {
            return response()->json(['error' => 'User is not a teacher'], 404);
        }

        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $teacher->id,
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($validated['name'])) $teacher->name = $validated['name'];
        if (isset($validated['email'])) $teacher->email = $validated['email'];
        if (isset($validated['password'])) $teacher->password = bcrypt($validated['password']);

        $teacher->save();

        return response()->json(['message' => 'Teacher updated successfully', 'teacher' => $teacher]);
    }

    // Delete teacher
    public function destroy($id)
    {
        $teacher = User::findOrFail($id);

        if (!$teacher->role || $teacher->role->name !== 'teacher') {
            return response()->json(['error' => 'User is not a teacher'], 404);
        }

        $teacher->delete();

        return response()->json(['message' => 'Teacher deleted successfully']);
    }
}
