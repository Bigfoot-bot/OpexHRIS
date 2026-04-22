<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branding_settings', function (Blueprint $table) {
            $table->id();
            $table->string('platform_name')->default('OpEx HRIS');
            $table->string('platform_tagline')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('primary_color')->default('#064e3b');
            $table->timestamps();
        });

        // Insert default record
        DB::table('branding_settings')->insert([
            'platform_name' => 'OpEx HRIS',
            'platform_tagline' => 'Healthcare HR Management Platform',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('branding_settings');
    }
};
