<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_activity_id')->constrained('daily_activities');
            $table->foreignId('updated_by')->constrained('users');
            $table->string('status'); // pending | done
            $table->text('remark')->nullable();
            $table->timestamp('updated_at_time'); // explicit time capture
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_updates');
    }
};
