<?php

namespace App\Services;

use App\Models\SystemSetting;

class PayrollService
{
    private function s(): SystemSetting
    {
        return SystemSetting::getSettings();
    }

    public function calculatePAYE(float $grossSalary): float
    {
        $s   = $this->s();
        $tax = 0;

        $b1l = (float) $s->paye_band1_limit;
        $b1r = (float) $s->paye_band1_rate  / 100;
        $b2l = (float) $s->paye_band2_limit;
        $b2r = (float) $s->paye_band2_rate  / 100;
        $b3l = (float) $s->paye_band3_limit;
        $b3r = (float) $s->paye_band3_rate  / 100;
        $b4l = (float) $s->paye_band4_limit;
        $b4r = (float) $s->paye_band4_rate  / 100;
        $b5r = (float) $s->paye_band5_rate  / 100;

        // Precompute cumulative tax at each band ceiling
        $tax1 = $b1l * $b1r;
        $tax2 = $tax1 + ($b2l - $b1l) * $b2r;
        $tax3 = $tax2 + ($b3l - $b2l) * $b3r;
        $tax4 = $tax3 + ($b4l - $b3l) * $b4r;

        if ($grossSalary <= $b1l) {
            $tax = $grossSalary * $b1r;
        } elseif ($grossSalary <= $b2l) {
            $tax = $tax1 + ($grossSalary - $b1l) * $b2r;
        } elseif ($grossSalary <= $b3l) {
            $tax = $tax2 + ($grossSalary - $b2l) * $b3r;
        } elseif ($grossSalary <= $b4l) {
            $tax = $tax3 + ($grossSalary - $b3l) * $b4r;
        } else {
            $tax = $tax4 + ($grossSalary - $b4l) * $b5r;
        }

        $tax = max(0, $tax - (float) $s->paye_personal_relief);

        return round($tax, 2);
    }

    public function calculateSHA(float $grossSalary): float
    {
        $rate = (float) $this->s()->sha_rate / 100;
        return round($grossSalary * $rate, 2);
    }

    /** @deprecated kept for backward compatibility — use calculateSHA() */
    public function calculateNHIF(float $grossSalary): float
    {
        return $this->calculateSHA($grossSalary);
    }

    public function calculateNSSF(float $grossSalary): array
    {
        $s    = $this->s();
        $empR = (float) $s->nssf_employee_rate / 100;
        $erlR = (float) $s->nssf_employer_rate / 100;
        $t1   = (float) $s->nssf_tier1_limit;
        $t2   = (float) $s->nssf_tier2_limit;

        $tier1Pay      = min($grossSalary, $t1);
        $tier1Employee = round($tier1Pay * $empR, 2);
        $tier1Employer = round($tier1Pay * $erlR, 2);

        $tier2Pay      = max(0, min($grossSalary, $t2) - $t1);
        $tier2Employee = round($tier2Pay * $empR, 2);
        $tier2Employer = round($tier2Pay * $erlR, 2);

        return [
            'employee' => round($tier1Employee + $tier2Employee, 2),
            'employer' => round($tier1Employer + $tier2Employer, 2),
        ];
    }

    public function calculateHousingLevy(float $grossSalary): float
    {
        $rate = (float) $this->s()->housing_levy_employee_rate / 100;
        return round($grossSalary * $rate, 2);
    }

    public function calculateHousingLevyEmployer(float $grossSalary): float
    {
        $rate = (float) $this->s()->housing_levy_employer_rate / 100;
        return round($grossSalary * $rate, 2);
    }

    public function calculate(
        float $basicSalary,
        float $houseAllowance    = 0,
        float $transportAllowance = 0,
        float $medicalAllowance  = 0,
        float $otherAllowances   = 0,
        float $overtimePay       = 0,
        float $loanDeduction     = 0,
        float $otherDeductions   = 0
    ): array {
        $grossSalary = $basicSalary + $houseAllowance + $transportAllowance +
                       $medicalAllowance + $otherAllowances + $overtimePay;

        $paye        = $this->calculatePAYE($grossSalary);
        $sha         = $this->calculateSHA($grossSalary);
        $nssf        = $this->calculateNSSF($grossSalary);
        $housingLevy = $this->calculateHousingLevy($grossSalary);

        $totalDeductions = $paye + $sha + $nssf['employee'] + $housingLevy +
                           $loanDeduction + $otherDeductions;

        $netSalary = $grossSalary - $totalDeductions;

        return [
            'basic_salary'        => round($basicSalary, 2),
            'house_allowance'     => round($houseAllowance, 2),
            'transport_allowance' => round($transportAllowance, 2),
            'medical_allowance'   => round($medicalAllowance, 2),
            'other_allowances'    => round($otherAllowances, 2),
            'overtime_pay'        => round($overtimePay, 2),
            'gross_salary'        => round($grossSalary, 2),
            'paye'                => $paye,
            'nhif'                => $sha,   // stored as nhif column in DB
            'nssf_employee'       => $nssf['employee'],
            'nssf_employer'       => $nssf['employer'],
            'housing_levy'        => $housingLevy,
            'loan_deduction'      => round($loanDeduction, 2),
            'other_deductions'    => round($otherDeductions, 2),
            'total_deductions'    => round($totalDeductions, 2),
            'net_salary'          => round($netSalary, 2),
        ];
    }
}
