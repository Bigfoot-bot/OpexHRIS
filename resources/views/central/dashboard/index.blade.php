@extends('central.layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . auth('super_admin')->user()->name)

@section('page-actions')
    <a href="{{ route('admin.tenants.create') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        + Add Facility
    </a>
@endsection

@section('content')

    {{-- Stats Grid --}}
    <div class="grid grid-cols-4 gap-5 mb-5">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total Facilities</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['total_facilities'] }}</p>
            <p class="text-xs text-emerald-600 mt-1">Registered on platform</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Active Facilities</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['active_facilities'] }}</p>
            <p class="text-xs text-teal-600 mt-1">Currently active</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total Employees</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['total_employees'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Across all facilities</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total Users</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['total_users'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Portal users</p>
        </div>
    </div>

    {{-- Revenue & Plans --}}
    <div class="grid grid-cols-3 gap-5 mb-5">

        {{-- Monthly Revenue --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Monthly Revenue (Est.)</h2>
            <p class="text-3xl font-medium text-emerald-900 mb-4">KES {{ number_format($totalRevenue) }}</p>
            @php
                $dotColors = ['bg-gray-400','bg-blue-400','bg-emerald-500','bg-purple-400','bg-amber-400','bg-rose-400','bg-cyan-400'];
            @endphp
            <div class="space-y-3">
                @foreach($planDistribution as $i => $plan)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $dotColors[$i % count($dotColors)] }}"></div>
                        <span class="text-xs text-gray-500 capitalize">{{ $plan['name'] }} ({{ $plan['count'] }})</span>
                    </div>
                    <span class="text-xs font-medium text-gray-700">KES {{ number_format($plan['revenue']) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Plan Distribution --}}
        @php
            $barColors = ['bg-gray-400','bg-blue-400','bg-emerald-500','bg-purple-400','bg-amber-400','bg-rose-400','bg-cyan-400'];
        @endphp
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Plan Distribution</h2>
            <div class="space-y-4">
                @foreach($planDistribution as $i => $plan)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-xs text-gray-500 capitalize">{{ $plan['name'] }}</span>
                        <span class="text-xs font-medium text-gray-700">{{ $plan['count'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="{{ $barColors[$i % count($barColors)] }} h-2 rounded-full"
                             style="width: {{ $plan['percentage'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Alerts --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Alerts</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg">
                    <div>
                        <p class="text-xs font-medium text-amber-700">On Trial</p>
                        <p class="text-xs text-amber-600">Active trial periods</p>
                    </div>
                    <span class="text-xl font-medium text-amber-700">{{ $stats['trial_facilities'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <div>
                        <p class="text-xs font-medium text-red-700">Expiring Soon</p>
                        <p class="text-xs text-red-500">Within 30 days</p>
                    </div>
                    <span class="text-xl font-medium text-red-700">{{ $stats['expiring_soon'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-xs font-medium text-gray-700">Inactive</p>
                        <p class="text-xs text-gray-500">Suspended facilities</p>
                    </div>
                    <span class="text-xl font-medium text-gray-700">{{ $stats['total_facilities'] - $stats['active_facilities'] }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Facility Usage Table --}}
    <div class="bg-white rounded-xl border border-green-100">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <h2 class="text-sm font-medium text-emerald-900">Facility Usage</h2>
            <a href="{{ route('admin.tenants.index') }}"
               class="text-xs text-emerald-600 hover:text-emerald-800">View all →</a>
        </div>
        @if($tenantUsage->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-400 text-sm">No facilities onboarded yet.</p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Facility</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Plan</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Employees</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Users</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Trial Ends</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($tenantUsage as $tenant)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3">
                            <p class="text-sm font-medium text-emerald-900">{{ $tenant->name }}</p>
                            <p class="text-xs text-gray-400">{{ $tenant->domains->first()?->domain ?? $tenant->slug . '.' . parse_url(config('app.url'), PHP_URL_HOST) }}</p>
                        </td>
                        <td class="px-6 py-3">
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
                        <td class="px-6 py-3">
                            <span class="text-sm text-gray-600">{{ $tenant->employee_count }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm text-gray-600">{{ $tenant->user_count }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-xs text-gray-400">
                                {{ $tenant->trial_ends_at ? $tenant->trial_ends_at->format('M d, Y') : '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            @if($tenant->is_active)
                                <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">Active</span>
                            @else
                                <span class="text-xs px-2.5 py-1 rounded-full bg-red-50 text-red-500">Suspended</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <a href="{{ route('admin.tenants.show', $tenant) }}"
                               class="text-xs text-emerald-600 hover:text-emerald-800">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

@endsection