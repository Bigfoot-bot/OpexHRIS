<?php

namespace App\Services;

use App\Models\Tenant\Notification;
use App\Models\Tenant\User;

class NotificationService
{
    public static function send(
        string $title,
        string $message,
        string $type = 'info',
        string $link = null
    ): void {
        $users = User::where('tenant_id', tenant('id'))->get();
        foreach ($users as $user) {
            Notification::create([
                'tenant_id' => tenant('id'),
                'user_id'   => $user->id,
                'title'     => $title,
                'message'   => $message,
                'type'      => $type,
                'link'      => $link,
            ]);
        }
    }


    public static function loanRequested(string $employeeName, string $loanNumber, float $amount): void
    {
        $admins = User::where('tenant_id', tenant('id'))
                      ->where(function($q) { $q->where('is_admin', 1)->orWhereHas('tenantRoles'); })
                      ->get();
        foreach ($admins as $user) {
            Notification::create([
                'tenant_id' => tenant('id'),
                'user_id'   => $user->id,
                'title'     => 'New Loan Application',
                'message'   => $employeeName . ' has applied for a loan ' . $loanNumber . ' of KES ' . number_format($amount, 0) . '. Please review.',
                'type'      => 'info',
                'link'      => '/loans',
            ]);
        }
    }

    public static function expenseSubmitted(string $employeeName, string $claimNumber, float $amount): void
    {
        $admins = User::where('tenant_id', tenant('id'))
                      ->where(function($q) { $q->where('is_admin', 1)->orWhereHas('tenantRoles'); })
                      ->get();
        foreach ($admins as $user) {
            Notification::create([
                'tenant_id' => tenant('id'),
                'user_id'   => $user->id,
                'title'     => 'New Expense Claim',
                'message'   => $employeeName . ' has submitted expense claim ' . $claimNumber . ' for KES ' . number_format($amount, 0) . '. Please review.',
                'type'      => 'info',
                'link'      => '/expenses',
            ]);
        }
    }

    public static function expenseApproved(string $employeeName, string $claimNumber, float $amount): void
    {
        $user = User::where('tenant_id', tenant('id'))
                    ->whereHas('employee', fn($q) => $q->where('first_name', explode(' ', $employeeName)[0]))
                    ->first();
        if ($user) {
            Notification::create([
                'tenant_id' => tenant('id'),
                'user_id'   => $user->id,
                'title'     => 'Expense Claim Approved',
                'message'   => 'Your expense claim ' . $claimNumber . ' for KES ' . number_format($amount, 0) . ' has been approved.',
                'type'      => 'success',
                'link'      => '/my/dashboard',
            ]);
        }
    }

    public static function expenseRejected(string $employeeName, string $claimNumber): void
    {
        $user = User::where('tenant_id', tenant('id'))
                    ->whereHas('employee', fn($q) => $q->where('first_name', explode(' ', $employeeName)[0]))
                    ->first();
        if ($user) {
            Notification::create([
                'tenant_id' => tenant('id'),
                'user_id'   => $user->id,
                'title'     => 'Expense Claim Rejected',
                'message'   => 'Your expense claim ' . $claimNumber . ' has been rejected. Please contact HR for more information.',
                'type'      => 'error',
                'link'      => '/my/dashboard',
            ]);
        }
    }

    public static function loanApproved(string $employeeName, string $loanNumber, float $amount): void
    {
        $user = User::where('tenant_id', tenant('id'))
                    ->whereHas('employee', fn($q) => $q->where('first_name', explode(' ', $employeeName)[0]))
                    ->first();
        if ($user) {
            Notification::create([
                'tenant_id' => tenant('id'),
                'user_id'   => $user->id,
                'title'     => 'Loan Application Approved',
                'message'   => 'Your loan application ' . $loanNumber . ' for KES ' . number_format($amount, 0) . ' has been approved.',
                'type'      => 'success',
                'link'      => '/my/dashboard',
            ]);
        }
    }

    public static function loanRejected(string $employeeName, string $loanNumber): void
    {
        $user = User::where('tenant_id', tenant('id'))
                    ->whereHas('employee', fn($q) => $q->where('first_name', explode(' ', $employeeName)[0]))
                    ->first();
        if ($user) {
            Notification::create([
                'tenant_id' => tenant('id'),
                'user_id'   => $user->id,
                'title'     => 'Loan Application Rejected',
                'message'   => 'Your loan application ' . $loanNumber . ' has been rejected. Please contact HR for more information.',
                'type'      => 'error',
                'link'      => '/my/dashboard',
            ]);
        }
    }

    public static function loanDisbursed(string $employeeName, string $loanNumber, float $amount, string $bankName, string $accountNumber): void
    {
        $user = User::where('tenant_id', tenant('id'))
                    ->whereHas('employee', fn($q) => $q->where('first_name', explode(' ', $employeeName)[0]))
                    ->first();
        if ($user) {
            Notification::create([
                'tenant_id' => tenant('id'),
                'user_id'   => $user->id,
                'title'     => 'Loan Disbursed',
                'message'   => 'Your loan ' . $loanNumber . ' of KES ' . number_format($amount, 0) . ' has been disbursed to ' . $bankName . ' account ' . $accountNumber . '.',
                'type'      => 'success',
                'link'      => '/my/dashboard',
            ]);
        }
    }

    public static function leaveRequested(string $employeeName, int $days): void
    {
        self::send(
            'New Leave Request',
            "{$employeeName} has requested {$days} day(s) of leave.",
            'info',
            '/leave-requests'
        );
    }

    public static function leaveApproved(string $employeeName): void
    {
        self::send(
            'Leave Approved',
            "Leave request for {$employeeName} has been approved.",
            'success',
            '/leave-requests'
        );
    }

    public static function leaveRejected(string $employeeName): void
    {
        self::send(
            'Leave Rejected',
            "Leave request for {$employeeName} has been rejected.",
            'warning',
            '/leave-requests'
        );
    }

    public static function payrollGenerated(string $period, int $count): void
    {
        self::send(
            'Payroll Generated',
            "Payroll for {$period} has been generated for {$count} employee(s).",
            'success',
            '/payroll'
        );
    }

    public static function payrollApproved(string $period): void
    {
        self::send(
            'Payroll Approved',
            "Payroll for {$period} has been approved.",
            'success',
            '/payroll'
        );
    }

    public static function licenseExpiring(string $employeeName, string $licenseName, int $days): void
    {
        self::send(
            'License Expiring Soon',
            "{$employeeName}'s {$licenseName} expires in {$days} days.",
            'warning',
            '/licenses'
        );
    }

    public static function licenseExpired(string $employeeName, string $licenseName): void
    {
        self::send(
            'License Expired',
            "{$employeeName}'s {$licenseName} has expired.",
            'danger',
            '/licenses'
        );
    }

    public static function disciplinaryCaseFiled(string $employeeName, string $caseNumber): void
    {
        self::send(
            'Disciplinary Case Filed',
            "Case {$caseNumber} has been filed against {$employeeName}.",
            'danger',
            '/disciplinary'
        );
    }

    public static function grievanceFiled(string $employeeName, string $ref): void
    {
        self::send(
            'New Grievance Filed',
            "{$employeeName} has filed grievance {$ref}.",
            'warning',
            '/grievances'
        );
    }

    public static function employeeAdded(string $employeeName): void
    {
        self::send(
            'New Employee Added',
            "{$employeeName} has been added to the system.",
            'success',
            '/employees'
        );
    }
}

