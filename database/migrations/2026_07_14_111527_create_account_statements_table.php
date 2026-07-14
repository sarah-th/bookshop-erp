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
        Schema::create('account_statements', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['client', 'supplier']);
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->decimal('amount', 12, 2);
            $table->foreignId('currency_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payment_method'); // cash, credit, bank_transfer, cheque
            $table->string('reference')->nullable(); // cheque number, transfer ref, etc
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_statements');
    }
};
