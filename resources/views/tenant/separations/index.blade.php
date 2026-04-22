@extends('tenant.layouts.app')
@section('page-title', 'Separation & Offboarding')
@section('page-subtitle', 'Manage employee separations and clearance')
@section('page-actions')
    <a href="{{ route('tenant.separations.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">+ New Separation</a>
@endsection
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>@endif

    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Pending</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['in_progress'] }}</p>
            <p class="text-xs text-gray-400 mt-1">In Progress</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['completed'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Completed</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($separations->isEmpty())
            <div class="p-12 text-center"><p class="text-gray-400 text-sm">No separations recorded yet.</p></div>
        @else
            <table class="w-full">
                <thead><tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Last Working Day</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Final Dues</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($separations as $sep)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $sep->employee->first_name ?? 'N/A' }} {{ $sep->employee->last_name ?? '' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $sep->type) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sep->last_working_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">KES {{ number_format($sep->final_dues, 0) }}</td>
                        <td class="px-6 py-4">
                            @php $colors = ['pending' => 'bg-amber-50 text-amber-600', 'in_progress' => 'bg-blue-50 text-blue-600', 'cleared' => 'bg-purple-50 text-purple-600', 'completed' => 'bg-emerald-50 text-emerald-700']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$sep->status] }} capitalize">{{ str_replace('_', ' ', $sep->status) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('tenant.separations.show', $sep) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $separations->links() }}</div>
        @endif
    </div>
</div>
@endsection
