<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminStudentController extends Controller
{
    // List all students with their classes and roles
    public function index()
    {
        $students = User::where('role_id', 3)->with(['studentClasses', 'role'])->get();
        return response()->json($students);
    }

    // Create a new student account
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => [
                'required', 'string', 'email', 'unique:users,email',
                function ($attribute, $value, $fail) {
                    if (!str_ends_with($value, '@student.passerellesnumeriques.org')) {
                        $fail('Student email must end with @student.passerellesnumeriques.org');
                    }
                }
            ],
            'password'  => 'required|string|min:6',
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:student_classes,id',
        ]);

        try {
            $student = DB::transaction(function () use ($validated) {
                $user = User::create([
                    'name'     => $validated['name'],
                    'email'    => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role_id'  => 3, // student role id
                ]);

                $user->studentClasses()->sync($validated['class_ids']);

                return $user;
            });

            return response()->json([
                'message' => 'Student created successfully.',
                'student' => $student->load(['studentClasses', 'role']),
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create student: ' . $e->getMessage()], 500);
        }
    }

    // Show specific student info
    public function show($id)
    {
        $student = User::where('role_id', 3)->with(['studentClasses', 'role'])->findOrFail($id);
        return response()->json($student);
    }

    // Update student info and classes
    public function update(Request $request, $id)
    {
        $student = User::where('role_id', 3)->findOrFail($id);

        $validated = $request->validate([
            'name'      => 'sometimes|required|string|max:255',
            'email'     => [
                'sometimes', 'required', 'string', 'email',
                'unique:users,email,' . $student->id,
                function ($attribute, $value, $fail) {
                    if (!str_ends_with($value, '@student.passerellesnumeriques.org')) {
                        $fail('Student email must end with @student.passerellesnumeriques.org');
                    }
                }
            ],
            'password'  => 'sometimes|required|string|min:6',
            'class_ids' => 'sometimes|required|array',
            'class_ids.*' => 'exists:student_classes,id',
        ]);

        try {
            DB::transaction(function () use ($validated, $student) {
                if (isset($validated['name'])) {
                    $student->name = $validated['name'];
                }
                if (isset($validated['email'])) {
                    $student->email = $validated['email'];
                }
                if (isset($validated['password'])) {
                    $student->password = Hash::make($validated['password']);
                }
                $student->save();

                if (isset($validated['class_ids'])) {
                    $student->studentClasses()->sync($validated['class_ids']);
                }
            });

            return response()->json([
                'message' => 'Student updated successfully.',
                'student' => $student->load(['studentClasses', 'role']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update student: ' . $e->getMessage()], 500);
        }
    }

    // Delete student account
    public function destroy($id)
    {
        $student = User::where('role_id', 3)->findOrFail($id);

        try {
            DB::transaction(function () use ($student) {
                $student->studentClasses()->detach();
                $student->delete();
            });

            return response()->json(['message' => 'Student deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete student: ' . $e->getMessage()], 500);
        }
    }
}
