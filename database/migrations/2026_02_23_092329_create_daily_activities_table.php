<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_template_id')->constrained('activity_templates');
            $table->date('activity_date');
            $table->string('shift'); // morning | night
            $table->string('status')->default('pending'); // pending | done
            $table->timestamps();

            $table->unique(['activity_template_id', 'activity_date', 'shift']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_activities');
    }
};
