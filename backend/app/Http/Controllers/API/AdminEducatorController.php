<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Educator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminEducatorController extends Controller
{
    public function index()
    {
        return response()->json(Educator::all());
    }

  public function store(Request $request)
{
    // Validate incoming request
    $validated = $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => [
            'required', 'string', 'email',
            'unique:educators,email',
            function ($attribute, $value, $fail) {
                if (!str_ends_with($value, '@passerellesnumeriques.org')) {
                    $fail('Educator email must end with @passerellesnumeriques.org');
                }
            }
        ],
        'password'  => 'required|string|min:6',
        'class_ids' => 'required|array',
        'class_ids.*' => 'exists:student_classes,id', // Ensure class IDs exist
    ]);

    try {
        // Start a database transaction
        DB::transaction(function () use ($validated) {
            // Create educator
            $educator = Educator::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Sync classes
            $educator->classes()->sync($validated['class_ids']);
        });

        return response()->json(['message' => 'Educator created successfully.'], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to create educator: ' . $e->getMessage()], 500);
    }
}
    public function show($id)
    {
        $educator = Educator::findOrFail($id);
        return response()->json($educator->load('classes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'      => 'sometimes|required|string|max:255',
            'email'     => [
                'sometimes', 'required', 'string', 'email',
                'unique:educators,email,' . $id,
                function ($attribute, $value, $fail) {
                    if (!str_ends_with($value, '@passerellesnumeriques.org')) {
                        $fail('Educator email must end with @passerellesnumeriques.org');
                    }
                }
            ],
            'password'  => 'sometimes|required|string|min:6',
            'class_ids' => 'sometimes|required|array'
        ]);

        $educator = Educator::findOrFail($id);

        DB::transaction(function () use ($validated, $educator) {
            if (isset($validated['name'])) {
                $educator->name = $validated['name'];
            }

            if (isset($validated['email'])) {
                $educator->email = $validated['email'];
            }

            if (isset($validated['password'])) {
                $educator->password = Hash::make($validated['password']);
            }

            $educator->save();

            if (isset($validated['class_ids'])) {
                $educator->classes()->sync($validated['class_ids']);
            }
        });

        return response()->json([
            'message' => 'Educator updated successfully.',
            'educator' => $educator->load('classes')
        ]);
    }

    public function destroy($id)
    {
        $educator = Educator::findOrFail($id);
        $educator->delete();
        return response()->json(['message' => 'Educator deleted successfully.']);
    }
}