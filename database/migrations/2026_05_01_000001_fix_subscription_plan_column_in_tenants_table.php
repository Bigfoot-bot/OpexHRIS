<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change from ENUM('basic','professional','enterprise') to VARCHAR
        // so any custom subscription plan name can be stored
        DB::statement("ALTER TABLE tenants MODIFY subscription_plan VARCHAR(100) NOT NULL DEFAULT 'basic'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tenants MODIFY subscription_plan ENUM('basic','professional','enterprise') NOT NULL DEFAULT 'basic'");
    }
};
