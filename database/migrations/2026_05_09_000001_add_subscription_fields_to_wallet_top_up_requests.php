<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_top_up_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('plan_id')->nullable()->after('daraja_checkout_request_id');
            $table->string('cycle')->nullable()->after('plan_id');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_top_up_requests', function (Blueprint $table) {
            $table->dropColumn(['plan_id', 'cycle']);
        });
    }
};
