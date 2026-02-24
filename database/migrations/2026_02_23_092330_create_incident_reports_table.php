<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reported_by')->constrained('users');
            $table->date('incident_date');
            $table->string('shift'); // morning | night
            $table->string('title');
            $table->text('description');
            $table->text('steps_taken')->nullable();
            $table->string('resolution_status'); // resolved | unresolved
            $table->text('escalation_note')->nullable(); // for unresolved incidents
            $table->string('severity')->default('low'); // low | medium | high | critical
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
