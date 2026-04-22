<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('name'); // Morning, Evening, Night, Day
            $table->string('code')->nullable(); // M, E, N, D
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_hours')->nullable();
            $table->boolean('is_night_shift')->default(false);
            $table->decimal('night_shift_allowance', 10, 2)->default(0);
            $table->integer('break_duration_minutes')->default(0);
            $table->string('color')->default('#064e3b');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('shifts');
    }
};
