<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'facility_type')) {
                $table->string('facility_type')->nullable();
            }
            if (!Schema::hasColumn('tenants', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('tenants', 'county')) {
                $table->string('county')->nullable();
            }
            if (!Schema::hasColumn('tenants', 'keph_level')) {
                $table->string('keph_level')->nullable();
            }
            if (!Schema::hasColumn('tenants', 'bed_capacity')) {
                $table->integer('bed_capacity')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['facility_type', 'address', 'county', 'keph_level', 'bed_capacity']);
        });
    }
};