<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('feedback_responses', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('feedback_request_id');
            $table->unsignedBigInteger('reviewer_id'); // User giving feedback
            $table->integer('rating_overall')->nullable();
            $table->integer('rating_communication')->nullable();
            $table->integer('rating_teamwork')->nullable();
            $table->integer('rating_technical')->nullable();
            $table->integer('rating_leadership')->nullable();
            $table->text('strengths')->nullable();
            $table->text('improvements')->nullable();
            $table->text('comments')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('feedback_responses'); }
};
