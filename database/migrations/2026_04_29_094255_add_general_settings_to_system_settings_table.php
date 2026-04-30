<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->boolean('maintenance_mode')->default(false)->after('receipt_prefix');
            $table->text('maintenance_message')->nullable()->after('maintenance_mode');
            $table->boolean('allow_new_registrations')->default(true)->after('maintenance_message');
            $table->string('support_email')->nullable()->after('allow_new_registrations');
            $table->string('support_phone')->nullable()->after('support_email');
            $table->string('default_timezone')->default('Africa/Nairobi')->after('support_phone');
            $table->unsignedTinyInteger('max_login_attempts')->default(5)->after('default_timezone');
            $table->unsignedSmallInteger('session_lifetime')->default(120)->after('max_login_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'maintenance_mode', 'maintenance_message', 'allow_new_registrations',
                'support_email', 'support_phone', 'default_timezone',
                'max_login_attempts', 'session_lifetime',
            ]);
        });
    }
};
