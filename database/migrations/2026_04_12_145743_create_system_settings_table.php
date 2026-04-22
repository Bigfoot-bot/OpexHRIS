<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('vat_percentage', 5, 2)->default(16);
            $table->integer('trial_days')->default(30);
            $table->integer('grace_period_days')->default(7);
            $table->string('invoice_prefix')->default('INV');
            $table->string('receipt_prefix')->default('RCP');
            $table->timestamps();
        });

        DB::table('system_settings')->insert([
            'vat_percentage'   => 16,
            'trial_days'       => 30,
            'grace_period_days'=> 7,
            'invoice_prefix'   => 'INV',
            'receipt_prefix'   => 'RCP',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
