<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disciplinary_cases', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('case_number')->nullable();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['verbal_warning', 'written_warning', 'final_warning', 'suspension', 'termination', 'other'])->default('verbal_warning');
            $table->enum('status', ['open', 'under_investigation', 'hearing_scheduled', 'closed', 'appealed'])->default('open');
            $table->enum('severity', ['minor', 'moderate', 'serious', 'gross_misconduct'])->default('minor');
            $table->date('incident_date');
            $table->date('hearing_date')->nullable();
            $table->date('resolution_date')->nullable();
            $table->text('outcome')->nullable();
            $table->text('employee_response')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('tenant_users')->onDelete('set null');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplinary_cases');
    }
};