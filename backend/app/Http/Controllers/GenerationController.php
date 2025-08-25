<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Generation;

class GenerationController extends Controller
{
    public function index()
    {
        return response()->json(Generation::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:generations,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $generation = Generation::create($request->all());

        return response()->json([
            'message' => 'Generation created successfully!',
            'generation' => $generation
        ], 201);
    }
    

    public function show($id)
    {
        return response()->json(Generation::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $generation = Generation::findOrFail($id);
        $generation->update($request->all());

        return response()->json([
            'message' => 'Generation updated successfully!',
            'generation' => $generation
        ]);
    }

    public function destroy($id)
    {
        Generation::findOrFail($id)->delete();

        return response()->json(['message' => 'Generation deleted successfully!']);
    }
}