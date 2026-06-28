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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->string('business_email')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('business_website')->nullable();
            $table->text('business_address');
            $table->enum('tax_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('tax_rate', 10, 2)->default(0);
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('commission_rate', 10, 2)->default(0);
            $table->string('registration_number')->nullable();
            $table->string('currency')->default('USD');
            $table->string('business_logo')->nullable();
            $table->timestamp('trial_ends_at')->nullable();

            $table->integer('plan_id')->nullable();

            $table->string('stripe_customer_id')->nullable()->index();
            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->timestamp('subscription_starts_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->string('stripe_account_id')->nullable();
            $table->string('subscription_status')->default('trial')->index();
            $table->string('stripe_account_status')->nullable();

            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
