<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ip_whitelists', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('ip_address');
            $table->string('label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('ip_whitelists'); }
};
