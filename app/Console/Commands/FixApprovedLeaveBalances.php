<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixApprovedLeaveBalances extends Command
{
    protected $signature   = 'leaves:fix-balances {--dry-run : Preview without making changes}';
    protected $description = 'Backfill paid_days and deduct balances for approved leaves where paid_days=0';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        // Fetch approved leaves that were never deducted (paid_days=0 AND lop_days=0)
        $leaves = DB::table('employee_leaves')
            ->where('status', 'approved')
            ->where('paid_days', 0)
            ->where('lop_days', 0)
            ->get();

        $this->info("Found {$leaves->count()} approved leaves with no balance deduction.");

        if ($leaves->isEmpty()) {
            $this->info('Nothing to fix.');
            return 0;
        }

        $fixed  = 0;
        $skipped = 0;

        foreach ($leaves as $leave) {
            $days = (int) $leave->no_of_days;

            if ($days <= 0) {
                $this->warn("  Skip leave id:{$leave->id} — no_of_days={$days}");
                $skipped++;
                continue;
            }

            $bal = DB::table('employee_leave_balances')
                ->where('employee_id', $leave->employee_id)
                ->where('leave_type', $leave->leave_type)
                ->first();

            if (!$bal) {
                $this->warn("  Skip leave id:{$leave->id} emp:{$leave->employee_id} type:{$leave->leave_type} — no balance row");
                $skipped++;
                continue;
            }

            $this->line("  Fix leave id:{$leave->id} emp:{$leave->employee_id} type:{$leave->leave_type} days:{$days}"
                . "  [used:{$bal->used_days} remaining:{$bal->remaining_days}]");

            if (!$dryRun) {
                DB::table('employee_leave_balances')
                    ->where('employee_id', $leave->employee_id)
                    ->where('leave_type', $leave->leave_type)
                    ->update([
                        'used_days'      => DB::raw("used_days + {$days}"),
                        'remaining_days' => DB::raw("GREATEST(0, remaining_days - {$days})"),
                        'updated_at'     => now(),
                    ]);

                DB::table('employee_leaves')
                    ->where('id', $leave->id)
                    ->update(['paid_days' => $days, 'lop_days' => 0]);
            }

            $fixed++;
        }

        $this->info($dryRun
            ? "Dry-run complete. Would fix {$fixed}, skip {$skipped}."
            : "Done. Fixed {$fixed}, skipped {$skipped}."
        );

        // Show verification for employee_id from first fixed leave
        if (!$dryRun && $fixed > 0) {
            $sample = $leaves->first();
            $bal = DB::table('employee_leave_balances')
                ->where('employee_id', $sample->employee_id)
                ->get();

            $this->info("Balance after fix for emp:{$sample->employee_id}:");
            foreach ($bal as $b) {
                $this->line("  type:{$b->leave_type} used:{$b->used_days} remaining:{$b->remaining_days}");
            }
        }

        return 0;
    }
}
