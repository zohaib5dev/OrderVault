<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->string('type');
            $table->decimal('amount', 10, 2);
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('tax', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled', 'refunded'])->default('pending');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('stripe_invoice_id')->nullable();
            $table->string('billing_period_starts')->nullable();
            $table->string('billing_period_ends')->nullable();
            $table->text('notes')->nullable();
            $table->json('items')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'status']);
            $table->index('invoice_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
