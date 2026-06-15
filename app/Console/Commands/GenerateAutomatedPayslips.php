<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Backend\Hr\AutomatedPayslipController;
use Carbon\Carbon;

class GenerateAutomatedPayslips extends Command
{
    protected $signature = 'payslips:auto-generate {--month=} {--year=} {--force}';
    protected $description = 'Generate automated monthly payslips using existing salary structure';

    public function handle()
    {
        $month = $this->option('month') ?? date('m', strtotime('last month'));
        $year = $this->option('year') ?? date('Y', strtotime('last month'));
        $force = $this->option('force');

        $this->info("🚀 Starting automated payslip generation for {$month}/{$year}...");

        // Check if it's the right time to generate
        $today = Carbon::now();
        $payrollMonth = Carbon::create($year, $month, 1);
        
        if (!$force && $today->lt($payrollMonth->endOfMonth())) {
            $this->error("❌ Cannot generate payslips before month end. Use --force to override.");
            return 1;
        }

        try {
            $payslipController = new AutomatedPayslipController();
            $result = $payslipController->generateMonthlyPayslips($month, $year);

            if ($result['success']) {
                $this->info("✅ " . $result['message']);
                
                if (!empty($result['errors'])) {
                    $this->warn("⚠️  Some errors encountered:");
                    foreach ($result['errors'] as $error) {
                        $this->error("   • " . $error);
                    }
                }
                
                $this->info("📧 All payslips have been emailed to employees automatically!");
                return 0;
            } else {
                $this->error("❌ Failed to generate payslips");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}