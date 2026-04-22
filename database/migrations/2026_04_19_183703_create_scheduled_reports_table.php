<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('name');
            $table->string('report_type');
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('monthly');
            $table->string('day_of_week')->nullable(); // for weekly: mon,tue,wed...
            $table->integer('day_of_month')->nullable(); // for monthly: 1-31
            $table->time('send_time')->default('08:00:00');
            $table->string('format')->default('csv'); // csv, excel
            $table->text('recipients'); // comma separated emails
            $table->json('filters')->nullable(); // date range, department, etc
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('next_send_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('scheduled_reports'); }
};
