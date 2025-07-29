<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StudentClass; // Assuming you have a StudentClass model
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        try {
            $classes = StudentClass::all(); // Fetch all classes

            return response()->json($classes, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve classes: ' . $e->getMessage()], 500);
        }
    }
}