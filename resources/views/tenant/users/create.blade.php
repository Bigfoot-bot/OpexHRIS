@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Add User')
@section('page-subtitle', 'Create a new portal user or send an invitation')

@section('page-actions')
    <a href="{{ route('tenant.users.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Users
    </a>
@endsection

@section('content')

<div class="max-w-2xl">

    @if($errors->any())
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tenant.users.store') }}" id="user-form">
        @csrf

        {{-- Invite Toggle --}}
        <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">How would you like to add this user?</h2>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-start gap-3 p-4 border-2 rounded-lg cursor-pointer transition-colors"
                       id="invite-option"
                       onclick="setMode('invite')">
                    <input type="radio" name="send_invite" value="1" checked class="mt-0.5"/>
                    <div>
                        <p class="text-sm font-medium text-emerald-900">Send Invitation</p>
                        <p class="text-xs text-gray-400 mt-0.5">User sets their own password via email link</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-4 border-2 rounded-lg cursor-pointer transition-colors"
                       id="manual-option"
                       onclick="setMode('manual')">
                    <input type="radio" name="send_invite" value="0" class="mt-0.5"/>
                    <div>
                        <p class="text-sm font-medium text-emerald-900">Set Password Manually</p>
                        <p class="text-xs text-gray-400 mt-0.5">You set the password directly</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">User Details</h2>

            <div class="space-y-4">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Role *</label>
                    <select name="role" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Link to Employee Profile</label>
                    <select name="employee_id"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">— Not linked —</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }} ({{ $employee->employee_number }}) — {{ $employee->job_title }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Link to enable Employee Self-Service Portal</p>
                </div>

                {{-- Password fields (hidden when invite mode) --}}
                <div id="password-fields">
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Password *</label>
                        <input type="password" name="password"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Confirm Password *</label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>

            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" id="submit-btn"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors duration-150">
                Send Invitation
            </button>
            <a href="{{ route('tenant.users.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600">Cancel</a>
        </div>

    </form>
</div>

<script>
    function setMode(mode) {
        const passwordFields = document.getElementById('password-fields');
        const submitBtn = document.getElementById('submit-btn');
        const inviteOption = document.getElementById('invite-option');
        const manualOption = document.getElementById('manual-option');

        if (mode === 'invite') {
            passwordFields.style.display = 'none';
            submitBtn.textContent = 'Send Invitation';
            inviteOption.classList.add('border-emerald-500', 'bg-emerald-50');
            inviteOption.classList.remove('border-gray-200');
            manualOption.classList.remove('border-emerald-500', 'bg-emerald-50');
            manualOption.classList.add('border-gray-200');
        } else {
            passwordFields.style.display = 'block';
            submitBtn.textContent = 'Create User';
            manualOption.classList.add('border-emerald-500', 'bg-emerald-50');
            manualOption.classList.remove('border-gray-200');
            inviteOption.classList.remove('border-emerald-500', 'bg-emerald-50');
            inviteOption.classList.add('border-gray-200');
        }
    }

    // Initialize
    setMode('invite');
</script>

@endsection
