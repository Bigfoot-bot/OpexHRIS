@extends('tenant.branch.layout')
@section('page-title', 'Assets')
@section('page-subtitle', 'Manage branch assets')
@section('page-actions')
<a href="{{ route('tenant.branch.assets.create', $branch) }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Add Asset
</a>
@endsection
@section('content')

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($assets->isEmpty())
        <div class="p-12 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
            <p class="text-gray-400 text-sm">No assets found.</p>
            <a href="{{ route('tenant.branch.assets.create', $branch) }}" class="inline-block mt-3 text-sm text-emerald-700 font-medium hover:underline">Add your first asset</a>
        </div>
    @else
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Asset</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Category</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Assigned To</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
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
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $sColors[$asset->status] ?? 'bg-gray-100 text-gray-500' }} capitalize">{{ str_replace('_', ' ', $asset->status) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('tenant.branch.assets.show', [$branch, $asset]) }}" class="text-xs text-emerald-700 hover:text-emerald-900 font-medium">Manage</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $assets->links() }}</div>
    @endif
</div>
@endsection
