@extends('tenant.branch.layout')
@section('page-title', 'Branch Contracts')
@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($contracts->isEmpty())
        <div class="p-12 text-center"><p class="text-gray-400 text-sm">No contracts found for this branch.</p></div>
    @else
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Contract</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
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
                    <td class="px-6 py-4">
                        @php $cColors = ['active' => 'bg-emerald-50 text-emerald-700', 'expired' => 'bg-red-50 text-red-600', 'terminated' => 'bg-gray-100 text-gray-500']; @endphp
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $cColors[$contract->status] ?? '' }} capitalize">{{ $contract->status }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $contracts->links() }}</div>
    @endif
</div>
@endsection
