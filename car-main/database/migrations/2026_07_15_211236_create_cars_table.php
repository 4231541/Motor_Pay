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
        Schema::create('cars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained()->restrictOnDelete();
            $table->foreignId('car_model_id')->constrained()->restrictOnDelete();
            
            $table->string('title', 200);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->json('specifications')->nullable();
            
            $table->unsignedSmallInteger('year');
            $table->decimal('price', 12, 2);
            $table->decimal('min_installment', 10, 2)->nullable();
            
            $table->unsignedInteger('mileage')->default(0);
            $table->string('condition');
            $table->string('transmission');
            $table->string('fuel_type');
            $table->string('grade', 50)->nullable();
            $table->string('color', 50)->nullable();
            
            $table->string('status')->default('available');
            $table->string('approval_status')->default('pending');
            $table->text('rejection_reason')->nullable();
            
            $table->boolean('featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Explicit indexes
            $table->index('owner_id');
            $table->index('brand_id');
            $table->index('car_model_id');
            $table->index('year');
            $table->index('price');
            $table->index('featured');
            $table->index('is_active');
            $table->index('published_at');
            $table->index('slug');
            $table->index('status');
            $table->index(['approval_status', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
