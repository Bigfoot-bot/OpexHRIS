<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('timesheet_entries', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('timesheet_id');
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->decimal('hours', 5, 2)->default(0);
            $table->string('project')->nullable();
            $table->text('description')->nullable();
            $table->enum('work_type', ['regular', 'overtime', 'leave', 'holiday', 'remote'])->default('regular');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('timesheet_entries'); }
};
