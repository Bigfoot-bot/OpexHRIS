<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('monthly_price', 10, 2);
            $table->integer('max_employees')->default(50);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // Default plans
        DB::table('subscription_plans')->insert([
            ['name' => 'Basic', 'description' => 'Perfect for small facilities', 'monthly_price' => 5000, 'max_employees' => 20, 'is_active' => true, 'is_featured' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Standard', 'description' => 'Great for growing facilities', 'monthly_price' => 10000, 'max_employees' => 50, 'is_active' => true, 'is_featured' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Premium', 'description' => 'For large healthcare facilities', 'monthly_price' => 20000, 'max_employees' => 200, 'is_active' => true, 'is_featured' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
