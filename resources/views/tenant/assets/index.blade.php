@extends(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0 ? 'tenant.layouts.app' : 'tenant.employee.layouts.app')
@section('page-title', 'Asset Management')
@section('page-subtitle', 'Track and manage company assets')
@section('page-actions')
@if(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0)
    <div class="flex gap-2">
        <a href="{{ route('tenant.assets.categories') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50">Categories</a>
        <a href="{{ route('tenant.assets.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">+ Add Asset</a>
    </div>
@endif
@endsection
@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    @if(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0)
    <div class="grid grid-cols-4 gap-4">
        @foreach(['available' => 'Available', 'assigned' => 'Assigned', 'under_repair' => 'Under Repair', 'disposed' => 'Disposed'] as $status => $label)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ \App\Models\Asset::where('tenant_id', tenant('id'))->where('status', $status)->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $label }}</p>
        </div>
        @endforeach
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search assets..."
                   class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            @if(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0)
            <select name="status" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">All Status</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="under_repair" {{ request('status') == 'under_repair' ? 'selected' : '' }}>Under Repair</option>
                <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
            </select>
            @endif
            <button type="submit" class="bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($assets->isEmpty())
            <div class="p-12 text-center">
                <p class="text-gray-400 text-sm">No assets found.</p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Asset</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Category</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Assigned To</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Value</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($assets as $asset)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-800">{{ $asset->name }}</p>
                            <p class="text-xs text-gray-400">{{ $asset->asset_code }} {{ $asset->serial_number ? '- S/N: ' . $asset->serial_number : '' }}{{ $asset->number_plate ? '- Plate: ' . $asset->number_plate : '' }}</p>
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
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $asset->current_value ? 'KES ' . number_format($asset->current_value, 0) : 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @php $sColors = ['available' => 'bg-emerald-50 text-emerald-700', 'assigned' => 'bg-blue-50 text-blue-600', 'under_repair' => 'bg-amber-50 text-amber-600', 'disposed' => 'bg-red-50 text-red-600']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $sColors[$asset->status] ?? '' }} capitalize">{{ str_replace('_', ' ', $asset->status) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('tenant.assets.show', $asset) }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">View</a>
                                @if(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0)
                                <a href="{{ route('tenant.assets.edit', $asset) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $assets->links() }}</div>
        @endif
    </div>
</div>
@endsection


