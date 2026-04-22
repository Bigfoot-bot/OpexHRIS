<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_top_up_requests', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->enum('payment_method', ['mpesa_manual', 'mpesa_daraja', 'bank_transfer']);
            $table->decimal('amount', 15, 2);
            $table->string('transaction_reference')->nullable();
            $table->string('mpesa_phone')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_reference')->nullable();
            $table->string('proof_file')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('daraja_checkout_request_id')->nullable();
            $table->timestamps();
            $table->index('tenant_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_top_up_requests');
    }
};
