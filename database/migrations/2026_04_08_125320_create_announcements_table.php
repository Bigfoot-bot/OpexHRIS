<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('body');
            $table->enum('type', ['global', 'facility'])->default('facility');
            $table->string('sender_type')->default('super_admin'); // super_admin or tenant
            $table->string('tenant_id')->nullable(); // null = from super admin
            $table->boolean('send_email')->default(true);
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
