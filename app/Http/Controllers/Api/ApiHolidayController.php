<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiHolidayController extends Controller
{
    /**
     * Get holidays for the app.
     */
    public function index(Request $request)
    {
        $request->validate([
            'year' => 'nullable|integer|min:1900|max:2100',
            'month' => 'nullable|integer|min:1|max:12',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'upcoming' => 'nullable|boolean',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $today = Carbon::today('Asia/Kolkata')->format('Y-m-d');
        $limit = (int) $request->input('limit', 50);

        $query = DB::table('holidays');

        if ($request->boolean('upcoming')) {
            $query->whereDate('holidaydate', '>=', $today);
        }

        if ($request->filled('year')) {
            $query->whereYear('holidaydate', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('holidaydate', $request->month);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('holidaydate', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('holidaydate', '<=', $request->date_to);
        }

        $holidays = $query
            ->orderBy('holidaydate', 'asc')
            ->limit($limit)
            ->get()
            ->map(function ($holiday) use ($today) {
                $date = $holiday->holidaydate ?? $holiday->date_holiday ?? null;
                $carbonDate = $date ? Carbon::parse($date) : null;

                return [
                    'id' => $holiday->id,
                    'title' => $holiday->title ?? $holiday->name_holiday ?? null,
                    'date' => $carbonDate ? $carbonDate->format('Y-m-d') : null,
                    'formatted_date' => $carbonDate ? $carbonDate->format('d M Y') : null,
                    'day' => $holiday->day ?? ($carbonDate ? $carbonDate->format('l') : null),
                    'is_today' => $carbonDate ? $carbonDate->format('Y-m-d') === $today : false,
                    'branch_id' => $holiday->branch_id ?? null,
                ];
            });

        $todayHoliday = DB::table('holidays')
            ->whereDate('holidaydate', $today)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'today' => [
                    'date' => $today,
                    'is_holiday' => (bool) $todayHoliday,
                    'holiday' => $todayHoliday ? [
                        'id' => $todayHoliday->id,
                        'title' => $todayHoliday->title ?? $todayHoliday->name_holiday ?? null,
                        'date' => Carbon::parse($todayHoliday->holidaydate)->format('Y-m-d'),
                        'day' => $todayHoliday->day ?? Carbon::parse($todayHoliday->holidaydate)->format('l'),
                    ] : null,
                ],
                'filters' => [
                    'year' => $request->year,
                    'month' => $request->month,
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'upcoming' => $request->boolean('upcoming'),
                    'limit' => $limit,
                ],
                'holidays' => $holidays,
            ]
        ], 200);
    }
}
