@extends('central.layouts.app')

@section('page-title', 'Facilities')
@section('page-subtitle', 'Manage all onboarded healthcare facilities')

@section('page-actions')
    <a href="{{ route('admin.onboarding.step1') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        + Add Facility
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-4">
            <p class="text-xs text-gray-400 mb-1">Total</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $tenants->total() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-4">
            <p class="text-xs text-gray-400 mb-1">Active</p>
            <p class="text-2xl font-medium text-emerald-600">{{ $tenants->where('is_active', true)->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-4">
            <p class="text-xs text-gray-400 mb-1">On Trial</p>
            <p class="text-2xl font-medium text-amber-600">{{ $tenants->filter(fn($t) => $t->trial_ends_at && $t->trial_ends_at->isFuture())->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-4">
            <p class="text-xs text-gray-400 mb-1">Suspended</p>
            <p class="text-2xl font-medium text-red-500">{{ $tenants->where('is_active', false)->count() }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-green-100">

        @if($tenants->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No facilities onboarded yet.</p>
                <a href="{{ route('admin.onboarding.step1') }}"
                   class="inline-block mt-3 text-sm text-emerald-600 hover:text-emerald-800">
                    Add your first facility →
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Facility</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Plan</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Domain</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employees</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Trial Ends</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($tenants as $tenant)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-emerald-900">{{ $tenant->name }}</p>
                            <p class="text-xs text-gray-400">{{ $tenant->email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $planColors = [
                                    'basic'        => 'bg-gray-50 text-gray-500',
                                    'professional' => 'bg-blue-50 text-blue-600',
                                    'enterprise'   => 'bg-emerald-50 text-emerald-600',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $planColors[$tenant->subscription_plan] ?? 'bg-gray-50 text-gray-500' }} capitalize">
                                {{ $tenant->subscription_plan }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="http://{{ $tenant->slug }}.hris-platform.test"
                               target="_blank"
                               class="text-xs text-emerald-600 hover:text-emerald-800">
                                {{ $tenant->slug }}.hris-platform.test ↗
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $empCount = \App\Models\Tenant\Employee::withoutGlobalScopes()
                                    ->where('tenant_id', $tenant->id)->count();
                            @endphp
                            <span class="text-sm text-gray-600">{{ $empCount }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($tenant->trial_ends_at)
                                @if($tenant->trial_ends_at->isFuture())
                                    <span class="text-xs text-amber-600">{{ $tenant->trial_ends_at->format('M d, Y') }}</span>
                                @else
                                    <span class="text-xs text-red-500">Expired {{ $tenant->trial_ends_at->diffForHumans() }}</span>
                                @endif
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($tenant->is_active)
                                <span class="text-xs bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full">Active</span>
                            @else
                                <span class="text-xs bg-red-50 text-red-500 px-2.5 py-1 rounded-full">Suspended</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.tenants.show', $tenant) }}"
                                   class="text-xs text-emerald-600 hover:text-emerald-800">View</a>
                                <form method="POST" action="{{ route('admin.tenants.toggle-status', $tenant) }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs {{ $tenant->is_active ? 'text-amber-500 hover:text-amber-700' : 'text-emerald-600 hover:text-emerald-800' }}">
                                        {{ $tenant->is_active ? 'Suspend' : 'Activate' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}"
                                      onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($tenants->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $tenants->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection