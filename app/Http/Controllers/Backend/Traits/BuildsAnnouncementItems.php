<?php

namespace App\Http\Controllers\Backend\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Announcement;

trait BuildsAnnouncementItems
{
    protected function buildAnnouncementItems(): Collection
    {
        $today = now();
        $items = collect();

        // ── 1. Birthdays today (stored in employee_profile_main.birthday) ──
        if (Schema::hasTable('employee_profile_main')) {
            DB::table('allemployees')
                ->leftJoin('employee_profile_main', 'allemployees.id', '=', 'employee_profile_main.employee_id')
                ->where('allemployees.deleted_at', 0)
                ->whereNotNull('employee_profile_main.birthday')
                ->whereRaw('DATE_FORMAT(employee_profile_main.birthday, "%m-%d") = ?', [$today->format('m-d')])
                ->select('allemployees.firstname', 'allemployees.lastname')
                ->distinct()
                ->get()
                ->each(fn($e) => $items->push([
                    'type' => 'Birthday',
                    'icon' => 'fa-cake-candles',
                    'text' => "Happy Birthday, {$e->firstname} {$e->lastname}!",
                ]));
        }

        // ── 2. Work anniversaries today (skip first year) ───────────────────
        DB::table('allemployees')
            ->where('deleted_at', 0)
            ->whereMonth('joiningdate', $today->month)
            ->whereDay('joiningdate', $today->day)
            ->whereYear('joiningdate', '<', $today->year)
            ->select('firstname', 'lastname', 'joiningdate')
            ->get()
            ->each(function ($e) use ($items, $today) {
                $years = $today->year - Carbon::parse($e->joiningdate)->year;
                $items->push([
                    'type' => 'Anniversary',
                    'icon' => 'fa-award',
                    'text' => "{$e->firstname} {$e->lastname} completes {$years} year" . ($years > 1 ? 's' : '') . " today!",
                ]);
            });

        // ── 3. Promotions today ─────────────────────────────────────────────
        if (Schema::hasTable('promotions') && Schema::hasTable('designation')) {
            DB::table('promotions as p')
                ->join('allemployees as ae', 'p.employee_id', '=', 'ae.employeeid')
                ->join('designation as td', 'p.promotion_to', '=', 'td.id')
                ->where('ae.deleted_at', 0)
                ->where(function ($q) use ($today) {
                    $q->whereDate('p.promotion_date', $today->format('Y-m-d'))
                      ->orWhereDate('p.created_at',   $today->format('Y-m-d'));
                })
                ->select('ae.firstname', 'ae.lastname', 'td.designation as to_designation')
                ->get()
                ->each(fn($p) => $items->push([
                    'type' => 'Promotion',
                    'icon' => 'fa-arrow-trend-up',
                    'text' => "{$p->firstname} {$p->lastname} promoted to {$p->to_designation}!",
                ]));
        }

        // ── 4. New employees added today ────────────────────────────────────
        DB::table('allemployees')
            ->where('deleted_at', 0)
            ->whereDate('created_at', $today->format('Y-m-d'))
            ->select('firstname', 'lastname')
            ->get()
            ->each(fn($e) => $items->push([
                'type' => 'New Joiner',
                'icon' => 'fa-user-plus',
                'text' => "Welcome {$e->firstname} {$e->lastname}! New team member joined today.",
            ]));

        // ── 5. Holidays in the next 3 days (incl. today) ───────────────────
        if (Schema::hasTable('holidays')) {
            DB::table('holidays')
                ->whereBetween('holidaydate', [
                    $today->format('Y-m-d'),
                    $today->copy()->addDays(3)->format('Y-m-d'),
                ])
                ->orderBy('holidaydate')
                ->select('name_holiday', 'holidaydate')
                ->get()
                ->each(function ($h) use ($items, $today) {
                    $date  = Carbon::parse($h->holidaydate);
                    $name  = $h->name_holiday ?? 'Holiday';
                    $label = $date->isToday()
                        ? 'Today is a holiday'
                        : 'Upcoming holiday on ' . $date->format('d M');
                    $items->push([
                        'type' => 'Holiday',
                        'icon' => 'fa-calendar-days',
                        'text' => "{$label}: {$name}",
                    ]);
                });
        }

        // ── 6. Manual announcements (active within 24 h) ───────────────────
        if (Schema::hasTable('announcements')) {
            Announcement::active()->latest()->get()
                ->each(fn($a) => $items->push([
                    'type' => $a->type,
                    'icon' => $a->icon,
                    'text' => $a->message,
                ]));
        }

        return $items;
    }
}
