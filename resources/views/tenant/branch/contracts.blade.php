@extends('tenant.branch.layout')
@section('page-title', 'Contracts')
@section('page-subtitle', 'Manage branch employee contracts')
@section('page-actions')
<a href="{{ route('tenant.branch.contracts.create', $branch) }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    New Contract
</a>
@endsection
@section('content')

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($contracts->isEmpty())
        <div class="p-12 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p class="text-gray-400 text-sm">No contracts found for this branch.</p>
            <a href="{{ route('tenant.branch.contracts.create', $branch) }}" class="inline-block mt-3 text-sm text-emerald-700 font-medium hover:underline">Create first contract</a>
        </div>
    @else
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Contract</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Dates</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($contracts as $contract)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-800">{{ $contract->employee->first_name ?? 'N/A' }} {{ $contract->employee->last_name ?? '' }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $contract->title }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $contract->contract_type) }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($contract->start_date)->format('M d, Y') }}
                        @if($contract->end_date)
                            – {{ \Carbon\Carbon::parse($contract->end_date)->format('M d, Y') }}
                        @else
                            <span class="text-gray-400">– Ongoing</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php $cColors = ['active' => 'bg-emerald-50 text-emerald-700', 'expired' => 'bg-red-50 text-red-600', 'terminated' => 'bg-gray-100 text-gray-500']; @endphp
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $cColors[$contract->status] ?? '' }} capitalize">{{ $contract->status }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('tenant.branch.contracts.show', [$branch, $contract]) }}" class="text-xs text-emerald-700 hover:text-emerald-900 font-medium">View</a>
                            @if($contract->file_path)
                            <a href="{{ route('tenant.branch.contracts.download', [$branch, $contract]) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Download</a>
                            @endif
                            <form method="POST" action="{{ route('tenant.branch.contracts.destroy', [$branch, $contract]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this contract?')" class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $contracts->links() }}</div>
    @endif
</div>
@endsection
