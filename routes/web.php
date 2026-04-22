<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\Auth\LoginController;
use App\Http\Controllers\Central\Auth\PasswordResetController;
use App\Http\Controllers\Central\SuperAdmin\DashboardController;
use App\Http\Controllers\Central\SuperAdmin\TenantController;
use App\Http\Controllers\Central\SuperAdmin\InvoiceController;
use App\Http\Controllers\Central\SuperAdmin\AuditLogController;
use App\Http\Controllers\Central\SuperAdmin\SupportTicketController;
use App\Http\Controllers\Central\SuperAdmin\OnboardingWizardController;
use App\Http\Controllers\Central\SuperAdmin\MailSettingsController;

// Redirect root to login
Route::get('/', fn() => redirect()->route('admin.login'));

// Guest routes
Route::middleware('guest:super_admin')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login.post');

    // Super Admin Password Reset
    Route::get('/admin/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('admin.password.request');
    Route::post('/admin/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('admin.password.email');
    Route::get('/admin/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('admin.password.reset');
    Route::post('/admin/reset-password', [PasswordResetController::class, 'reset'])->name('admin.password.update');
});

// Super Admin authenticated routes
Route::middleware('auth:super_admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // MFA Settings
    Route::get('mfa', [App\Http\Controllers\Central\MfaSettingsController::class, 'index'])->name('mfa.index');
    Route::post('mfa', [App\Http\Controllers\Central\MfaSettingsController::class, 'update'])->name('mfa.update');

    // Integrations
    Route::get('integrations', [App\Http\Controllers\Central\IntegrationsController::class, 'index'])->name('integrations.index');
    Route::post('integrations', [App\Http\Controllers\Central\IntegrationsController::class, 'update'])->name('integrations.update');
    Route::post('integrations/test-sms', [App\Http\Controllers\Central\IntegrationsController::class, 'testSms'])->name('integrations.test-sms');
    // Facilities
    Route::resource('tenants', TenantController::class);
    Route::post('tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])->name('tenants.toggle-status');
    Route::post('tenants/{tenant}/update-plan', [TenantController::class, 'updatePlan'])->name('tenants.update-plan');

    // Onboarding Wizard
    Route::get('onboarding/step1', [OnboardingWizardController::class, 'step1'])->name('onboarding.step1');
    Route::post('onboarding/step1', [OnboardingWizardController::class, 'step1Store'])->name('onboarding.step1.store');
    Route::get('onboarding/step2', [OnboardingWizardController::class, 'step2'])->name('onboarding.step2');
    Route::post('onboarding/step2', [OnboardingWizardController::class, 'step2Store'])->name('onboarding.step2.store');
    Route::get('onboarding/step3', [OnboardingWizardController::class, 'step3'])->name('onboarding.step3');
    Route::post('onboarding/step3', [OnboardingWizardController::class, 'step3Store'])->name('onboarding.step3.store');
    Route::get('onboarding/step4', [OnboardingWizardController::class, 'step4'])->name('onboarding.step4');
    Route::post('onboarding/step4', [OnboardingWizardController::class, 'step4Store'])->name('onboarding.step4.store');
    Route::get('onboarding/step5', [OnboardingWizardController::class, 'step5'])->name('onboarding.step5');
    Route::post('onboarding/complete', [OnboardingWizardController::class, 'complete'])->name('onboarding.complete');

    // Invoices
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::post('invoices/mark-overdue', [InvoiceController::class, 'markOverdue'])->name('invoices.mark-overdue');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
    Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Support Tickets
    Route::get('support', [SupportTicketController::class, 'index'])->name('support.index');
    Route::get('support/{ticket}', [SupportTicketController::class, 'show'])->name('support.show');
    Route::post('support/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('support.reply');
    Route::post('support/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('support.status');

    // Mail Settings
    Route::get('mail-settings', [MailSettingsController::class, 'index'])->name('mail-settings.index');
    Route::post('mail-settings', [MailSettingsController::class, 'update'])->name('mail-settings.update');
    Route::post('mail-settings/test', [MailSettingsController::class, 'sendTest'])->name('mail-settings.test');

    // Wallet Management
    Route::get('wallets', [App\Http\Controllers\Central\SuperAdmin\WalletController::class, 'index'])->name('wallets.index');
    Route::get('wallets/requests', [App\Http\Controllers\Central\SuperAdmin\WalletController::class, 'requests'])->name('wallets.requests');
    Route::post('wallets/requests/{topUpRequest}/approve', [App\Http\Controllers\Central\SuperAdmin\WalletController::class, 'approve'])->name('wallets.approve');
    Route::post('wallets/requests/{topUpRequest}/reject', [App\Http\Controllers\Central\SuperAdmin\WalletController::class, 'reject'])->name('wallets.reject');
    Route::post('wallets/manual-credit', [App\Http\Controllers\Central\SuperAdmin\WalletController::class, 'manualCredit'])->name('wallets.manual-credit');

    // Daraja Settings
    Route::get('daraja-settings', [App\Http\Controllers\Central\SuperAdmin\DarajaSettingsController::class, 'index'])->name('daraja-settings.index');
    Route::post('daraja-settings', [App\Http\Controllers\Central\SuperAdmin\DarajaSettingsController::class, 'update'])->name('daraja-settings.update');
    Route::post('daraja-settings/test', [App\Http\Controllers\Central\SuperAdmin\DarajaSettingsController::class, 'testConnection'])->name('daraja-settings.test');

    // Branding Settings
    Route::get('branding-settings', [App\Http\Controllers\Central\SuperAdmin\BrandingSettingsController::class, 'index'])->name('branding-settings.index');
    Route::post('branding-settings', [App\Http\Controllers\Central\SuperAdmin\BrandingSettingsController::class, 'update'])->name('branding-settings.update');

    // Subscription Plans
    Route::get('subscription-plans', [App\Http\Controllers\Central\SuperAdmin\SubscriptionPlanController::class, 'index'])->name('subscription-plans.index');
    Route::post('subscription-plans', [App\Http\Controllers\Central\SuperAdmin\SubscriptionPlanController::class, 'store'])->name('subscription-plans.store');
    Route::put('subscription-plans/{plan}', [App\Http\Controllers\Central\SuperAdmin\SubscriptionPlanController::class, 'update'])->name('subscription-plans.update');
    Route::delete('subscription-plans/{plan}', [App\Http\Controllers\Central\SuperAdmin\SubscriptionPlanController::class, 'destroy'])->name('subscription-plans.destroy');
    Route::post('subscription-plans/discounts', [App\Http\Controllers\Central\SuperAdmin\SubscriptionPlanController::class, 'updateDiscounts'])->name('subscription-plans.discounts');

    // Subscription Settings & Management
    Route::get('subscription-settings', [App\Http\Controllers\Central\SuperAdmin\SubscriptionSettingsController::class, 'index'])->name('subscription-settings.index');
    Route::post('subscription-settings', [App\Http\Controllers\Central\SuperAdmin\SubscriptionSettingsController::class, 'update'])->name('subscription-settings.update');
    Route::post('subscription-settings/{subscription}/extend', [App\Http\Controllers\Central\SuperAdmin\SubscriptionSettingsController::class, 'extendSubscription'])->name('subscription-settings.extend');
    Route::post('subscription-settings/{subscription}/suspend', [App\Http\Controllers\Central\SuperAdmin\SubscriptionSettingsController::class, 'suspendSubscription'])->name('subscription-settings.suspend');
    Route::post('subscription-settings/{subscription}/cancel', [App\Http\Controllers\Central\SuperAdmin\SubscriptionSettingsController::class, 'cancelSubscription'])->name('subscription-settings.cancel');

    // Subscription Payments
    Route::get('subscription-payments', [App\Http\Controllers\Central\SuperAdmin\SubscriptionPaymentController::class, 'index'])->name('subscription-payments.index');
    Route::post('subscription-payments/{payment}/approve', [App\Http\Controllers\Central\SuperAdmin\SubscriptionPaymentController::class, 'approve'])->name('subscription-payments.approve');
    Route::post('subscription-payments/{payment}/reject', [App\Http\Controllers\Central\SuperAdmin\SubscriptionPaymentController::class, 'reject'])->name('subscription-payments.reject');

    // Announcements
    Route::get('announcements', [App\Http\Controllers\Central\SuperAdmin\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('announcements/create', [App\Http\Controllers\Central\SuperAdmin\AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('announcements', [App\Http\Controllers\Central\SuperAdmin\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('announcements/{announcement}', [App\Http\Controllers\Central\SuperAdmin\AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});









Route::post('/lang', function() { $l= request('locale','en'); if(in_array($l,['en','sw'])){session(['locale'=>$l]);} return back(); })->name('lang.switch')->middleware('web');

