@extends('tenant.layouts.app')
@section('page-title', 'Roles & Permissions')
@section('page-subtitle', 'Manage roles and assign permissions')
@section('page-actions')
    <a href="{{ route('tenant.roles.users') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50">
        Assign Roles to Users
    </a>
@endsection
@section('content')
<div class="space-y-6">

{{-- Admin Role --}}
<div class="bg-white rounded-2xl border border-purple-100 shadow-sm p-6">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div>
            <h2 class="text-sm font-semibold text-gray-800">Admin Role</h2>
            <p class="text-xs text-gray-400">Admins have full access to all HR features, settings, and user management</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-2 gap-8">

        {{-- Assign --}}
        <div>
            <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Assign Admin to Employee</h3>
            @php $assignedEmployeeIds = $admins->pluck('employee_id')->filter()->toArray(); @endphp
            <form method="POST" action="{{ route('tenant.roles.admin.assign') }}" class="flex gap-2">
                @csrf
                <select name="employee_id" required
                        class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Select employee…</option>
                    @foreach($employees as $emp)
                        @if(!in_array($emp->id, $assignedEmployeeIds))
                        <option value="{{ $emp->id }}">{{ $emp->full_name }} — {{ $emp->job_title }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit"
                        class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium px-4 py-2 rounded-lg whitespace-nowrap">
                    Assign
                </button>
            </form>
            <p class="text-xs text-gray-400 mt-2">Only employees with an existing user account can be made admin.</p>
        </div>

        {{-- Current admins --}}
        <div>
            <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Current Admins</h3>
            @forelse($admins as $admin)
            <div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 text-xs font-semibold flex-shrink-0">
                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $admin->name }}</p>
                        <p class="text-xs text-gray-400">{{ $admin->email }}</p>
                    </div>
                </div>
                @if($admin->id !== auth()->id())
                <form method="POST" action="{{ route('tenant.roles.admin.revoke') }}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $admin->id }}"/>
                    <button type="submit"
                            onclick="return confirm('Remove admin access for {{ $admin->name }}?')"
                            class="text-xs text-red-500 hover:text-red-700 font-medium">
                        Revoke
                    </button>
                </form>
                @else
                <span class="text-xs text-gray-400 italic">You</span>
                @endif
            </div>
            @empty
            <p class="text-xs text-gray-400">No admins assigned yet.</p>
            @endforelse
        </div>

    </div>
</div>

<div class="grid grid-cols-2 gap-6">

    {{-- Create Role --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Create New Role</h2>
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('tenant.roles.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Role Name *</label>
                <input type="text" name="name" required placeholder="e.g. HR Officer, Finance Manager"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
                <input type="text" name="description" placeholder="Brief description of this role"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-3">Permissions</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($allPermissions as $key => $label)
                    <label class="flex items-center gap-2 text-sm text-gray-600 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $key }}" class="rounded text-emerald-600"/>
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Create Role
            </button>
        </form>
    </div>

    {{-- Existing Roles --}}
    <div class="space-y-4">
        <h2 class="text-sm font-semibold text-gray-800">Existing Roles</h2>
        @if($roles->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                <p class="text-gray-400 text-sm">No roles created yet.</p>
            </div>
        @else
            @foreach($roles as $role)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ $role->name }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $role->description ?? 'No description' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $role->users_count }} user(s) assigned</p>
                    </div>
                    <form method="POST" action="{{ route('tenant.roles.destroy', $role) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700" onclick="return confirm('Delete this role?')">Delete</button>
                    </form>
                </div>
                <div class="flex flex-wrap gap-1 mb-3">
                    @foreach($role->permissions as $perm)
                        <span class="text-xs bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full">{{ $allPermissions[$perm->permission] ?? $perm->permission }}</span>
                    @endforeach
                </div>

                {{-- Edit Role --}}
                <details class="mt-2">
                    <summary class="text-xs text-blue-600 cursor-pointer hover:text-blue-800">Edit Permissions</summary>
                    <form method="POST" action="{{ route('tenant.roles.update', $role) }}" class="mt-3 space-y-3">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $role->name }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        <div class="grid grid-cols-2 gap-1">
                            @foreach($allPermissions as $key => $label)
                            <label class="flex items-center gap-2 text-xs text-gray-600 p-1.5 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="permissions[]" value="{{ $key }}"
                                       {{ $role->permissions->pluck('permission')->contains($key) ? 'checked' : '' }}
                                       class="rounded text-emerald-600"/>
                                {{ $label }}
                            </label>
                            @endforeach
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-2 rounded-lg">
                            Update Role
                        </button>
                    </form>
                </details>
            </div>
            @endforeach
        @endif
    </div>
</div>

</div>{{-- end space-y-6 --}}
@endsection
