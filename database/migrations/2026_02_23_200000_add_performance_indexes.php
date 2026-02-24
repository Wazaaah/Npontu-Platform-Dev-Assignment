<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_activities', function (Blueprint $table) {
            $table->index('activity_date');
            $table->index('shift');
            $table->index(['activity_date', 'shift']);
        });

        Schema::table('incident_reports', function (Blueprint $table) {
            $table->index('incident_date');
            $table->index('shift');
            $table->index('resolution_status');
            $table->index(['incident_date', 'shift']);
        });

        Schema::table('activity_updates', function (Blueprint $table) {
            $table->index('daily_activity_id');
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::table('daily_activities', function (Blueprint $table) {
            $table->dropIndex(['activity_date']);
            $table->dropIndex(['shift']);
            $table->dropIndex(['activity_date', 'shift']);
        });

        Schema::table('incident_reports', function (Blueprint $table) {
            $table->dropIndex(['incident_date']);
            $table->dropIndex(['shift']);
            $table->dropIndex(['resolution_status']);
            $table->dropIndex(['incident_date', 'shift']);
        });

        Schema::table('activity_updates', function (Blueprint $table) {
            $table->dropIndex(['daily_activity_id']);
            $table->dropIndex(['updated_by']);
        });
    }
};