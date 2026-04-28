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
        Schema::create('sales_pages', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel users
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
            
            // Kolom untuk menyimpan input form
            $table->string('product_name');
            $table->text('description');
            $table->json('features')->nullable(); 
            $table->string('target_audience')->nullable();
            $table->string('price')->nullable();
            $table->text('unique_selling_points')->nullable();
            
            // Kolom hasil AI
            $table->longText('ai_generated_content')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_pages');
    }
};