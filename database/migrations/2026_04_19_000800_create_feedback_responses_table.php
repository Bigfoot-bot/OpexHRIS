<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('feedback_requests', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('employee_id'); // Who is being reviewed
            $table->unsignedBigInteger('requested_by'); // Who initiated
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['peer', 'manager', 'subordinate', 'self', 'client'])->default('peer');
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->integer('total_reviewers')->default(0);
            $table->integer('completed_reviews')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('feedback_requests'); }
};
