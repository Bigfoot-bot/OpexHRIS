<?php

namespace App\Services;

class PayrollService
{
    /**
     * Calculate PAYE (Kenya graduated tax rates 2024)
     */
    public function calculatePAYE(float $grossSalary): float
    {
        $taxableIncome = $grossSalary;
        $tax = 0;

        // Kenya PAYE tax bands 2024
        if ($taxableIncome <= 24000) {
            $tax = $taxableIncome * 0.10;
        } elseif ($taxableIncome <= 32333) {
            $tax = 2400 + ($taxableIncome - 24000) * 0.25;
        } elseif ($taxableIncome <= 500000) {
            $tax = 4483.25 + ($taxableIncome - 32333) * 0.30;
        } elseif ($taxableIncome <= 800000) {
            $tax = 144483.25 + ($taxableIncome - 500000) * 0.325;
        } else {
            $tax = 241983.25 + ($taxableIncome - 800000) * 0.35;
        }

        // Personal relief
        $personalRelief = 2400;
        $tax = max(0, $tax - $personalRelief);

        return round($tax, 2);
    }

    /**
     * Calculate NHIF (Kenya 2024 rates)
     */
    public function calculateNHIF(float $grossSalary): float
    {
        if ($grossSalary < 5999) return 150;
        if ($grossSalary <= 7999) return 300;
        if ($grossSalary <= 11999) return 400;
        if ($grossSalary <= 14999) return 500;
        if ($grossSalary <= 19999) return 600;
        if ($grossSalary <= 24999) return 750;
        if ($grossSalary <= 29999) return 850;
        if ($grossSalary <= 34999) return 900;
        if ($grossSalary <= 39999) return 950;
        if ($grossSalary <= 44999) return 1000;
        if ($grossSalary <= 49999) return 1100;
        if ($grossSalary <= 59999) return 1200;
        if ($grossSalary <= 69999) return 1300;
        if ($grossSalary <= 79999) return 1400;
        if ($grossSalary <= 89999) return 1500;
        if ($grossSalary <= 99999) return 1600;
        return 1700;
    }

    /**
     * Calculate NSSF (Kenya 2024 - new rates)
     * Tier I: 6% of pensionable pay up to KES 7,000 (max KES 420)
     * Tier II: 6% of pensionable pay between KES 7,001 and KES 36,000
     */
    public function calculateNSSF(float $grossSalary): array
    {
        $tierILimit    = 7000;
        $tierIILimit   = 36000;
        $rate          = 0.06;

        // Tier I
        $tierIPay      = min($grossSalary, $tierILimit);
        $tierIEmployee = round($tierIPay * $rate, 2);
        $tierIEmployer = $tierIEmployee;

        // Tier II
        $tierIIPay      = max(0, min($grossSalary, $tierIILimit) - $tierILimit);
        $tierIIEmployee = round($tierIIPay * $rate, 2);
        $tierIIEmployer = $tierIIEmployee;

        return [
            'employee' => round($tierIEmployee + $tierIIEmployee, 2),
            'employer' => round($tierIEmployer + $tierIIEmployer, 2),
        ];
    }

    /**
     * Calculate Housing Levy (1.5% of gross salary)
     */
    public function calculateHousingLevy(float $grossSalary): float
    {
        return round($grossSalary * 0.015, 2);
    }

    /**
     * Calculate full payroll for an employee
     */
    public function calculate(
        float $basicSalary,
        float $houseAllowance = 0,
        float $transportAllowance = 0,
        float $medicalAllowance = 0,
        float $otherAllowances = 0,
        float $overtimePay = 0,
        float $loanDeduction = 0,
        float $otherDeductions = 0
    ): array {
        $grossSalary = $basicSalary + $houseAllowance + $transportAllowance +
                       $medicalAllowance + $otherAllowances + $overtimePay;

        $paye         = $this->calculatePAYE($grossSalary);
        $nhif         = $this->calculateNHIF($grossSalary);
        $nssf         = $this->calculateNSSF($grossSalary);
        $housingLevy  = $this->calculateHousingLevy($grossSalary);

        $totalDeductions = $paye + $nhif + $nssf['employee'] + $housingLevy +
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
            'nhif'                => $nhif,
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