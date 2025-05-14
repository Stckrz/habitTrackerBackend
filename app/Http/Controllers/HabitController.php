<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\HabitResource;
use Carbon\Carbon;

class HabitController extends Controller
{

    // public function __construct()
    // {
        // Only store, update and destroy require an authenticated Sanctum token
        // $this->authorizeResource(Habit::class, 'habit');
    // }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Fetch only the habits belonging to the authenticated user
        $habits = Habit::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Return a collection of HabitResource
        return response()->json([
            'data' => HabitResource::collection($habits),
        ]);
    }

    /**
     * Store a newly created resource in DB.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly',
            'interval' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'goal_count' => 'nullable|integer|min:1',
        ]);

        $validated['user_id'] = $request->user()->id;

        $habit = Habit::create($validated);

        return response()->json([
            'message' => 'Habit Created Successfully',
            'data'    => new HabitResource($habit),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Habit $habit): JsonResponse
    {
        // $this->authorize('view', $habit);

        return response()->json([
            'data'    => new HabitResource($habit),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Habit $habit): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly',
            'interval' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'goal_count' => 'nullable|integer|min:1',
        ]);
        $habit->update($validated);
        return response()->json([
            'message' => 'Habit Edited Successfully',
            'data'    => new HabitResource($habit),
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Habit $habit): JsonResponse
    {
        $habit->delete();
        return response()->json([
            'message' => "Habit Deleted Successfully",
        ]);
    }

    //This is the function for marking a habit done for the current day.
    //Creates a new habitLog in the habitLog table
    public function markDone(Habit $habit, Request $request)
    {
        $this->authorize('update', $habit);
        $log = $habit->logs()->updateOrCreate(
            ['performed_at' => today()],
            ['count' => 1]
        );
        return response()->json($log, 201);
    }

    //checks whether or not this habit has been completed today, and returns the latest log.
    public function status(Habit $habit): JsonResponse
    {
        $doneToday = $habit->logs()
            ->whereDate('performed_at', Carbon::today())
            ->exists();

        return response()->json([
            'done_today' => $doneToday,
            'latest_log' => $habit->latestLog,
        ]);
    }

    //This returns habit logs for a given period.
    public function logsInPeriod(Habit $habit, Request $request): JsonResponse
    {
        $period = $request->query('period', 'week'); // or 'month', 'custom'

        $start = match ($period) {
            'week'  => Carbon::today()->startOfWeek(),
            'month' => Carbon::today()->startOfMonth(),
            default => Carbon::parse($request->query('start')),
        };
        $end = match ($period) {
            'week'  => Carbon::today()->endOfWeek(),
            'month' => Carbon::today()->endOfMonth(),
            default => Carbon::parse($request->query('end')),
        };

        $logs = $habit->logs()
            ->whereBetween('performed_at', [$start, $end])
            ->orderBy('performed_at')
            ->get();

        return response()->json([
            'period' => $period,
            'start'  => $start->toDateString(),
            'end'    => $end->toDateString(),
            'logs'   => $logs,
        ]);
    }
}
