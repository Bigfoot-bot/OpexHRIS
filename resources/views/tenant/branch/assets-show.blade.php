@extends('tenant.branch.layout')
@section('page-title', $asset->name)
@section('page-subtitle', $asset->asset_code)
@section('content')

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-xl px-4 py-3 mb-6">{{ session('error') }}</div>
@endif

<div class="grid grid-cols-3 gap-6">
    {{-- Asset Details --}}
    <div class="col-span-1 space-y-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-800">Asset Details</h2>
                @php $sColors = ['available' => 'bg-emerald-50 text-emerald-700', 'assigned' => 'bg-blue-50 text-blue-600', 'under_repair' => 'bg-amber-50 text-amber-600', 'disposed' => 'bg-red-50 text-red-600']; @endphp
                <span class="text-xs px-2.5 py-1 rounded-full {{ $sColors[$asset->status] ?? 'bg-gray-100 text-gray-500' }} capitalize">{{ str_replace('_', ' ', $asset->status) }}</span>
            </div>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-xs text-gray-400">Category</dt><dd class="text-gray-700 font-medium">{{ $asset->assetCategory->name ?? 'N/A' }}</dd></div>
                @if($asset->brand)<div><dt class="text-xs text-gray-400">Brand</dt><dd class="text-gray-700">{{ $asset->brand }}</dd></div>@endif
                @if($asset->model)<div><dt class="text-xs text-gray-400">Model</dt><dd class="text-gray-700">{{ $asset->model }}</dd></div>@endif
                @if($asset->serial_number)<div><dt class="text-xs text-gray-400">Serial</dt><dd class="text-gray-700 font-mono text-xs">{{ $asset->serial_number }}</dd></div>@endif
                @if($asset->purchase_price)<div><dt class="text-xs text-gray-400">Purchase Price</dt><dd class="text-gray-700">KES {{ number_format($asset->purchase_price) }}</dd></div>@endif
                @if($asset->purchase_date)<div><dt class="text-xs text-gray-400">Purchase Date</dt><dd class="text-gray-700">{{ \Carbon\Carbon::parse($asset->purchase_date)->format('M d, Y') }}</dd></div>@endif
            </dl>
        </div>

        {{-- Return Button --}}
        @if($asset->status === 'assigned')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-800 mb-3">Mark as Returned</h2>
            <form method="POST" action="{{ route('tenant.branch.assets.return', [$branch, $asset]) }}">
                @csrf
                <button type="submit" onclick="return confirm('Mark this asset as returned?')"
                        class="w-full bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-medium py-2 rounded-lg border border-amber-100">
                    Return Asset
                </button>
            </form>
        </div>
        @endif

        {{-- Assign Form --}}
        @if($asset->status === 'available')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-800 mb-3">Assign to Employee</h2>
            @if($errors->any())
                <div class="bg-red-50 text-red-600 text-xs rounded-lg px-3 py-2 mb-3">
                    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('tenant.branch.assets.assign', [$branch, $asset]) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Employee *</label>
                    <select name="employee_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Assigned Date *</label>
                    <input type="date" name="assigned_date" required value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                    <input type="text" name="notes" placeholder="Optional notes"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2 rounded-lg">Assign Asset</button>
            </form>
        </div>
        @endif
    </div>

    {{-- Assignment History --}}
    <div class="col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h2 class="text-sm font-semibold text-gray-800">Assignment History</h2>
            </div>
            @if($assignments->isEmpty())
                <div class="p-8 text-center"><p class="text-gray-400 text-sm">No assignments yet.</p></div>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Employee</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Assigned</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Returned</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($assignments as $a)
                        <tr>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $a->employee->first_name ?? 'N/A' }} {{ $a->employee->last_name ?? '' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($a->assigned_date)->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-sm text-gray-500">{{ $a->return_date ? \Carbon\Carbon::parse($a->return_date)->format('M d, Y') : '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $a->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }} capitalize">{{ $a->status }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
