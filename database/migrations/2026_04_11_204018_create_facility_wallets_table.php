<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->unique();
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency')->default('KES');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_wallets');
    }
};
