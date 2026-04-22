<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_acknowledgments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('employee_id');
            $table->timestamp('acknowledged_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->unique(['document_id', 'employee_id']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_acknowledgments');
    }
};

