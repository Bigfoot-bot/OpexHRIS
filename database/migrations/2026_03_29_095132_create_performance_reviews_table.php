<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('reviewer_id')->nullable()->constrained('tenant_users')->onDelete('set null');
            $table->string('review_period');
            $table->integer('review_year');
            $table->enum('review_type', ['quarterly', 'bi_annual', 'annual', 'probation'])->default('annual');
            $table->enum('status', ['draft', 'self_assessment', 'manager_review', 'completed'])->default('draft');

            // Ratings (1-5)
            $table->decimal('self_rating', 3, 1)->nullable();
            $table->decimal('manager_rating', 3, 1)->nullable();
            $table->decimal('final_rating', 3, 1)->nullable();

            // Comments
            $table->text('self_assessment')->nullable();
            $table->text('manager_comments')->nullable();
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('goals_next_period')->nullable();

            $table->date('review_date')->nullable();
            $table->date('due_date')->nullable();

            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};