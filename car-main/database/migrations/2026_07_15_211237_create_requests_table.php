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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('car_id')->constrained()->restrictOnDelete();
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('type');
            $table->string('status')->default('received');
            
            $table->decimal('down_payment', 12, 2)->nullable();
            $table->unsignedSmallInteger('financing_months')->nullable();
            $table->decimal('monthly_installment', 12, 2)->nullable();
            
            $table->string('source')->nullable();
            $table->text('customer_message')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Explicit indexes
            $table->index('user_id');
            $table->index('car_id');
            $table->index('assigned_agent_id');
            $table->index('type');
            $table->index('status');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
