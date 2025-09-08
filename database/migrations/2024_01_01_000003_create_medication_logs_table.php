<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_id')->constrained()->onDelete('cascade');
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->timestamp('actual_time')->nullable();
            $table->enum('status', ['taken', 'missed', 'skipped']);
            $table->json('side_effects')->nullable(); // ['頭痛', '吐き気']
            $table->text('notes')->nullable();
            $table->enum('severity_level', ['mild', 'moderate', 'severe'])->nullable();
            $table->timestamps();

            // インデックス
            $table->index('scheduled_date');
            $table->index(['medication_id', 'scheduled_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_logs');
    }
};
