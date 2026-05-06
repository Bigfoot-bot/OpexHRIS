@extends('tenant.branch.layout')
@section('page-title', $contract->title)
@section('page-subtitle', $contract->employee->first_name . ' ' . $contract->employee->last_name)
@section('page-actions')
@if($contract->file_path)
<a href="{{ route('tenant.branch.contracts.download', [$branch, $contract]) }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Download
</a>
@endif
@endsection
@section('content')

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
@endif

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
        @php $cColors = ['active' => 'bg-emerald-50 text-emerald-700', 'expired' => 'bg-red-50 text-red-600', 'terminated' => 'bg-gray-100 text-gray-500']; @endphp
        <div class="flex items-center justify-between">
            <span class="text-xs px-2.5 py-1 rounded-full {{ $cColors[$contract->status] ?? '' }} capitalize">{{ $contract->status }}</span>
            <span class="text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', $contract->contract_type) }}</span>
        </div>

        <dl class="grid grid-cols-2 gap-x-8 gap-y-4 text-sm">
            <div>
                <dt class="text-xs text-gray-400">Employee</dt>
                <dd class="font-medium text-gray-800 mt-0.5">{{ $contract->employee->first_name }} {{ $contract->employee->last_name }}</dd>
            </div>
            @if($contract->job_title)
            <div>
                <dt class="text-xs text-gray-400">Job Title</dt>
                <dd class="text-gray-700 mt-0.5">{{ $contract->job_title }}</dd>
            </div>
            @endif
            @if($contract->department)
            <div>
                <dt class="text-xs text-gray-400">Department</dt>
                <dd class="text-gray-700 mt-0.5">{{ $contract->department }}</dd>
            </div>
            @endif
            @if($contract->salary)
            <div>
                <dt class="text-xs text-gray-400">Salary</dt>
                <dd class="font-medium text-gray-800 mt-0.5">KES {{ number_format($contract->salary) }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-xs text-gray-400">Start Date</dt>
                <dd class="text-gray-700 mt-0.5">{{ \Carbon\Carbon::parse($contract->start_date)->format('M d, Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400">End Date</dt>
                <dd class="text-gray-700 mt-0.5">{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('M d, Y') : 'Permanent / Ongoing' }}</dd>
            </div>
            @if($contract->file_name)
            <div class="col-span-2">
                <dt class="text-xs text-gray-400">Attached File</dt>
                <dd class="text-gray-700 mt-0.5">{{ $contract->file_name }}</dd>
            </div>
            @endif
            @if($contract->notes)
            <div class="col-span-2">
                <dt class="text-xs text-gray-400">Notes</dt>
                <dd class="text-gray-600 mt-0.5">{{ $contract->notes }}</dd>
            </div>
            @endif
        </dl>

        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
            <a href="{{ route('tenant.branch.contracts', $branch) }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Contracts</a>
            <form method="POST" action="{{ route('tenant.branch.contracts.destroy', [$branch, $contract]) }}">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Delete this contract?')" class="text-sm text-red-500 hover:text-red-700">Delete Contract</button>
            </form>
        </div>
    </div>
</div>
@endsection
