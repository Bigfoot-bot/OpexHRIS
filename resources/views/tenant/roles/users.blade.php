@extends('tenant.layouts.app')
@section('page-title', 'Assign Roles to Users')
@section('page-subtitle', 'Manage user role assignments')
@section('page-actions')
    <a href="{{ route('tenant.roles.index') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">
        Back to Roles
    </a>
@endsection
@section('content')
<div class="space-y-4">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">User</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Current Roles</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Assign Role</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                        @if($user->is_admin)
                            <span class="text-xs bg-purple-50 text-purple-700 px-2 py-0.5 rounded-full">Admin</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @forelse($user->tenantRoles as $userRole)
                                <div class="flex items-center gap-1">
                                    <span class="text-xs bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full">{{ $userRole->role->name ?? 'Unknown' }}</span>
                                    <form method="POST" action="{{ route('tenant.roles.revoke') }}" style="display:inline">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}"/>
                                        <input type="hidden" name="role_id" value="{{ $userRole->role_id }}"/>
                                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs">x</button>
                                    </form>
                                </div>
                            @empty
                                <span class="text-xs text-gray-400">No roles assigned</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if(!$user->is_admin)
                        <form method="POST" action="{{ route('tenant.roles.assign') }}" class="flex gap-2">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}"/>
                            <select name="role_id" required class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-emerald-700 text-white text-xs px-3 py-1.5 rounded-lg">Assign</button>
                        </form>
                        @else
                            <span class="text-xs text-gray-400">Admin has all permissions</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
