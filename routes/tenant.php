<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use App\Http\Controllers\Tenant\Auth\LoginController;
use App\Http\Controllers\Tenant\Auth\InvitationController;
use App\Http\Controllers\Tenant\Auth\PasswordResetController;
use App\Http\Controllers\Tenant\Auth\SetPasswordController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\NotificationController;
use App\Http\Controllers\Tenant\SettingsController;
use App\Http\Controllers\Tenant\UserController;
use App\Http\Controllers\Tenant\AuditLogController;
use App\Http\Controllers\Tenant\ExportController;
use App\Http\Controllers\Tenant\SearchController;
use App\Http\Controllers\Tenant\TicketController;
use App\Http\Controllers\Tenant\PortalController;
use App\Http\Controllers\Tenant\AnnouncementController;
use App\Http\Controllers\Tenant\HR\EmployeeController;
use App\Http\Controllers\Tenant\HR\OnboardingController;
use App\Http\Controllers\Tenant\Employee\SelfServiceController;
use App\Http\Controllers\Tenant\Leave\LeaveTypeController;
use App\Http\Controllers\Tenant\Leave\LeaveRequestController;
use App\Http\Controllers\Tenant\Payroll\PayrollController;
use App\Http\Controllers\Tenant\Payroll\PayslipController;
use App\Http\Controllers\Tenant\Performance\PerformanceController;
use App\Http\Controllers\Tenant\Compliance\LicenseController;
use App\Http\Controllers\Tenant\Recruitment\JobPositionController;
use App\Http\Controllers\Tenant\Recruitment\ApplicantController;
use App\Http\Controllers\Tenant\Disciplinary\DisciplinaryController;
use App\Http\Controllers\Tenant\Disciplinary\GrievanceController;
use App\Http\Controllers\Tenant\Training\TrainingController;
use App\Http\Controllers\Tenant\Reports\ReportController;

$centralDomains = config('tenancy.central_domains');

