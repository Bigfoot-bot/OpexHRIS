<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Domains table is already created in the tenants migration
    }

    public function down(): void
    {
        // Domains table is dropped in the tenants migration
    }
};