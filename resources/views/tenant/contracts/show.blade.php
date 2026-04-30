@extends(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0 ? 'tenant.layouts.app' : 'tenant.employee.layouts.app')
@section('page-title', $contract->title)
@section('page-subtitle', 'Contract details')
@section('page-actions')
@if(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0)
    <div class="flex gap-2">
        <a href="{{ route('tenant.contracts.edit', $contract) }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">Edit</a>
        @if($contract->file_path)
            <a href="{{ route('tenant.contracts.download', $contract) }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Download</a>
        @endif
    </div>
@else
    @if($contract->file_path)
        <a href="{{ asset($contract->file_path) }}" target="_blank" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Download</a>
    @endif
@endif
@endsection
            <div><p class="text-xs text-gray-400">Employee</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $contract->employee->first_name ?? 'N/A' }} {{ $contract->employee->last_name ?? '' }}</p></div>
            <div><p class="text-xs text-gray-400">Contract Type</p><p class="text-sm font-medium text-gray-800 mt-1 capitalize">{{ str_replace('_', ' ', $contract->contract_type) }}</p></div>
            <div><p class="text-xs text-gray-400">Job Title</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $contract->job_title ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-400">Department</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $contract->department ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-400">Start Date</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $contract->start_date->format('M d, Y') }}</p></div>
            <div><p class="text-xs text-gray-400">End Date</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $contract->end_date ? $contract->end_date->format('M d, Y') : 'Permanent' }}</p></div>
            <div><p class="text-xs text-gray-400">Salary</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $contract->salary ? 'KES ' . number_format($contract->salary, 2) : 'N/A' }}</p></div>
            <div>
                <p class="text-xs text-gray-400">Status</p>
                @php $cColors = ['active' => 'bg-emerald-50 text-emerald-700', 'expired' => 'bg-red-50 text-red-600', 'terminated' => 'bg-gray-100 text-gray-500', 'pending' => 'bg-amber-50 text-amber-600']; @endphp
                <span class="text-xs px-2.5 py-1 rounded-full {{ $cColors[$contract->status] ?? '' }} capitalize mt-1 inline-block">{{ $contract->status }}</span>
            </div>
        </div>
        @if($contract->notes)
            <div class="p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-400 mb-1">Notes</p>
                <p class="text-sm text-gray-700">{{ $contract->notes }}</p>
            </div>
        @endif
        @if($contract->end_date && $contract->status === 'active')
            @php $days = $contract->daysUntilExpiry(); @endphp
            @if($days <= 30)
                <div class="bg-amber-50 border border-amber-100 rounded-lg px-4 py-2 text-amber-700 text-sm">
                    ?? This contract expires in {{ $days }} days
                </div>
            @endif
        @endif
    </div>
</div>
@endsection




