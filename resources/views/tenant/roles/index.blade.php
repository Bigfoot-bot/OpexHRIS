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
