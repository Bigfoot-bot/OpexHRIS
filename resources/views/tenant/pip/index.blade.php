@extends('tenant.layouts.app')
@section('page-title', 'Performance Improvement Plans')
@section('page-subtitle', 'Manage employee PIPs and improvement tracking')
@section('page-actions')
    <a href="{{ route('tenant.pip.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">+ New PIP</a>
@endsection
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>@endif

    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-600">{{ $stats['draft'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Draft</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $stats['active'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Active</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['completed'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Completed</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($pips->isEmpty())
            <div class="p-12 text-center"><p class="text-gray-400 text-sm">No PIPs created yet.</p></div>
        @else
            <table class="w-full">
                <thead><tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Title</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Period</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Review Date</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($pips as $pip)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $pip->employee->first_name ?? 'N/A' }} {{ $pip->employee->last_name ?? '' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $pip->title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $pip->start_date->format('M d') }} - {{ $pip->end_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $pip->review_date ? $pip->review_date->format('M d, Y') : '-' }}</td>
                        <td class="px-6 py-4">
                            @php $colors = ['draft' => 'bg-gray-100 text-gray-600', 'active' => 'bg-amber-50 text-amber-600', 'completed' => 'bg-emerald-50 text-emerald-700', 'extended' => 'bg-blue-50 text-blue-600', 'terminated' => 'bg-red-50 text-red-600']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$pip->status] }} capitalize">{{ $pip->status }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenant.pip.show', $pip) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">View</a>
                                <a href="{{ route('tenant.pip.edit', $pip) }}" class="text-xs text-gray-500 hover:text-gray-700 font-medium">Edit</a>
                                <form method="POST" action="{{ route('tenant.pip.destroy', $pip) }}">@csrf @method('DELETE')<button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button></form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $pips->links() }}</div>
        @endif
    </div>
</div>
@endsection