Route::middleware(['web'])
    ->group(function () use ($centralDomains) {

        if (!in_array(request()->getHost(), $centralDomains)) {
            Route::middleware([InitializeTenancyByDomain::class, 'maintenance'])->group(function () {

                Route::get('/', fn() => redirect()->route('tenant.login'));

                // Daraja Callback - Public
                Route::post('/daraja/callback', [App\Http\Controllers\Tenant\DarajaController::class, 'callback'])->name('tenant.daraja.callback');

                Route::middleware(['guest', 'throttle:login'])->group(function () {
                    Route::get('/login', [LoginController::class, 'show'])->name('tenant.login');
                    Route::post('/login', [LoginController::class, 'login'])->name('tenant.login.post');
                    Route::get('/invitation/{token}', [InvitationController::class, 'accept'])->name('tenant.invitation.accept');
                    Route::post('/invitation/{token}', [InvitationController::class, 'store'])->name('tenant.invitation.store');
                    Route::get('/set-password', [SetPasswordController::class, 'show'])->name('tenant.set-password.show');
                    Route::post('/set-password', [SetPasswordController::class, 'store'])->name('tenant.set-password.store');
                    Route::middleware('throttle:password-reset')->group(function () {
                        Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('tenant.password.request');
                        Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('tenant.password.email');
                        Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('tenant.password.reset');
                        Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('tenant.password.update');
                    });
                });

                Route::middleware(['auth', 'throttle:tenant'])->group(function () {
                    Route::post('/logout', [LoginController::class, 'logout'])->name('tenant.logout');
                    Route::post('portal/switch', [PortalController::class, 'switch'])->name('tenant.portal.switch');
                    Route::post('portal/switch', [PortalController::class, 'switch'])->name('tenant.portal.switch');
                });






                Route::post('/lang', function() {
                    $locale = request('locale', 'en');
                    if (in_array($locale, ['en', 'sw'])) {
                        session(['locale' => $locale]);
                    }
                    return back();
                })->name('tenant.lang.switch');
                Route::middleware(['auth', 'tenant.subscription', 'tenant.permission'])->group(function () {
                    Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');
                    Route::get('search', [SearchController::class, 'index'])->name('tenant.search');
                    Route::get('roles', [App\Http\Controllers\Tenant\RoleController::class, 'index'])->name('tenant.roles.index');
                    Route::post('roles', [App\Http\Controllers\Tenant\RoleController::class, 'store'])->name('tenant.roles.store');
                    Route::get('roles/users', [App\Http\Controllers\Tenant\RoleController::class, 'users'])->name('tenant.roles.users');
                    Route::post('roles/assign', [App\Http\Controllers\Tenant\RoleController::class, 'assignRole'])->name('tenant.roles.assign');
                    Route::post('roles/revoke', [App\Http\Controllers\Tenant\RoleController::class, 'revokeRole'])->name('tenant.roles.revoke');
                    Route::put('roles/{role}', [App\Http\Controllers\Tenant\RoleController::class, 'update'])->name('tenant.roles.update');
                    Route::delete('roles/{role}', [App\Http\Controllers\Tenant\RoleController::class, 'destroy'])->name('tenant.roles.destroy');

                    // Branch Portal Routes
                    Route::prefix('branch/{branch}')->group(function () {
                        Route::get('dashboard', [App\Http\Controllers\Tenant\BranchPortalController::class, 'dashboard'])->name('tenant.branch.dashboard');
                        Route::get('employees', [App\Http\Controllers\Tenant\BranchPortalController::class, 'employees'])->name('tenant.branch.employees');
                        Route::get('leave', [App\Http\Controllers\Tenant\BranchPortalController::class, 'leave'])->name('tenant.branch.leave');
                        Route::post('leave/{leaveRequest}/approve', [App\Http\Controllers\Tenant\BranchPortalController::class, 'approveLeave'])->name('tenant.branch.leave.approve');
                        Route::post('leave/{leaveRequest}/reject', [App\Http\Controllers\Tenant\BranchPortalController::class, 'rejectLeave'])->name('tenant.branch.leave.reject');
                        Route::get('documents', [App\Http\Controllers\Tenant\BranchPortalController::class, 'documents'])->name('tenant.branch.documents');
                        Route::get('assets', [App\Http\Controllers\Tenant\BranchPortalController::class, 'assets'])->name('tenant.branch.assets');
                        Route::get('contracts', [App\Http\Controllers\Tenant\BranchPortalController::class, 'contracts'])->name('tenant.branch.contracts');
                        Route::get('payroll', [App\Http\Controllers\Tenant\BranchPortalController::class, 'payroll'])->name('tenant.branch.payroll');
                        Route::get('settings', [App\Http\Controllers\Tenant\BranchPortalController::class, 'settings'])->name('tenant.branch.settings');
                        Route::put('settings', [App\Http\Controllers\Tenant\BranchPortalController::class, 'updateSettings'])->name('tenant.branch.settings.update');
                        Route::get('announcements', [App\Http\Controllers\Tenant\BranchPortalController::class, 'announcements'])->name('tenant.branch.announcements');
                        Route::post('announcements', [App\Http\Controllers\Tenant\BranchPortalController::class, 'storeAnnouncement'])->name('tenant.branch.announcements.store');
                        Route::delete('announcements/{announcement}', [App\Http\Controllers\Tenant\BranchPortalController::class, 'destroyAnnouncement'])->name('tenant.branch.announcements.destroy');
                        Route::get('reports', [App\Http\Controllers\Tenant\BranchPortalController::class, 'reports'])->name('tenant.branch.reports');
                        Route::get('employees/create', [App\Http\Controllers\Tenant\BranchPortalController::class, 'createEmployee'])->name('tenant.branch.employees.create');
                        Route::post('employees', [App\Http\Controllers\Tenant\BranchPortalController::class, 'storeEmployee'])->name('tenant.branch.employees.store');
                        Route::get('employees/{employee}/edit', [App\Http\Controllers\Tenant\BranchPortalController::class, 'editEmployee'])->name('tenant.branch.employees.edit');
                        Route::put('employees/{employee}', [App\Http\Controllers\Tenant\BranchPortalController::class, 'updateEmployee'])->name('tenant.branch.employees.update');
                    });
                    Route::post('employees/{employee}/transfer', [App\Http\Controllers\Tenant\HR\EmployeeController::class, 'transfer'])->name('tenant.employees.transfer');
                    // Separations
                    Route::get('separations', [App\Http\Controllers\Tenant\SeparationController::class, 'index'])->name('tenant.separations.index');
                    Route::get('separations/create', [App\Http\Controllers\Tenant\SeparationController::class, 'create'])->name('tenant.separations.create');
                    Route::post('separations', [App\Http\Controllers\Tenant\SeparationController::class, 'store'])->name('tenant.separations.store');
                    Route::get('separations/{separation}', [App\Http\Controllers\Tenant\SeparationController::class, 'show'])->name('tenant.separations.show');
                    Route::post('separations/{separation}/complete', [App\Http\Controllers\Tenant\SeparationController::class, 'complete'])->name('tenant.separations.complete');
                    Route::post('separations/{separation}/certificate', [App\Http\Controllers\Tenant\SeparationController::class, 'generateCertificate'])->name('tenant.separations.certificate');
                    Route::post('separations/clearance/{item}/clear', [App\Http\Controllers\Tenant\SeparationController::class, 'clearItem'])->name('tenant.separations.clear-item');
                    Route::post('separations/exit-interview/{interview}', [App\Http\Controllers\Tenant\SeparationController::class, 'submitExitInterview'])->name('tenant.separations.exit-interview');

                    // MFA
                    Route::get('mfa/challenge', [App\Http\Controllers\Tenant\MfaController::class, 'challenge'])->name('tenant.mfa.challenge');
                    Route::post('mfa/send-code', [App\Http\Controllers\Tenant\MfaController::class, 'sendCode'])->name('tenant.mfa.send-code');
                    Route::post('mfa/verify', [App\Http\Controllers\Tenant\MfaController::class, 'verify'])->name('tenant.mfa.verify');
                    Route::get('mfa/setup', [App\Http\Controllers\Tenant\MfaController::class, 'setup'])->name('tenant.mfa.setup');
                    Route::post('mfa/enable', [App\Http\Controllers\Tenant\MfaController::class, 'enable'])->name('tenant.mfa.enable');
                    Route::post('mfa/disable', [App\Http\Controllers\Tenant\MfaController::class, 'disable'])->name('tenant.mfa.disable');

                    // IP Whitelist
                    Route::get('ip-whitelist', [App\Http\Controllers\Tenant\IpWhitelistController::class, 'index'])->name('tenant.ip-whitelist.index');
                    Route::post('ip-whitelist', [App\Http\Controllers\Tenant\IpWhitelistController::class, 'store'])->name('tenant.ip-whitelist.store');
                    Route::post('ip-whitelist/{ip}/toggle', [App\Http\Controllers\Tenant\IpWhitelistController::class, 'toggle'])->name('tenant.ip-whitelist.toggle');
                    Route::delete('ip-whitelist/{ip}', [App\Http\Controllers\Tenant\IpWhitelistController::class, 'destroy'])->name('tenant.ip-whitelist.destroy');

                    // Scheduled Reports
                    Route::get('scheduled-reports', [App\Http\Controllers\Tenant\ScheduledReportController::class, 'index'])->name('tenant.scheduled-reports.index');
                    Route::post('scheduled-reports', [App\Http\Controllers\Tenant\ScheduledReportController::class, 'store'])->name('tenant.scheduled-reports.store');
                    Route::post('scheduled-reports/{report}/toggle', [App\Http\Controllers\Tenant\ScheduledReportController::class, 'toggle'])->name('tenant.scheduled-reports.toggle');
                    Route::delete('scheduled-reports/{report}', [App\Http\Controllers\Tenant\ScheduledReportController::class, 'destroy'])->name('tenant.scheduled-reports.destroy');

                    // Report Builder
                    Route::get('report-builder', [App\Http\Controllers\Tenant\ReportBuilderController::class, 'index'])->name('tenant.report-builder.index');
                    Route::post('report-builder', [App\Http\Controllers\Tenant\ReportBuilderController::class, 'generate'])->name('tenant.report-builder.generate');

                    // PIP
                    Route::get('pip', [App\Http\Controllers\Tenant\PIPController::class, 'index'])->name('tenant.pip.index');
                    Route::get('pip/create', [App\Http\Controllers\Tenant\PIPController::class, 'create'])->name('tenant.pip.create');
                    Route::post('pip', [App\Http\Controllers\Tenant\PIPController::class, 'store'])->name('tenant.pip.store');
                    Route::get('pip/{pip}', [App\Http\Controllers\Tenant\PIPController::class, 'show'])->name('tenant.pip.show');
                    Route::get('pip/{pip}/edit', [App\Http\Controllers\Tenant\PIPController::class, 'edit'])->name('tenant.pip.edit');
                    Route::put('pip/{pip}', [App\Http\Controllers\Tenant\PIPController::class, 'update'])->name('tenant.pip.update');
                    Route::post('pip/{pip}/activate', [App\Http\Controllers\Tenant\PIPController::class, 'activate'])->name('tenant.pip.activate');
                    Route::post('pip/{pip}/complete', [App\Http\Controllers\Tenant\PIPController::class, 'complete'])->name('tenant.pip.complete');
                    Route::delete('pip/{pip}', [App\Http\Controllers\Tenant\PIPController::class, 'destroy'])->name('tenant.pip.destroy');

                    // 360 Feedback
                    Route::get('feedback', [App\Http\Controllers\Tenant\FeedbackController::class, 'index'])->name('tenant.feedback.index');
                    Route::get('feedback/create', [App\Http\Controllers\Tenant\FeedbackController::class, 'create'])->name('tenant.feedback.create');
                    Route::post('feedback', [App\Http\Controllers\Tenant\FeedbackController::class, 'store'])->name('tenant.feedback.store');
                    Route::get('feedback/{feedback}', [App\Http\Controllers\Tenant\FeedbackController::class, 'show'])->name('tenant.feedback.show');
                    Route::get('feedback/respond/{response}', [App\Http\Controllers\Tenant\FeedbackController::class, 'respond'])->name('tenant.feedback.respond');
                    Route::post('feedback/respond/{response}', [App\Http\Controllers\Tenant\FeedbackController::class, 'submitResponse'])->name('tenant.feedback.submit');
                    Route::delete('feedback/{feedback}', [App\Http\Controllers\Tenant\FeedbackController::class, 'destroy'])->name('tenant.feedback.destroy');

                    // Statutory Returns
                    Route::get('statutory', [App\Http\Controllers\Tenant\StatutoryReturnsController::class, 'index'])->name('tenant.statutory.index');
                    Route::post('statutory/p9', [App\Http\Controllers\Tenant\StatutoryReturnsController::class, 'p9'])->name('tenant.statutory.p9');
                    Route::post('statutory/p10', [App\Http\Controllers\Tenant\StatutoryReturnsController::class, 'p10'])->name('tenant.statutory.p10');
                    Route::post('statutory/nhif', [App\Http\Controllers\Tenant\StatutoryReturnsController::class, 'nhif'])->name('tenant.statutory.nhif');
                    Route::post('statutory/nssf', [App\Http\Controllers\Tenant\StatutoryReturnsController::class, 'nssf'])->name('tenant.statutory.nssf');
                    Route::post('statutory/housing-levy', [App\Http\Controllers\Tenant\StatutoryReturnsController::class, 'housingLevy'])->name('tenant.statutory.housing-levy');

                    // Bank Files
                    Route::get('bank-files', [App\Http\Controllers\Tenant\BankFileController::class, 'index'])->name('tenant.bank-files.index');
                    Route::post('bank-files/generate', [App\Http\Controllers\Tenant\BankFileController::class, 'generate'])->name('tenant.bank-files.generate');
                    Route::post('bank-files/expense', [App\Http\Controllers\Tenant\BankFileController::class, 'generateExpense'])->name('tenant.bank-files.expense');

                    // Loans
                    Route::get('loans', [App\Http\Controllers\Tenant\LoanController::class, 'index'])->name('tenant.loans.index');
                    Route::get('loans/create', [App\Http\Controllers\Tenant\LoanController::class, 'create'])->name('tenant.loans.create');
                    Route::post('loans', [App\Http\Controllers\Tenant\LoanController::class, 'store'])->name('tenant.loans.store');
                    Route::get('loans/{loan}', [App\Http\Controllers\Tenant\LoanController::class, 'show'])->name('tenant.loans.show');
                    Route::post('loans/{loan}/approve', [App\Http\Controllers\Tenant\LoanController::class, 'approve'])->name('tenant.loans.approve');
                    Route::post('loans/{loan}/reject', [App\Http\Controllers\Tenant\LoanController::class, 'reject'])->name('tenant.loans.reject');
                    Route::post('loans/{loan}/disburse', [App\Http\Controllers\Tenant\LoanController::class, 'disburse'])->name('tenant.loans.disburse');
                    Route::post('loans/repayments/{repayment}/pay', [App\Http\Controllers\Tenant\LoanController::class, 'recordPayment'])->name('tenant.loans.repayment.pay');

                    // Expenses
                    Route::get('expenses', [App\Http\Controllers\Tenant\ExpenseController::class, 'index'])->name('tenant.expenses.index');
                    Route::get('expenses/create', [App\Http\Controllers\Tenant\ExpenseController::class, 'create'])->name('tenant.expenses.create');
                    Route::post('expenses', [App\Http\Controllers\Tenant\ExpenseController::class, 'store'])->name('tenant.expenses.store');
                    Route::get('expenses/{expense}', [App\Http\Controllers\Tenant\ExpenseController::class, 'show'])->name('tenant.expenses.show');
                    Route::post('expenses/{expense}/submit', [App\Http\Controllers\Tenant\ExpenseController::class, 'submit'])->name('tenant.expenses.submit');
                    Route::post('expenses/{expense}/approve', [App\Http\Controllers\Tenant\ExpenseController::class, 'approve'])->name('tenant.expenses.approve');
                    Route::post('expenses/{expense}/reject', [App\Http\Controllers\Tenant\ExpenseController::class, 'reject'])->name('tenant.expenses.reject');
                    Route::post('expenses/{expense}/pay', [App\Http\Controllers\Tenant\ExpenseController::class, 'pay'])->name('tenant.expenses.pay');
                    Route::delete('expenses/{expense}', [App\Http\Controllers\Tenant\ExpenseController::class, 'destroy'])->name('tenant.expenses.destroy');

                    // Timesheets
                    Route::get('timesheets', [App\Http\Controllers\Tenant\TimesheetController::class, 'index'])->name('tenant.timesheets.index');
                    Route::get('timesheets/create', [App\Http\Controllers\Tenant\TimesheetController::class, 'create'])->name('tenant.timesheets.create');
                    Route::post('timesheets', [App\Http\Controllers\Tenant\TimesheetController::class, 'store'])->name('tenant.timesheets.store');
                    Route::get('timesheets/{timesheet}', [App\Http\Controllers\Tenant\TimesheetController::class, 'show'])->name('tenant.timesheets.show');
                    Route::post('timesheets/{timesheet}/submit', [App\Http\Controllers\Tenant\TimesheetController::class, 'submit'])->name('tenant.timesheets.submit');
                    Route::post('timesheets/{timesheet}/approve', [App\Http\Controllers\Tenant\TimesheetController::class, 'approve'])->name('tenant.timesheets.approve');
                    Route::post('timesheets/{timesheet}/reject', [App\Http\Controllers\Tenant\TimesheetController::class, 'reject'])->name('tenant.timesheets.reject');
                    Route::delete('timesheets/{timesheet}', [App\Http\Controllers\Tenant\TimesheetController::class, 'destroy'])->name('tenant.timesheets.destroy');

                    // Overtime
                    Route::get('overtime', [App\Http\Controllers\Tenant\OvertimeController::class, 'index'])->name('tenant.overtime.index');
                    Route::post('overtime', [App\Http\Controllers\Tenant\OvertimeController::class, 'store'])->name('tenant.overtime.store');
                    Route::post('overtime/{overtime}/approve', [App\Http\Controllers\Tenant\OvertimeController::class, 'approve'])->name('tenant.overtime.approve');
                    Route::post('overtime/{overtime}/reject', [App\Http\Controllers\Tenant\OvertimeController::class, 'reject'])->name('tenant.overtime.reject');
                    Route::delete('overtime/{overtime}', [App\Http\Controllers\Tenant\OvertimeController::class, 'destroy'])->name('tenant.overtime.destroy');

                    // Shifts & Rosters
                    Route::get('shifts', [App\Http\Controllers\Tenant\ShiftController::class, 'index'])->name('tenant.shifts.index');
                    Route::post('shifts', [App\Http\Controllers\Tenant\ShiftController::class, 'storeShift'])->name('tenant.shifts.store');
                    Route::delete('shifts/{shift}', [App\Http\Controllers\Tenant\ShiftController::class, 'destroyShift'])->name('tenant.shifts.destroy');
                    Route::get('rosters/create', [App\Http\Controllers\Tenant\ShiftController::class, 'createRoster'])->name('tenant.shifts.roster.create');
                    Route::post('rosters', [App\Http\Controllers\Tenant\ShiftController::class, 'storeRoster'])->name('tenant.shifts.roster.store');
                    Route::get('rosters/{roster}', [App\Http\Controllers\Tenant\ShiftController::class, 'showRoster'])->name('tenant.shifts.roster');
                    Route::post('rosters/{roster}/assign', [App\Http\Controllers\Tenant\ShiftController::class, 'assignShift'])->name('tenant.shifts.assign');
                    Route::post('rosters/{roster}/publish', [App\Http\Controllers\Tenant\ShiftController::class, 'publishRoster'])->name('tenant.shifts.publish');
                    Route::delete('rosters/{roster}', [App\Http\Controllers\Tenant\ShiftController::class, 'destroyRoster'])->name('tenant.shifts.roster.destroy');

                    Route::get('branches', [App\Http\Controllers\Tenant\BranchController::class, 'index'])->name('tenant.branches.index');
                    Route::get('branches/create', [App\Http\Controllers\Tenant\BranchController::class, 'create'])->name('tenant.branches.create');
                    Route::post('branches', [App\Http\Controllers\Tenant\BranchController::class, 'store'])->name('tenant.branches.store');
                    Route::put('branches/{branch}', [App\Http\Controllers\Tenant\BranchController::class, 'update'])->name('tenant.branches.update');
                    Route::delete('branches/{branch}', [App\Http\Controllers\Tenant\BranchController::class, 'destroy'])->name('tenant.branches.destroy');
                    Route::get('branches/{branch}/portal', [App\Http\Controllers\Tenant\BranchController::class, 'portal'])->name('tenant.branches.portal');
                    Route::post('branches/{branch}/assign-employee', [App\Http\Controllers\Tenant\BranchController::class, 'assignEmployee'])->name('tenant.branches.assign-employee');
                    Route::post('branches/{branch}/remove-employee', [App\Http\Controllers\Tenant\BranchController::class, 'removeEmployee'])->name('tenant.branches.remove-employee');
                    Route::post('branches/{branch}/assign-hr', [App\Http\Controllers\Tenant\BranchController::class, 'assignHR'])->name('tenant.branches.assign-hr');

                    // Documents
                    Route::get('documents', [App\Http\Controllers\Tenant\DocumentController::class, 'index'])->name('tenant.documents.index');
                    Route::get('documents/create', [App\Http\Controllers\Tenant\DocumentController::class, 'create'])->name('tenant.documents.create');
                    Route::post('documents', [App\Http\Controllers\Tenant\DocumentController::class, 'store'])->name('tenant.documents.store');
                    Route::get('documents/{document}', [App\Http\Controllers\Tenant\DocumentController::class, 'show'])->name('tenant.documents.show');
                    Route::get('documents/{document}/download', [App\Http\Controllers\Tenant\DocumentController::class, 'download'])->name('tenant.documents.download');
                    Route::delete('documents/{document}', [App\Http\Controllers\Tenant\DocumentController::class, 'destroy'])->name('tenant.documents.destroy');
                    Route::post('documents/{document}/acknowledge', [App\Http\Controllers\Tenant\DocumentController::class, 'acknowledge'])->name('tenant.documents.acknowledge');
                    Route::get('document-categories', [App\Http\Controllers\Tenant\DocumentController::class, 'categories'])->name('tenant.documents.categories');
                    Route::post('document-categories', [App\Http\Controllers\Tenant\DocumentController::class, 'storeCategory'])->name('tenant.documents.categories.store');
                    Route::delete('document-categories/{category}', [App\Http\Controllers\Tenant\DocumentController::class, 'destroyCategory'])->name('tenant.documents.categories.destroy');

                    // Asset Categories
                    Route::get('asset-categories', [App\Http\Controllers\Tenant\AssetController::class, 'categories'])->name('tenant.assets.categories');
                    Route::post('asset-categories', [App\Http\Controllers\Tenant\AssetController::class, 'storeCategory'])->name('tenant.assets.categories.store');
                    Route::delete('asset-categories/{category}', [App\Http\Controllers\Tenant\AssetController::class, 'destroyCategory'])->name('tenant.assets.categories.destroy');

                    // Assets
                    Route::get('assets', [App\Http\Controllers\Tenant\AssetController::class, 'index'])->name('tenant.assets.index');
                    Route::get('assets/create', [App\Http\Controllers\Tenant\AssetController::class, 'create'])->name('tenant.assets.create');
                    Route::post('assets', [App\Http\Controllers\Tenant\AssetController::class, 'store'])->name('tenant.assets.store');
                    Route::get('assets/{asset}', [App\Http\Controllers\Tenant\AssetController::class, 'show'])->name('tenant.assets.show');
                    Route::get('assets/{asset}/edit', [App\Http\Controllers\Tenant\AssetController::class, 'edit'])->name('tenant.assets.edit');
                    Route::put('assets/{asset}', [App\Http\Controllers\Tenant\AssetController::class, 'update'])->name('tenant.assets.update');
                    Route::post('assets/{asset}/assign', [App\Http\Controllers\Tenant\AssetController::class, 'assign'])->name('tenant.assets.assign');
                    Route::post('assets/{asset}/return', [App\Http\Controllers\Tenant\AssetController::class, 'returnAsset'])->name('tenant.assets.return');
                    Route::delete('assets/{asset}', [App\Http\Controllers\Tenant\AssetController::class, 'destroy'])->name('tenant.assets.destroy');

                    // Contracts
                    Route::get('contracts', [App\Http\Controllers\Tenant\ContractController::class, 'index'])->name('tenant.contracts.index');
                    Route::get('contracts/create', [App\Http\Controllers\Tenant\ContractController::class, 'create'])->name('tenant.contracts.create');
                    Route::post('contracts', [App\Http\Controllers\Tenant\ContractController::class, 'store'])->name('tenant.contracts.store');
                    Route::get('contracts/{contract}', [App\Http\Controllers\Tenant\ContractController::class, 'show'])->name('tenant.contracts.show');
                    Route::get('contracts/{contract}/edit', [App\Http\Controllers\Tenant\ContractController::class, 'edit'])->name('tenant.contracts.edit');
                    Route::get('contracts/{contract}/download', [App\Http\Controllers\Tenant\ContractController::class, 'download'])->name('tenant.contracts.download');
                    Route::put('contracts/{contract}', [App\Http\Controllers\Tenant\ContractController::class, 'update'])->name('tenant.contracts.update');
                    Route::delete('contracts/{contract}', [App\Http\Controllers\Tenant\ContractController::class, 'destroy'])->name('tenant.contracts.destroy');

                    // Subscription
                    Route::get('subscription', [App\Http\Controllers\Tenant\SubscriptionController::class, 'index'])->name('tenant.subscription.index');
                    Route::get('subscription/plans', [App\Http\Controllers\Tenant\SubscriptionController::class, 'plans'])->name('tenant.subscription.plans');
                    Route::get('subscription/checkout', [App\Http\Controllers\Tenant\SubscriptionController::class, 'checkoutGet'])->name('tenant.subscription.checkout.get');
                    Route::post('subscription/checkout', [App\Http\Controllers\Tenant\SubscriptionController::class, 'checkout'])->name('tenant.subscription.checkout');
                    Route::post('subscription/pay', [App\Http\Controllers\Tenant\SubscriptionController::class, 'submitPayment'])->name('tenant.subscription.pay');
                    Route::get('subscription/invoice/{invoice}', [App\Http\Controllers\Tenant\SubscriptionController::class, 'invoice'])->name('tenant.subscription.invoice');

                    // Wallet
                    Route::get('wallet', [App\Http\Controllers\Tenant\WalletController::class, 'index'])->name('tenant.wallet.index');
                    Route::get('wallet/top-up', [App\Http\Controllers\Tenant\WalletController::class, 'topUp'])->name('tenant.wallet.top-up');
                    Route::post('wallet/top-up', [App\Http\Controllers\Tenant\WalletController::class, 'submitTopUp'])->name('tenant.wallet.submit-top-up');
                    Route::post('wallet/stk-push', [App\Http\Controllers\Tenant\DarajaController::class, 'stkPush'])->name('tenant.wallet.stk-push');

                    // Announcements
                    Route::get('announcements', [AnnouncementController::class, 'index'])->name('tenant.announcements.index');
                    Route::get('announcements/create', [AnnouncementController::class, 'create'])->name('tenant.announcements.create');
                    Route::post('announcements', [AnnouncementController::class, 'store'])->name('tenant.announcements.store');
                    Route::delete('announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('tenant.announcements.destroy');

                    // Support Tickets
                    Route::get('support', [TicketController::class, 'index'])->name('tenant.support.index');
                    Route::get('support/create', [TicketController::class, 'create'])->name('tenant.support.create');
                    Route::post('support', [TicketController::class, 'store'])->name('tenant.support.store');
                    Route::get('support/{ticket}', [TicketController::class, 'show'])->name('tenant.support.show');

                    // Employee Self-Service Portal
                    Route::prefix('my')->name('tenant.employee.')->group(function () {
                        Route::get('dashboard', [SelfServiceController::class, 'dashboard'])->name('dashboard');
                        Route::get('leave', [SelfServiceController::class, 'leave'])->name('leave');
                        Route::get('payslips', [SelfServiceController::class, 'payslips'])->name('payslips');
                        Route::get('training', [SelfServiceController::class, 'training'])->name('training');
                        Route::get('profile', [SelfServiceController::class, 'profile'])->name('profile');
                        Route::get('notifications', [SelfServiceController::class, 'notifications'])->name('notifications');
                        Route::get('onboarding', [SelfServiceController::class, 'onboarding'])->name('onboarding');
                        Route::get('performance', [SelfServiceController::class, 'performanceReviews'])->name('performance');
                    });

                    // Notifications
                    Route::get('notifications', [NotificationController::class, 'index'])->name('tenant.notifications.index');
                    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('tenant.notifications.mark-all-read');
                    Route::post('notifications/destroy-all', [NotificationController::class, 'destroyAll'])->name('tenant.notifications.destroy-all');
                    Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('tenant.notifications.read');
                    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('tenant.notifications.destroy');

                    // Audit Logs
                    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('tenant.audit.index');

                    // Exports
                    Route::get('exports/employees', [ExportController::class, 'employees'])->name('tenant.exports.employees');
                    Route::get('exports/leave-requests', [ExportController::class, 'leaveRequests'])->name('tenant.exports.leave-requests');
                    Route::get('exports/payroll', [ExportController::class, 'payroll'])->name('tenant.exports.payroll');

                    // Settings
                    Route::get('settings', [SettingsController::class, 'index'])->name('tenant.settings.index');
                    Route::post('settings', [SettingsController::class, 'update'])->name('tenant.settings.update');
                    Route::post('settings/password', [SettingsController::class, 'updatePassword'])->name('tenant.settings.password');
                    Route::post('settings/departments', [SettingsController::class, 'updateDepartments'])->name('tenant.settings.departments');
                    Route::post('settings/leave-policy', [SettingsController::class, 'updateLeavePolicy'])->name('tenant.settings.leave-policy');
                    Route::post('settings/payroll', [SettingsController::class, 'updatePayrollSettings'])->name('tenant.settings.payroll');
                    Route::post('settings/holidays', [SettingsController::class, 'updatePublicHolidays'])->name('tenant.settings.holidays');
                    Route::post('settings/logo', [SettingsController::class, 'updateLogo'])->name('tenant.settings.logo');
                    Route::post('settings/certificate', [SettingsController::class, 'updateCertificateSettings'])->name('tenant.settings.certificate');

                    // Users
                    Route::resource('users', UserController::class)->names([
                        'index'   => 'tenant.users.index',
                        'create'  => 'tenant.users.create',
                        'store'   => 'tenant.users.store',
                        'edit'    => 'tenant.users.edit',
                        'update'  => 'tenant.users.update',
                        'destroy' => 'tenant.users.destroy',
                    ]);
                    Route::post('users/invitations/{invitation}/resend', [UserController::class, 'resendInvitation'])->name('tenant.users.invitations.resend');

                    // Reports
                    Route::get('reports', [ReportController::class, 'index'])->name('tenant.reports.index');
                    Route::get('reports/headcount', [ReportController::class, 'headcount'])->name('tenant.reports.headcount');
                    Route::get('reports/payroll', [ReportController::class, 'payroll'])->name('tenant.reports.payroll');
                    Route::get('reports/leave', [ReportController::class, 'leave'])->name('tenant.reports.leave');
                    Route::get('reports/compliance', [ReportController::class, 'compliance'])->name('tenant.reports.compliance');
                    Route::get('reports/training', [ReportController::class, 'training'])->name('tenant.reports.training');

                    // Employees
                    Route::resource('employees', EmployeeController::class)->names([
                        'index'   => 'tenant.employees.index',
                        'create'  => 'tenant.employees.create',
                        'store'   => 'tenant.employees.store',
                        'show'    => 'tenant.employees.show',
                        'edit'    => 'tenant.employees.edit',
                        'update'  => 'tenant.employees.update',
                        'destroy' => 'tenant.employees.destroy',
                    ]);

                    // Onboarding
                    Route::get('employees/{employee}/onboarding', [OnboardingController::class, 'show'])->name('tenant.onboarding.show');
                    Route::post('employees/{employee}/onboarding', [OnboardingController::class, 'store'])->name('tenant.onboarding.store');
                    Route::post('onboarding/{item}/toggle', [OnboardingController::class, 'toggle'])->name('tenant.onboarding.toggle');
                    Route::delete('onboarding/{item}', [OnboardingController::class, 'destroy'])->name('tenant.onboarding.destroy');

                    // Leave Types
                    Route::resource('leave-types', LeaveTypeController::class)->names([
                        'index'   => 'tenant.leave-types.index',
                        'create'  => 'tenant.leave-types.create',
                        'store'   => 'tenant.leave-types.store',
                        'edit'    => 'tenant.leave-types.edit',
                        'update'  => 'tenant.leave-types.update',
                        'destroy' => 'tenant.leave-types.destroy',
                    ]);

                    // Leave Requests
                    Route::resource('leave-requests', LeaveRequestController::class)->names([
                        'index'   => 'tenant.leave-requests.index',
                        'create'  => 'tenant.leave-requests.create',
                        'store'   => 'tenant.leave-requests.store',
                        'show'    => 'tenant.leave-requests.show',
                        'destroy' => 'tenant.leave-requests.destroy',
                    ]);
                    Route::post('leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('tenant.leave-requests.approve');
                    Route::post('leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('tenant.leave-requests.reject');

                    // Payroll
                    Route::get('payroll', [PayrollController::class, 'index'])->name('tenant.payroll.index');
                    Route::get('payroll/create', [PayrollController::class, 'create'])->name('tenant.payroll.create');
                    Route::post('payroll', [PayrollController::class, 'store'])->name('tenant.payroll.store');
                    Route::get('payroll/{payroll}', [PayrollController::class, 'show'])->name('tenant.payroll.show');
                    Route::post('payroll/{payroll}/approve', [PayrollController::class, 'approve'])->name('tenant.payroll.approve');
                    Route::post('payroll/records/{record}/update', [PayrollController::class, 'updateRecord'])->name('tenant.payroll.record.update');

                    // Payslips
                    Route::get('payroll/{payroll}/payslip/{record}/download', [PayslipController::class, 'download'])->name('tenant.payslip.download');
                    Route::get('payroll/{payroll}/payslip/all/download', [PayslipController::class, 'downloadAll'])->name('tenant.payslip.download-all');

                    // Performance
                    Route::get('performance', [PerformanceController::class, 'index'])->name('tenant.performance.index');
                    Route::get('performance/create', [PerformanceController::class, 'create'])->name('tenant.performance.create');
                    Route::post('performance', [PerformanceController::class, 'store'])->name('tenant.performance.store');
                    Route::get('performance/{performance}', [PerformanceController::class, 'show'])->name('tenant.performance.show');
                    Route::post('performance/{performance}', [PerformanceController::class, 'update'])->name('tenant.performance.update');
                    Route::delete('performance/{performance}', [PerformanceController::class, 'destroy'])->name('tenant.performance.destroy');
                    Route::post('performance/goals/store', [PerformanceController::class, 'storeGoal'])->name('tenant.performance.goals.store');
                    Route::post('performance/goals/{goal}/update', [PerformanceController::class, 'updateGoal'])->name('tenant.performance.goals.update');

                    // Licenses
                    Route::resource('licenses', LicenseController::class)->names([
                        'index'   => 'tenant.licenses.index',
                        'create'  => 'tenant.licenses.create',
                        'store'   => 'tenant.licenses.store',
                        'edit'    => 'tenant.licenses.edit',
                        'update'  => 'tenant.licenses.update',
                        'destroy' => 'tenant.licenses.destroy',
                    ]);

                    // Recruitment
                    Route::resource('positions', JobPositionController::class)->names([
                        'index'   => 'tenant.positions.index',
                        'create'  => 'tenant.positions.create',
                        'store'   => 'tenant.positions.store',
                        'show'    => 'tenant.positions.show',
                        'edit'    => 'tenant.positions.edit',
                        'update'  => 'tenant.positions.update',
                        'destroy' => 'tenant.positions.destroy',
                    ]);
                    Route::get('positions/{position}/applicants/create', [ApplicantController::class, 'create'])->name('tenant.applicants.create');
                    Route::post('positions/{position}/applicants', [ApplicantController::class, 'store'])->name('tenant.applicants.store');
                    Route::get('applicants/{applicant}', [ApplicantController::class, 'show'])->name('tenant.applicants.show');
                    Route::post('applicants/{applicant}/stage', [ApplicantController::class, 'updateStage'])->name('tenant.applicants.stage');
                    Route::delete('applicants/{applicant}', [ApplicantController::class, 'destroy'])->name('tenant.applicants.destroy');

                    // Disciplinary
                    Route::resource('disciplinary', DisciplinaryController::class)->names([
                        'index'   => 'tenant.disciplinary.index',
                        'create'  => 'tenant.disciplinary.create',
                        'store'   => 'tenant.disciplinary.store',
                        'show'    => 'tenant.disciplinary.show',
                        'destroy' => 'tenant.disciplinary.destroy',
                    ]);
                    Route::post('disciplinary/{disciplinary}', [DisciplinaryController::class, 'update'])->name('tenant.disciplinary.update');

                    // Grievances
                    Route::resource('grievances', GrievanceController::class)->names([
                        'index'   => 'tenant.grievances.index',
                        'create'  => 'tenant.grievances.create',
                        'store'   => 'tenant.grievances.store',
                        'show'    => 'tenant.grievances.show',
                        'destroy' => 'tenant.grievances.destroy',
                    ]);
                    Route::post('grievances/{grievance}', [GrievanceController::class, 'update'])->name('tenant.grievances.update');

                    // Training
                    Route::get('training', [TrainingController::class, 'index'])->name('tenant.training.index');
                    Route::get('training/create', [TrainingController::class, 'create'])->name('tenant.training.create');
                    Route::post('training', [TrainingController::class, 'store'])->name('tenant.training.store');
                    Route::get('training/{training}', [TrainingController::class, 'show'])->name('tenant.training.show');
                    Route::post('training/{training}/status', [TrainingController::class, 'update'])->name('tenant.training.update');
                    Route::delete('training/{training}', [TrainingController::class, 'destroy'])->name('tenant.training.destroy');
                    Route::post('training/{training}/enroll', [TrainingController::class, 'enroll'])->name('tenant.training.enroll');
                    Route::post('training/enrollments/{enrollment}/update', [TrainingController::class, 'updateEnrollment'])->name('tenant.training.enrollment.update');
                    Route::get('training/enrollments/{enrollment}/certificate', [TrainingController::class, 'certificate'])->name('tenant.training.certificate');
                });
            });
        }
    });












































