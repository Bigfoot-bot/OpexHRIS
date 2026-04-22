<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tenant_users', function (Blueprint $table) {
            $table->boolean('mfa_enabled')->default(false)->after('password');
            $table->string('mfa_secret')->nullable()->after('mfa_enabled');
            $table->string('mfa_method')->default('email')->after('mfa_secret'); // email, totp
            $table->string('mfa_code')->nullable()->after('mfa_method');
            $table->timestamp('mfa_code_expires_at')->nullable()->after('mfa_code');
        });
    }
    public function down(): void {
        Schema::table('tenant_users', function (Blueprint $table) {
            $table->dropColumn(['mfa_enabled', 'mfa_secret', 'mfa_method', 'mfa_code', 'mfa_code_expires_at']);
        });
    }
};
