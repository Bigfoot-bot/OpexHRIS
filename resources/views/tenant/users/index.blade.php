@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'User Management')
@section('page-subtitle', 'Manage portal users and their roles')

@section('page-actions')
    <a href="{{ route('tenant.users.create') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        + Add User
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('error') }}
        </div>
    @endif

    {{-- Roles Summary --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        @foreach($roles as $role)
        <div class="bg-white rounded-xl border border-green-100 p-4">
            <p class="text-xs text-gray-400 mb-1">{{ $role->name }}</p>
            <p class="text-2xl font-medium text-emerald-900">
                {{ $users->filter(fn($u) => $u->hasRole($role->name))->count() }}
            </p>
            <p class="text-xs text-gray-400 mt-1">{{ $role->permissions->count() }} permissions</p>
        </div>
        @endforeach
    </div>

    {{-- Pending Invitations --}}
    @if($invitations->isNotEmpty())
    <div class="bg-amber-50 rounded-xl border border-amber-100 mb-6">
        <div class="px-6 py-4 border-b border-amber-100">
            <h2 class="text-sm font-medium text-amber-700">⏳ Pending Invitations ({{ $invitations->count() }})</h2>
        </div>
        <table class="w-full">
            <thead>
                <tr class="border-b border-amber-100">
                    <th class="text-left text-xs text-amber-600 font-medium px-6 py-3">Name</th>
                    <th class="text-left text-xs text-amber-600 font-medium px-6 py-3">Email</th>
                    <th class="text-left text-xs text-amber-600 font-medium px-6 py-3">Role</th>
                    <th class="text-left text-xs text-amber-600 font-medium px-6 py-3">Expires</th>
                    <th class="text-left text-xs text-amber-600 font-medium px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-amber-50">
                @foreach($invitations as $invitation)
                <tr class="hover:bg-amber-50/50">
                    <td class="px-6 py-3 text-sm text-gray-700">{{ $invitation->name }}</td>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $invitation->email }}</td>
                    <td class="px-6 py-3">
                        <span class="text-xs px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">
                            {{ $invitation->role }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-xs text-amber-600">
                        {{ $invitation->expires_at->format('M d, Y H:i') }}
                    </td>
                    <td class="px-6 py-3">
                        <form method="POST" action="{{ route('tenant.users.invitations.resend', $invitation) }}">
                            @csrf
                            <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800">
                                Resend
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Users Table --}}
    <div class="bg-white rounded-xl border border-green-100">

        @if($users->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No users yet.</p>
                <a href="{{ route('tenant.users.create') }}"
                   class="inline-block mt-3 text-sm text-emerald-600 hover:text-emerald-800">
                    Add first user →
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">User</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Role</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Last Login</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-medium">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-emerald-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @foreach($user->roles as $role)
                                <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                            @if($user->roles->isEmpty())
                                <span class="text-xs text-gray-400">No role</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($user->employee)
                                <p class="text-xs text-gray-600">{{ $user->employee->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->employee->employee_number }}</p>
                            @else
                                <span class="text-xs text-gray-400">Not linked</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($user->status === 'active')
                                <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">Active</span>
                            @else
                                <span class="text-xs px-2.5 py-1 rounded-full bg-red-50 text-red-500">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-gray-400">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($user->id !== auth()->id())
                                    <a href="{{ route('tenant.users.edit', $user) }}"
                                       class="text-xs text-blue-500 hover:text-blue-700">Edit</a>
                                    <form method="POST" action="{{ route('tenant.users.destroy', $user) }}"
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">Delete</button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">You</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>

@endsection
