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
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer')->onUpdate('cascade')->onDelete('restrict');
            $table->string('invoice_code')->unique();
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['draft', 'unpaid', 'partial', 'paid'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
