<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('employee_number')->nullable();

            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternative_phone')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('national_id')->nullable();
            $table->string('kra_pin')->nullable();
            $table->string('nhif_number')->nullable();
            $table->string('nssf_number')->nullable();
            $table->string('nationality')->default('Kenyan');
            $table->text('address')->nullable();
            $table->string('photo')->nullable();

            // Employment Information
            $table->string('department')->nullable();
            $table->string('job_title')->nullable();
            $table->string('job_grade')->nullable();
            $table->enum('employment_type', ['permanent', 'contract', 'casual', 'intern'])->default('permanent');
            $table->enum('employment_status', ['active', 'probation', 'suspended', 'terminated', 'resigned'])->default('probation');
            $table->date('hire_date')->nullable();
            $table->date('confirmation_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->string('reporting_to')->nullable();
            $table->string('work_location')->nullable();
            $table->decimal('basic_salary', 12, 2)->nullable();

            // Healthcare Specific
            $table->string('professional_cadre')->nullable();
            $table->string('registration_body')->nullable();
            $table->string('registration_number')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->enum('license_status', ['valid', 'expiring', 'expired', 'not_applicable'])->default('not_applicable');
            $table->string('indemnity_provider')->nullable();
            $table->date('indemnity_expiry_date')->nullable();
            $table->string('specialty')->nullable();
            $table->integer('cpd_points_required')->default(0);
            $table->integer('cpd_points_earned')->default(0);

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'employee_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};