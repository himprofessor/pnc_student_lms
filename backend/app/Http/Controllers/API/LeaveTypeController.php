<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller; // <--- CHANGE THIS LINE
use App\Models\LeaveType;
use Illuminate\Http\Request; // Add this if you intend to use Request object later, though not strictly needed for index()


class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::select('id', 'name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $leaveTypes
        ]);
    }
}