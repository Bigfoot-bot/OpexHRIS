@extends('tenant.branch.layout')
@section('page-title', 'Branch Assets')
@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($assets->isEmpty())
        <div class="p-12 text-center"><p class="text-gray-400 text-sm">No assets found for this branch.</p></div>
    @else
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Asset</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Category</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Assigned To</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($assets as $asset)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-800">{{ $asset->name }}</p>
                        <p class="text-xs text-gray-400">{{ $asset->asset_code }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $asset->assetCategory->name ?? $asset->category ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($asset->currentAssignment)
                            @php $emp = \App\Models\Tenant\Employee::find($asset->currentAssignment->employee_id); @endphp
                            {{ $emp ? $emp->first_name . ' ' . $emp->last_name : 'N/A' }}
                        @else
                            <span class="text-gray-400">Unassigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php $sColors = ['available' => 'bg-emerald-50 text-emerald-700', 'assigned' => 'bg-blue-50 text-blue-600', 'under_repair' => 'bg-amber-50 text-amber-600', 'disposed' => 'bg-red-50 text-red-600']; @endphp
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $sColors[$asset->status] ?? '' }} capitalize">{{ str_replace('_', ' ', $asset->status) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $assets->links() }}</div>
    @endif
</div>
@endsection
