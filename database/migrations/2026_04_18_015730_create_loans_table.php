<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('loan_number')->unique();
            $table->enum('type', ['salary_advance', 'personal_loan', 'emergency_loan', 'education_loan'])->default('personal_loan');
            $table->decimal('amount', 10, 2);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->integer('repayment_months');
            $table->decimal('monthly_deduction', 10, 2);
            $table->decimal('total_repayable', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->date('disbursement_date')->nullable();
            $table->date('start_repayment_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'disbursed', 'active', 'completed', 'rejected'])->default('pending');
            $table->text('purpose')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('loans'); }
};
