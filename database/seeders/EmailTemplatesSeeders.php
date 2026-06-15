<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('email_templates')->insertOrIgnore([
            [
                'name' => 'salary_hike_notification',
                'subject' => 'Congratulations on Your Salary Hike, {employee_name}!',
                // Changed ${old_salary} to {old_salary} and ${new_salary} to {new_salary}
                'body' => "Dear {employee_name},\n\nWe are pleased to inform you that your salary has been revised.\n\nYour previous salary was: {old_salary}\nYour new salary is: {new_salary}\n\nThis increase reflects your valuable contributions to the company. We appreciate your hard work and dedication.\n\nBest regards,\n[Your Company Name]",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
