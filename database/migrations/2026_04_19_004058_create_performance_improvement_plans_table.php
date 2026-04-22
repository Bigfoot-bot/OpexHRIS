<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('performance_improvement_plans', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('created_by');
            $table->string('title');
            $table->text('reason');
            $table->text('goals');
            $table->text('support_provided')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'active', 'completed', 'extended', 'terminated'])->default('draft');
            $table->text('progress_notes')->nullable();
            $table->text('outcome')->nullable();
            $table->date('review_date')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('performance_improvement_plans'); }
};
