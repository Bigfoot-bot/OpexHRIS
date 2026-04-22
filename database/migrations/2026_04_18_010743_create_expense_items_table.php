<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_claim_id');
            $table->string('category'); // transport, accommodation, meals, supplies, etc.
            $table->string('description');
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->string('receipt_path')->nullable();
            $table->string('receipt_name')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('expense_items'); }
};
