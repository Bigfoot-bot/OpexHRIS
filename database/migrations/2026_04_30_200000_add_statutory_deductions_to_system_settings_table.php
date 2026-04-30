<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            // PAYE tax bands (rates stored as percentages, e.g. 10 = 10%)
            $table->decimal('paye_band1_limit', 12, 2)->default(24000)->after('vat_percentage');
            $table->decimal('paye_band1_rate',  5, 2)->default(10)->after('paye_band1_limit');
            $table->decimal('paye_band2_limit', 12, 2)->default(32333)->after('paye_band1_rate');
            $table->decimal('paye_band2_rate',  5, 2)->default(25)->after('paye_band2_limit');
            $table->decimal('paye_band3_limit', 12, 2)->default(500000)->after('paye_band2_rate');
            $table->decimal('paye_band3_rate',  5, 2)->default(30)->after('paye_band3_limit');
            $table->decimal('paye_band4_limit', 12, 2)->default(800000)->after('paye_band3_rate');
            $table->decimal('paye_band4_rate',  5, 2)->default(32.5)->after('paye_band4_limit');
            $table->decimal('paye_band5_rate',  5, 2)->default(35)->after('paye_band4_rate');
            $table->decimal('paye_personal_relief', 10, 2)->default(2400)->after('paye_band5_rate');

            // SHA (Social Health Authority — replaced NHIF, rate as % of gross)
            $table->decimal('sha_rate', 5, 2)->default(2.75)->after('paye_personal_relief');

            // NSSF
            $table->decimal('nssf_employee_rate', 5, 2)->default(6)->after('sha_rate');
            $table->decimal('nssf_employer_rate', 5, 2)->default(6)->after('nssf_employee_rate');
            $table->decimal('nssf_tier1_limit', 10, 2)->default(7000)->after('nssf_employer_rate');
            $table->decimal('nssf_tier2_limit', 10, 2)->default(36000)->after('nssf_tier1_limit');

            // Housing Levy
            $table->decimal('housing_levy_employee_rate', 5, 2)->default(1.5)->after('nssf_tier2_limit');
            $table->decimal('housing_levy_employer_rate', 5, 2)->default(1.5)->after('housing_levy_employee_rate');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'paye_band1_limit', 'paye_band1_rate',
                'paye_band2_limit', 'paye_band2_rate',
                'paye_band3_limit', 'paye_band3_rate',
                'paye_band4_limit', 'paye_band4_rate',
                'paye_band5_rate',  'paye_personal_relief',
                'sha_rate',
                'nssf_employee_rate', 'nssf_employer_rate',
                'nssf_tier1_limit',  'nssf_tier2_limit',
                'housing_levy_employee_rate', 'housing_levy_employer_rate',
            ]);
        });
    }
};
