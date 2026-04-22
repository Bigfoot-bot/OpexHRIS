<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('professional_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('license_name');
            $table->string('license_number');
            $table->string('issuing_body');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date');
            $table->enum('status', ['valid', 'expiring', 'expired'])->default('valid');
            $table->string('document')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('alert_sent')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_licenses');
    }
};