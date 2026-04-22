<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('color')->default('#064e3b');
            $table->timestamps();
            $table->index('tenant_id');
        });

        // Default categories will be seeded per tenant
    }

    public function down(): void
    {
        Schema::dropIfExists('document_categories');
    }
};
