@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Edit User')
@section('page-subtitle', $user->name)

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

    <form method="POST" action="{{ route('tenant.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">User Details</h2>

            <div class="space-y-4">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Role *</label>
                    <select name="role" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ old('role', $user->roles->first()?->name) === $role->name ? 'selected' : '' }}>
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
                            <option value="{{ $employee->id }}"
                                {{ old('employee_id', $user->employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }} ({{ $employee->employee_number }}) — {{ $employee->job_title }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Link this user to an employee record to enable the Employee Self-Service Portal</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Status *</label>
                    <select name="status" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                @if(auth()->user()->is_admin && auth()->id() !== $user->id)
                <div class="flex items-start justify-between rounded-lg border border-gray-200 px-4 py-3">
                    <div>
                        <p class="text-xs font-medium text-gray-700">Facility Admin</p>
                        <p class="text-xs text-gray-400 mt-0.5">Full access to all HR features, settings, and user management</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-4 flex-shrink-0">
                        <input type="hidden" name="is_admin" value="0"/>
                        <input type="checkbox" name="is_admin" value="1" class="sr-only peer"
                               {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}/>
                        <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>
                @endif

            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors duration-150">
                Update User
            </button>
            <a href="{{ route('tenant.users.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600">Cancel</a>
        </div>

    </form>
</div>

@endsection
