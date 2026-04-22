<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('training_programs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['internal', 'external', 'online', 'conference', 'workshop'])->default('internal');
            $table->enum('category', ['clinical', 'administrative', 'compliance', 'leadership', 'technical', 'soft_skills'])->default('clinical');
            $table->string('provider')->nullable();
            $table->integer('cpd_points')->default(0);
            $table->decimal('cost', 10, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location')->nullable();
            $table->integer('max_participants')->nullable();
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled'])->default('planned');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_programs');
    }
};