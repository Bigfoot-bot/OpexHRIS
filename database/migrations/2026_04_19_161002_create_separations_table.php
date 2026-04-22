<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('separations', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->enum('type', ['resignation', 'termination', 'retirement', 'contract_end', 'redundancy', 'death'])->default('resignation');
            $table->date('notice_date');
            $table->date('last_working_date');
            $table->date('effective_date');
            $table->enum('status', ['pending', 'in_progress', 'cleared', 'completed'])->default('pending');
            $table->text('reason')->nullable();
            $table->decimal('leave_encashment', 10, 2)->default(0);
            $table->decimal('gratuity', 10, 2)->default(0);
            $table->decimal('pending_claims', 10, 2)->default(0);
            $table->decimal('loan_balance', 10, 2)->default(0);
            $table->decimal('final_dues', 10, 2)->default(0);
            $table->boolean('certificate_issued')->default(false);
            $table->date('certificate_date')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('separations'); }
};
