<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's mark all the pending migrations as complete
        // This will prevent errors when trying to run duplicate migrations
        $migrationFiles = [
            '2025_04_12_205554_create_leads_table',
            '2025_04_12_205600_create_email_campaigns_table',
            '2025_04_12_205611_create_scheduled_emails_table',
            '2025_04_12_205649_create_leads_table',
            '2025_04_12_205721_create_email_campaigns_table',
            '2025_04_12_205752_create_scheduled_emails_table'
        ];

        // Get the max batch number
        $maxBatch = DB::table('migrations')->max('batch') ?? 0;
        
        // Mark all migrations as complete with the next batch number
        foreach ($migrationFiles as $migrationFile) {
            if (!DB::table('migrations')->where('migration', $migrationFile)->exists()) {
                DB::table('migrations')->insert([
                    'migration' => $migrationFile,
                    'batch' => $maxBatch + 1
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the marked migrations
        $migrationFiles = [
            '2025_04_12_205554_create_leads_table',
            '2025_04_12_205600_create_email_campaigns_table',
            '2025_04_12_205611_create_scheduled_emails_table',
            '2025_04_12_205649_create_leads_table',
            '2025_04_12_205721_create_email_campaigns_table',
            '2025_04_12_205752_create_scheduled_emails_table'
        ];

        DB::table('migrations')->whereIn('migration', $migrationFiles)->delete();
    }
};
