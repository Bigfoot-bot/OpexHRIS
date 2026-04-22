@extends('tenant.layouts.app')
@section('page-title', 'Overtime Management')
@section('page-subtitle', 'Track and approve overtime requests')
@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Pending</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['approved'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Approved</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_hours'], 1) }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Hours</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">KES {{ number_format($stats['total_amount'], 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Amount</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        {{-- Submit Overtime --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Submit Overtime Request</h2>
            <form method="POST" action="{{ route('tenant.overtime.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Employee *</label>
                    <select name="employee_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Date *</label>
                    <input type="date" name="date" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Start Time *</label>
                        <input type="time" name="start_time" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">End Time *</label>
                        <input type="time" name="end_time" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Rate Multiplier</label>
                    <select name="rate_multiplier" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="1.5">1.5x (Standard)</option>
                        <option value="2.0">2.0x (Holiday/Night)</option>
                        <option value="1.25">1.25x (Weekend)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Reason *</label>
                    <textarea name="reason" required rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Submit Request</button>
            </form>
        </div>

        {{-- Overtime List --}}
        <div class="col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 flex gap-3">
                <form method="GET" class="flex gap-2">
                    <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:outline-none">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </form>
            </div>
            @if($overtimes->isEmpty())
                <div class="p-12 text-center"><p class="text-gray-400 text-sm">No overtime requests yet.</p></div>
            @else
                <table class="w-full">
                    <thead><tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Date</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Hours</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Amount</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($overtimes as $ot)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-800">{{ $ot->employee->first_name ?? 'N/A' }} {{ $ot->employee->last_name ?? '' }}</p>
                                <p class="text-xs text-gray-400">{{ $ot->reason }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $ot->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $ot->hours }}hrs</td>
                            <td class="px-6 py-4 text-sm text-gray-600">KES {{ number_format($ot->amount, 0) }}</td>
                            <td class="px-6 py-4">
                                @php $colors = ['pending' => 'bg-amber-50 text-amber-600', 'approved' => 'bg-emerald-50 text-emerald-700', 'rejected' => 'bg-red-50 text-red-600']; @endphp
                                <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$ot->status] }} capitalize">{{ $ot->status }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($ot->status === 'pending')
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('tenant.overtime.approve', $ot) }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('tenant.overtime.reject', $ot) }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Reject</button>
                                    </form>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-6 py-4">{{ $overtimes->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
