@extends(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0 ? 'tenant.layouts.app' : 'tenant.employee.layouts.app')
@section('page-title', $asset->name)
@section('page-subtitle', 'Asset details and assignment history')
@section('page-actions')
    @if(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0)
    <div class="flex gap-2">
        <a href="{{ route('tenant.assets.edit', $asset) }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">Edit</a>
        @if($asset->status === 'assigned')
            <form method="POST" action="{{ route('tenant.assets.return', $asset) }}">
                @csrf
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium px-4 py-2 rounded-lg" onclick="return confirm('Mark as returned?')">Return Asset</button>
            </form>
        @endif
    </div>
    @endif
@endsection
@section('content')
<div class="grid grid-cols-3 gap-6">
    <div class="col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Asset Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-xs text-gray-400">Asset Code</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $asset->asset_code }}</p></div>
                <div><p class="text-xs text-gray-400">Category</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $asset->category ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-400">Brand / Model</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $asset->brand ?? 'N/A' }} {{ $asset->model ? '/ ' . $asset->model : '' }}</p></div>
                <div><p class="text-xs text-gray-400">Serial Number</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $asset->serial_number ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-400">Purchase Price</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $asset->purchase_price ? 'KES ' . number_format($asset->purchase_price, 2) : 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-400">Current Value</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $asset->current_value ? 'KES ' . number_format($asset->current_value, 2) : 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-400">Purchase Date</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $asset->purchase_date?->format('M d, Y') ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-400">Location</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $asset->location ?? 'N/A' }}</p></div>
            </div>

            @php $sColors = ['available' => 'bg-emerald-50 text-emerald-700', 'assigned' => 'bg-blue-50 text-blue-600', 'under_repair' => 'bg-amber-50 text-amber-600', 'disposed' => 'bg-red-50 text-red-600']; @endphp
            <div class="mt-4">
                <span class="text-xs px-3 py-1.5 rounded-full {{ $sColors[$asset->status] ?? '' }} capitalize font-medium">{{ str_replace('_', ' ', $asset->status) }}</span>
            </div>
        </div>

        {{-- Assign Asset --}}
        @if($asset->status !== 'disposed' && auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Assign Asset</h2>
            <form method="POST" action="{{ route('tenant.assets.assign', $asset) }}" class="grid grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Employee *</label>
                    <select name="employee_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Assigned Date *</label>
                    <input type="date" name="assigned_date" required value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Notes</label>
                    <input type="text" name="notes" placeholder="Optional notes"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-5 py-2 rounded-lg">Assign Asset</button>
                </div>
            </form>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Assignment History</h2>
        @if($assignments->isEmpty())
            <p class="text-xs text-gray-400">No assignments yet.</p>
        @else
            <div class="space-y-3">
                @foreach($assignments as $assignment)
                @php $emp = \App\Models\Tenant\Employee::find($assignment->employee_id); @endphp
                <div class="p-3 bg-gray-50 rounded-xl">
                    <p class="text-sm font-medium text-gray-800">{{ $emp ? $emp->first_name . ' ' . $emp->last_name : 'Unknown' }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $assignment->assigned_date->format('M d, Y') }} {{ $assignment->return_date ? '? ' . $assignment->return_date->format('M d, Y') : '? Present' }}</p>
                    @php $aColors = ['active' => 'bg-emerald-50 text-emerald-700', 'returned' => 'bg-gray-100 text-gray-500']; @endphp
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $aColors[$assignment->status] ?? '' }} capitalize mt-1 inline-block">{{ $assignment->status }}</span>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection





