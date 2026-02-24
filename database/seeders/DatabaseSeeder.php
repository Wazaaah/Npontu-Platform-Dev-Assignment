<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ActivityTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin — no shift, manager role
        User::firstOrCreate(['email' => 'admin@npontu.com'], [
            'name'       => 'System Administrator',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'shift'      => null,
            'phone'      => '+233 55 654 1525',
            'department' => 'IT Administration',
            'is_active'  => true,
        ]);

        // Morning staff
        User::firstOrCreate(['email' => 'kwame@npontu.com'], [
            'name'       => 'Kwame Asante',
            'password'   => Hash::make('password'),
            'role'       => 'staff',
            'shift'      => 'morning',
            'phone'      => '+233 24 000 0001',
            'department' => 'Applications Support',
            'is_active'  => true,
        ]);

        // Night staff
        User::firstOrCreate(['email' => 'ama@npontu.com'], [
            'name'       => 'Ama Boateng',
            'password'   => Hash::make('password'),
            'role'       => 'staff',
            'shift'      => 'night',
            'phone'      => '+233 24 000 0002',
            'department' => 'Applications Support',
            'is_active'  => true,
        ]);

        $admin = User::where('email', 'admin@npontu.com')->first();

        $templates = [
            ['name' => 'Daily SMS Count vs Log Count', 'description' => 'Compare the daily SMS count with SMS count from system logs. Document any discrepancies found.', 'category' => 'sms', 'applicable_shift' => 'both'],
            ['name' => 'Server Health Check', 'description' => 'Check CPU, RAM, and disk usage on all production servers. Flag any anomalies.', 'category' => 'server', 'applicable_shift' => 'both'],
            ['name' => 'Application Error Log Review', 'description' => 'Review application error logs for the shift period and categorize any recurring errors.', 'category' => 'logs', 'applicable_shift' => 'both'],
            ['name' => 'Network Connectivity Check', 'description' => 'Verify network connectivity across all critical nodes and document latency readings.', 'category' => 'network', 'applicable_shift' => 'both'],
            ['name' => 'Morning System Startup Verification', 'description' => 'Confirm all systems and services started correctly at the beginning of morning shift.', 'category' => 'general', 'applicable_shift' => 'morning'],
            ['name' => 'End-of-Day Backup Confirmation', 'description' => 'Verify that all scheduled backups completed successfully before end of night shift.', 'category' => 'server', 'applicable_shift' => 'night'],
            ['name' => 'SMS Gateway Status Check', 'description' => 'Verify SMS gateway is operational and check message delivery success rates.', 'category' => 'sms', 'applicable_shift' => 'both'],
            ['name' => 'Database Performance Review', 'description' => 'Check database query performance metrics and identify any slow queries.', 'category' => 'logs', 'applicable_shift' => 'both'],
        ];

        foreach ($templates as $template) {
            ActivityTemplate::firstOrCreate(
                ['name' => $template['name']],
                array_merge($template, ['is_active' => true, 'created_by' => $admin->id])
            );
        }
    }
}
