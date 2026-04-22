<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_discounts', function (Blueprint $table) {
            $table->id();
            $table->enum('cycle', ['monthly', 'quarterly', 'biannual', 'annual']);
            $table->integer('months');
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->timestamps();
        });

        DB::table('subscription_discounts')->insert([
            ['cycle' => 'monthly',   'months' => 1,  'discount_percentage' => 0,  'created_at' => now(), 'updated_at' => now()],
            ['cycle' => 'quarterly', 'months' => 3,  'discount_percentage' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['cycle' => 'biannual',  'months' => 6,  'discount_percentage' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['cycle' => 'annual',    'months' => 12, 'discount_percentage' => 20, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_discounts');
    }
};
