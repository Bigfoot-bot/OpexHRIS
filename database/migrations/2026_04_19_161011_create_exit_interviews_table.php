<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('exit_interviews', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('separation_id');
            $table->unsignedBigInteger('employee_id');
            $table->integer('rating_overall')->nullable();
            $table->integer('rating_management')->nullable();
            $table->integer('rating_work_environment')->nullable();
            $table->integer('rating_compensation')->nullable();
            $table->integer('rating_growth')->nullable();
            $table->text('reason_leaving')->nullable();
            $table->text('what_worked_well')->nullable();
            $table->text('what_could_improve')->nullable();
            $table->text('would_recommend')->nullable();
            $table->boolean('would_return')->default(false);
            $table->text('additional_comments')->nullable();
            $table->boolean('is_submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('exit_interviews'); }
};
