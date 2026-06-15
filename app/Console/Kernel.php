<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // 🎯 MAIN AUTOMATION: Generate payslips on 1st of every month at 9:00 AM
        $schedule->command('payslips:auto-generate')
                 ->monthlyOn(1, '09:00')
                 ->timezone('Asia/Kolkata')
                 ->emailOutputOnFailure('admin@yourcompany.com')
                 ->description('Generate monthly payslips automatically')
                 ->runInBackground();

        // 🔄 RETRY MECHANISM: Retry failed generations daily at 10:00 AM
        $schedule->command('payslips:auto-generate --force')
                 ->dailyAt('10:00')
                 ->when(function () {
                     return \DB::table('monthly_payslips')
                         ->where('status', 'failed')
                         ->whereMonth('payroll_month', now()->subMonth()->month)
                         ->exists();
                 })
                 ->description('Retry failed payslip generations');

        // 📊 MONTHLY REPORT: Send summary report to admin on 2nd of every month
        $schedule->call(function () {
            $lastMonth = now()->subMonth();
            $stats = \DB::table('monthly_payslips')
                ->whereMonth('payroll_month', $lastMonth->month)
                ->whereYear('payroll_month', $lastMonth->year)
                ->selectRaw('
                    COUNT(*) as total_generated,
                    SUM(CASE WHEN email_sent = 1 THEN 1 ELSE 0 END) as emails_sent,
                    SUM(net_salary) as total_payout,
                    AVG(net_salary) as avg_salary,
                    SUM(overtime_amount) as total_overtime
                ')
                ->first();
  $schedule->command('reminder:amc-renewal')->dailyAt('09:00');
            // Log the summary
            \Log::info("Monthly Payroll Summary for {$lastMonth->format('F Y')}", [
                'total_generated' => $stats->total_generated,
                'emails_sent' => $stats->emails_sent,
                'total_payout' => $stats->total_payout,
                'avg_salary' => $stats->avg_salary,
                'total_overtime' => $stats->total_overtime
            ]);

        })->monthlyOn(2, '10:00')
          ->description('Generate monthly payroll summary');

        // 🧹 CLEANUP: Clean old PDF files (older than 2 years)
        $schedule->call(function () {
            $oldFiles = \DB::table('monthly_payslips')
                ->where('payroll_month', '<', now()->subYears(2))
                ->whereNotNull('pdf_path')
                ->pluck('pdf_path');

            foreach ($oldFiles as $filePath) {
                $fullPath = storage_path("app/public/{$filePath}");
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            \Log::info("Cleaned up " . count($oldFiles) . " old payslip files");
        })->yearly()
          ->description('Clean up old payslip files');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}