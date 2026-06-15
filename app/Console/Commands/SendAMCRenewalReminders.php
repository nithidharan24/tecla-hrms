<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AMCRenewalReminder; // Remove \Client from the import
use Carbon\Carbon;

class SendAMCRenewalReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:amc-renewal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send AMC renewal reminders based on individual client reminder days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        
        $clients = DB::table('clients')
            ->where('deleted_at', 0)
            ->whereNotNull('hosting_amc_renewal_date')
            ->whereNotNull('amc_reminder_days')
            ->get();

        $this->info("Found {$clients->count()} clients with AMC reminder settings.");

        $sentCount = 0;
        
        foreach ($clients as $client) {
            try {
                // Calculate the reminder date based on individual client setting
                $reminderDate = Carbon::parse($client->hosting_amc_renewal_date)
                    ->subDays($client->amc_reminder_days)
                    ->format('Y-m-d');
                
                // Check if today is the reminder date for this client
                if ($today === $reminderDate) {
                    
                    // Check if email was already sent today for this client
                    $alreadySent = DB::table('email_logs')
                        ->where('client_id', $client->id)
                        ->where('email_type', 'amc_renewal_reminder')
                        ->whereDate('sent_at', $today)
                        ->exists();
                    
                    if ($alreadySent) {
                        $this->info("AMC reminder already sent today to {$client->email}, skipping...");
                        continue;
                    }
                    
                    $daysLeft = Carbon::now()->diffInDays(Carbon::parse($client->hosting_amc_renewal_date));
                    
                    Mail::to($client->email)
                        ->send(new AMCRenewalReminder($client, $daysLeft, $client->hosting_amc_renewal_date));
                    
                    $sentCount++;
                    $this->info("AMC reminder sent to {$client->email} ({$client->amc_reminder_days} days before expiry)");
                    
                    // Log the email sent to prevent duplicates
                    DB::table('email_logs')->insert([
                        'client_id' => $client->id,
                        'email_type' => 'amc_renewal_reminder',
                        'sent_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
            } catch (\Exception $e) {
                $this->error("Failed to send email to {$client->email}: {$e->getMessage()}");
            }
        }

        $this->info("AMC renewal reminder process completed. Sent {$sentCount} reminders.");
    }
}