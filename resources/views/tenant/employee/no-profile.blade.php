@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Employee Portal')
@section('page-subtitle', 'Self-service portal')

@section('content')

<div class="flex items-center justify-center h-96">
    <div class="text-center">
        <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h2 class="text-base font-medium text-emerald-900 mb-2">No Employee Profile Linked</h2>
        <p class="text-sm text-gray-400 mb-6">Your user account is not linked to an employee record yet.</p>
        <p class="text-xs text-gray-400">Please contact your HR Administrator to link your account to your employee profile.</p>
    </div>
</div>

@endsection
