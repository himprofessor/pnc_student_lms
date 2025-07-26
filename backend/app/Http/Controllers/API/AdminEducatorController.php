<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Educator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminEducatorController extends Controller
{
    public function index()
    {
        return response()->json(Educator::with('classes')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => [
                'required', 'string', 'email',
                'unique:users,email',  // Check unique in users table
                function ($attribute, $value, $fail) {
                    if (!str_ends_with($value, '@passerellesnumeriques.org')) {
                        $fail('Educator email must end with @passerellesnumeriques.org');
                    }
                }
            ],
            'password'  => 'required|string|min:6',
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:student_classes,id',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // 1. Create User (for authentication)
                $user = User::create([
                    'name'     => $validated['name'],
                    'email'    => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role_id'  => 2,  // Assuming 2 = Educator role
                ]);

                // 2. Create Educator linked to the User
                $educator = Educator::create([
                    'user_id' => $user->id,
                    'name'    => $validated['name'],   // optional if your Educator has name field
                    'email'   => $validated['email'],  // optional but consistent
                    'password' => $user->password,     // hashed password (optional)
                ]);

                // 3. Attach classes to educator
                $educator->classes()->sync($validated['class_ids']);
            });

            return response()->json(['message' => 'Educator created successfully.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create educator: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $educator = Educator::with('classes')->findOrFail($id);
        return response()->json($educator);
    }

public function update(Request $request, $userId)
{
    try {
        $validated = $request->validate([
            'name'      => 'sometimes|required|string|max:255',
            'email'     => [
                'sometimes', 'required', 'string', 'email',
                'unique:users,email,' . $userId,
                function ($attribute, $value, $fail) {
                    if (!str_ends_with($value, '@passerellesnumeriques.org')) {
                        $fail('Educator email must end with @passerellesnumeriques.org');
                    }
                }
            ],
            'password'  => 'sometimes|required|string|min:6',
            'class_ids' => 'sometimes|required|array'
        ]);

        $educator = Educator::where('user_id', $userId)->firstOrFail();
        $user = User::findOrFail($userId);

        DB::transaction(function () use ($validated, $educator, $user) {
            if (isset($validated['name'])) {
                $educator->name = $validated['name'];
                $user->name = $validated['name'];
            }

            if (isset($validated['email'])) {
                $educator->email = $validated['email'];
                $user->email = $validated['email'];
            }

            if (isset($validated['password'])) {
                $hashed = Hash::make($validated['password']);
                $educator->password = $hashed;
                $user->password = $hashed;
            }

            $educator->save();
            $user->save();

            if (isset($validated['class_ids'])) {
                $educator->classes()->sync($validated['class_ids']);
            }
        });

        return response()->json([
            'message' => 'Educator updated successfully.',
            'educator' => $educator->load('classes'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to update educator: ' . $e->getMessage()
        ], 500);
    }
}



  public function destroy($userId)
{
    $educator = Educator::where('user_id', $userId)->firstOrFail();
    $user = User::findOrFail($userId);

    DB::transaction(function () use ($educator, $user) {
        $educator->delete();
        $user->delete();
    });

    return response()->json(['message' => 'Educator and user account deleted successfully.']);
}



}
