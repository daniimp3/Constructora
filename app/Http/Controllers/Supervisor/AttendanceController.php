<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date');
        
        $query = Attendance::with('user');
        
        if ($date) {
            $query->whereDate('date', $date);
        }
        
        $attendances = $query->get();
        
        return response()->json($attendances);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late',
            'notes' => 'nullable|string'
        ]);

        $attendance = Attendance::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Asistencia registrada',
            'attendance' => $attendance
        ]);
    }
}