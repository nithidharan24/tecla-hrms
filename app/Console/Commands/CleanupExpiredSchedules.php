<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupExpiredSchedules extends Command
{
    protected $signature = 'schedule:cleanup';
    protected $description = 'Clean up expired schedules automatically';

    public function handle()
    {
        $this->info('Starting schedule cleanup...');
        
        $cutoffDate = Carbon::now()->subDays(30);
        $hardDeleteDate = Carbon::now()->subYear();
        
        try {
            // Soft delete expired schedules older than 30 days
            $softDeleted = DB::table('schedule')
                ->where('schedule_end_date', '<', $cutoffDate)
                ->where('deleted_at', 0)
                ->update(['deleted_at' => now()]);

            // Hard delete very old schedules (older than 1 year)
            $hardDeleted = DB::table('schedule')
                ->where('schedule_end_date', '<', $hardDeleteDate)
                ->where('deleted_at', '!=', 0)
                ->delete();

            $this->info("Soft deleted {$softDeleted} expired schedules");
            $this->info("Hard deleted {$hardDeleted} old schedules");
            
            Log::info("Schedule cleanup completed: {$softDeleted} soft deleted, {$hardDeleted} hard deleted");
            
        } catch (\Exception $e) {
            $this->error("Failed to cleanup schedules: " . $e->getMessage());
            Log::error("Schedule cleanup failed: " . $e->getMessage());
        }
    }
}