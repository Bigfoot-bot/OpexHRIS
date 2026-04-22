<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('job_position_id')->constrained('job_positions')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('current_employer')->nullable();
            $table->string('current_position')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->string('highest_qualification')->nullable();
            $table->string('resume')->nullable();
            $table->text('cover_letter')->nullable();
            $table->enum('stage', [
                'applied',
                'shortlisted',
                'interview',
                'assessment',
                'offer',
                'hired',
                'rejected'
            ])->default('applied');
            $table->integer('score')->nullable();
            $table->text('notes')->nullable();
            $table->date('interview_date')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};