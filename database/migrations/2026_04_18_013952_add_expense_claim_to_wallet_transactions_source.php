<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN source ENUM('mpesa_manual','mpesa_daraja','bank_transfer','payroll','adjustment','expense_claim','loan') NOT NULL");
    }
    public function down(): void {
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN source ENUM('mpesa_manual','mpesa_daraja','bank_transfer','payroll','adjustment') NOT NULL");
    }
};
