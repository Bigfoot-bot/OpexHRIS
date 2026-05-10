@extends((auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0) && !auth()->user()->isInEmployeePortal() ? 'tenant.layouts.app' : 'tenant.employee.layouts.app')
@section('page-title', 'Contracts')
@section('page-subtitle', 'Manage employee contracts')
@section('page-actions')
    @if(!auth()->user()->isInEmployeePortal() && (auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0))
    <a href="{{ route('tenant.contracts.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + New Contract
    </a>
    @endif
@endsection
@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    {{-- Expiring Soon --}}
    @if($expiring->isNotEmpty())
    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4">
        <p class="text-amber-700 font-semibold text-sm mb-2">?? {{ $expiring->count() }} contract(s) expiring within 30 days</p>
        <div class="space-y-1">
            @foreach($expiring as $contract)
            <p class="text-xs text-amber-600">
                {{ $contract->employee->first_name ?? 'N/A' }} {{ $contract->employee->last_name ?? '' }} �
                {{ $contract->title }} � Expires {{ $contract->end_date->format('M d, Y') }}
                ({{ $contract->daysUntilExpiry() }} days)
            </p>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search contracts..."
                   class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            <select name="status" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
            </select>
            <button type="submit" class="bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
        </form>
    </div>

    {{-- Contracts Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($contracts->isEmpty())
            <div class="p-12 text-center">
                @if(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0)
                <a href="{{ route('tenant.contracts.create') }}" class="mt-2 inline-block text-emerald-600 text-sm hover:underline">Create first contract</a>
                @endif
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Contract</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Duration</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($contracts as $contract)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-800">{{ $contract->employee->first_name ?? 'N/A' }} {{ $contract->employee->last_name ?? '' }}</p>
                            <p class="text-xs text-gray-400">{{ $contract->employee->employee_id ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-800">{{ $contract->title }}</p>
                            <p class="text-xs text-gray-400">{{ $contract->job_title ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $contract->contract_type) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $contract->start_date->format('M d, Y') }}
                            {{ $contract->end_date ? '? ' . $contract->end_date->format('M d, Y') : '? Permanent' }}
                        </td>
                        <td class="px-6 py-4">
                            @php $cColors = ['active' => 'bg-emerald-50 text-emerald-700', 'expired' => 'bg-red-50 text-red-600', 'terminated' => 'bg-gray-100 text-gray-500', 'pending' => 'bg-amber-50 text-amber-600']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $cColors[$contract->status] ?? '' }} capitalize">{{ $contract->status }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('tenant.contracts.show', $contract) }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">View</a>
                                @if(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0)
                                <a href="{{ route('tenant.contracts.edit', $contract) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $contracts->links() }}</div>
        @endif
    </div>
</div>
@endsection







