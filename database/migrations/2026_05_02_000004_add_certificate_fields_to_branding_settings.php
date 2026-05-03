<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->string('director_name')->nullable()->after('mpesa_account');
            $table->string('director_title')->nullable()->after('director_name');
            $table->string('director_signature')->nullable()->after('director_title');
        });
    }

    public function down(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->dropColumn(['director_name', 'director_title', 'director_signature']);
        });
    }
};
