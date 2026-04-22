<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use App\Models\TenantSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $tenant   = tenant();
        $settings = TenantSetting::getAll();

        $departments = TenantSetting::get('departments', [
            'Administration', 'Nursing', 'Clinical', 'Laboratory',
            'Pharmacy', 'Radiology', 'Finance', 'HR', 'Maintenance', 'Security'
        ]);

        $leavePolicy = TenantSetting::get('leave_policy', [
            'leave_year_start' => '01-01',
            'carry_forward'    => false,
            'max_carry_forward'=> 10,
            'auto_approve_after_days' => 0,
        ]);

        $payrollSettings = TenantSetting::get('payroll_settings', [
            'pay_day'       => 28,
            'currency'      => 'KES',
            'payroll_email' => '',
        ]);

        $publicHolidays = TenantSetting::get('public_holidays', [
            ['name' => "New Year's Day",        'date' => '2026-01-01'],
            ['name' => 'Good Friday',            'date' => '2026-04-03'],
            ['name' => 'Easter Monday',          'date' => '2026-04-06'],
            ['name' => 'Labour Day',             'date' => '2026-05-01'],
            ['name' => 'Madaraka Day',           'date' => '2026-06-01'],
            ['name' => 'Huduma Day',             'date' => '2026-10-10'],
            ['name' => 'Mashujaa Day',           'date' => '2026-10-20'],
            ['name' => 'Jamhuri Day',            'date' => '2026-12-12'],
            ['name' => 'Christmas Day',          'date' => '2026-12-25'],
            ['name' => 'Boxing Day',             'date' => '2026-12-26'],
            ['name' => 'Idd ul Fitr',            'date' => '2026-03-31'],
        ]);

        return view('tenant.settings.index', compact(
            'tenant', 'settings', 'departments',
            'leavePolicy', 'payrollSettings', 'publicHolidays'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['nullable', 'email'],
            'phone'         => ['nullable', 'string'],
            'address'       => ['nullable', 'string'],
            'county'        => ['nullable', 'string'],
            'facility_type' => ['nullable', 'string'],
            'bed_capacity'  => ['nullable', 'integer'],
            'keph_level'    => ['nullable', 'string'],
        ]);

        $tenant = Tenant::find(tenant('id'));
        $tenant->update($validated);

        return back()->with('success', 'Facility profile updated successfully!');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $tenant = Tenant::find(tenant('id'));

        // Delete old logo if exists
        if ($tenant->logo && file_exists(public_path('logos/' . $tenant->logo))) {
            unlink(public_path('logos/' . $tenant->logo));
        }

        // Store new logo
        $filename = 'logo_' . tenant('id') . '_' . time() . '.' . $request->logo->extension();
        $request->logo->move(public_path('logos'), $filename);

        $tenant->update(['logo' => $filename]);

        return back()->with('success', 'Logo updated successfully!');
    }

    public function updateDepartments(Request $request)
    {
        $request->validate([
            'departments'   => ['required', 'array'],
            'departments.*' => ['required', 'string', 'max:100'],
        ]);

        TenantSetting::set('departments', array_values(array_filter($request->departments)));

        return back()->with('success', 'Departments updated successfully!');
    }

    public function updateLeavePolicy(Request $request)
    {
        $request->validate([
            'leave_year_start'        => ['required', 'string'],
            'carry_forward'           => ['boolean'],
            'max_carry_forward'       => ['required', 'integer', 'min:0'],
            'auto_approve_after_days' => ['required', 'integer', 'min:0'],
        ]);

        TenantSetting::set('leave_policy', [
            'leave_year_start'        => $request->leave_year_start,
            'carry_forward'           => $request->boolean('carry_forward'),
            'max_carry_forward'       => $request->max_carry_forward,
            'auto_approve_after_days' => $request->auto_approve_after_days,
        ]);

        return back()->with('success', 'Leave policy updated successfully!');
    }

    public function updatePayrollSettings(Request $request)
    {
        $request->validate([
            'pay_day'       => ['required', 'integer', 'between:1,31'],
            'currency'      => ['required', 'string'],
            'payroll_email' => ['nullable', 'email'],
        ]);

        TenantSetting::set('payroll_settings', [
            'pay_day'       => $request->pay_day,
            'currency'      => $request->currency,
            'payroll_email' => $request->payroll_email,
        ]);

        return back()->with('success', 'Payroll settings updated successfully!');
    }

    public function updatePublicHolidays(Request $request)
    {
        $request->validate([
            'holidays'        => ['required', 'array'],
            'holidays.*.name' => ['required', 'string'],
            'holidays.*.date' => ['required', 'date'],
        ]);

        TenantSetting::set('public_holidays', $request->holidays);

        return back()->with('success', 'Public holidays updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => bcrypt($request->password)]);

        return back()->with('success', 'Password updated successfully!');
    }
}
