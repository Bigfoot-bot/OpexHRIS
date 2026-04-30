<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class StatutoryDeductionsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::getSettings();
        return view('central.superadmin.statutory-deductions', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            // PAYE
            'paye_band1_limit'     => ['required', 'numeric', 'min:1'],
            'paye_band1_rate'      => ['required', 'numeric', 'min:0', 'max:100'],
            'paye_band2_limit'     => ['required', 'numeric', 'gt:paye_band1_limit'],
            'paye_band2_rate'      => ['required', 'numeric', 'min:0', 'max:100'],
            'paye_band3_limit'     => ['required', 'numeric', 'gt:paye_band2_limit'],
            'paye_band3_rate'      => ['required', 'numeric', 'min:0', 'max:100'],
            'paye_band4_limit'     => ['required', 'numeric', 'gt:paye_band3_limit'],
            'paye_band4_rate'      => ['required', 'numeric', 'min:0', 'max:100'],
            'paye_band5_rate'      => ['required', 'numeric', 'min:0', 'max:100'],
            'paye_personal_relief' => ['required', 'numeric', 'min:0'],
            // SHA
            'sha_rate'             => ['required', 'numeric', 'min:0', 'max:100'],
            // NSSF
            'nssf_employee_rate'   => ['required', 'numeric', 'min:0', 'max:100'],
            'nssf_employer_rate'   => ['required', 'numeric', 'min:0', 'max:100'],
            'nssf_tier1_limit'     => ['required', 'numeric', 'min:1'],
            'nssf_tier2_limit'     => ['required', 'numeric', 'gt:nssf_tier1_limit'],
            // Housing Levy
            'housing_levy_employee_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'housing_levy_employer_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        SystemSetting::getSettings()->update($request->only([
            'paye_band1_limit', 'paye_band1_rate',
            'paye_band2_limit', 'paye_band2_rate',
            'paye_band3_limit', 'paye_band3_rate',
            'paye_band4_limit', 'paye_band4_rate',
            'paye_band5_rate',  'paye_personal_relief',
            'sha_rate',
            'nssf_employee_rate', 'nssf_employer_rate',
            'nssf_tier1_limit',   'nssf_tier2_limit',
            'housing_levy_employee_rate', 'housing_levy_employer_rate',
        ]));

        return back()->with('success', 'Statutory deduction rates updated successfully. New rates will apply to all future payroll runs.');
    }
}
