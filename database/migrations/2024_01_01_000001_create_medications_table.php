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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_path', 500)->nullable();
            
            // 薬剤情報
            $table->string('generic_name')->nullable();
            $table->string('dosage_form')->nullable();
            $table->string('strength')->nullable();
            $table->string('manufacturer')->nullable();
            
            // 処方情報
            $table->string('prescription_number')->nullable();
            $table->string('prescribing_doctor')->nullable();
            $table->string('pharmacy')->nullable();
            $table->string('ndc_number')->nullable();
            
            // 薬効・副作用情報
            $table->json('indications')->nullable();
            $table->json('contraindications')->nullable();
            $table->json('side_effects')->nullable();
            $table->json('drug_interactions')->nullable();
            
            // その他
            $table->text('storage_instructions')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            
            // スケジュール情報
            $table->json('schedule')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
