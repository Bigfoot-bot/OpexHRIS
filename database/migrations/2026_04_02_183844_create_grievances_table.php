<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grievances', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('grievance_number')->nullable();
            $table->string('title');
            $table->text('description');
            $table->enum('category', [
                'harassment',
                'discrimination',
                'working_conditions',
                'compensation',
                'management',
                'policy',
                'other'
            ])->default('other');
            $table->enum('status', [
                'submitted',
                'under_review',
                'investigation',
                'resolved',
                'closed',
                'escalated'
            ])->default('submitted');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('resolution')->nullable();
            $table->date('submitted_date');
            $table->date('resolution_date')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('tenant_users')->onDelete('set null');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grievances');
    }
};