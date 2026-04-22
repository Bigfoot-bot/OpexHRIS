@extends('tenant.layouts.app')
@section('page-title', $roster->name)
@section('page-subtitle', $roster->start_date->format('M d') . ' - ' . $roster->end_date->format('M d, Y'))
@section('page-actions')
    @if($roster->status === 'draft')
    <form method="POST" action="{{ route('tenant.shifts.publish', $roster) }}" class="inline">
        @csrf
        <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Publish Roster</button>
    </form>
    @endif
    <a href="{{ route('tenant.shifts.index') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">Back</a>
@endsection
@section('content')
<div class="space-y-4">
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    {{-- Shift Legend --}}
    <div class="bg-white rounded-xl border border-gray-100 p-4 flex gap-4 flex-wrap">
        @foreach($shifts as $shift)
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded" style="background-color: {{ $shift->color }}"></div>
            <span class="text-xs text-gray-600">{{ $shift->code ?? $shift->name }} - {{ $shift->name }} ({{ $shift->start_time }}-{{ $shift->end_time }})</span>
        </div>
        @endforeach
    </div>

    {{-- Roster Grid --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-4 py-3 text-gray-500 font-medium w-40 sticky left-0 bg-white">Employee</th>
                    @foreach($dates as $date)
                    <th class="px-2 py-3 text-center text-gray-500 font-medium min-w-16">
                        <div>{{ $date->format('D') }}</div>
                        <div class="text-gray-400">{{ $date->format('d') }}</div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($employees as $employee)
                <tr class="hover:bg-gray-50/30">
                    <td class="px-4 py-2 sticky left-0 bg-white">
                        <p class="font-medium text-gray-800">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                        <p class="text-gray-400">{{ $employee->job_title }}</p>
                    </td>
                    @foreach($dates as $date)
                    @php
                        $key = $employee->id . '_' . $date->format('Y-m-d');
                        $assignment = $assignments->get($key)?->first();
                        $shift = $assignment ? $shifts->find($assignment->shift_id) : null;
                    @endphp
                    <td class="px-1 py-2 text-center">
                        @if($roster->status === 'draft')
                        <select onchange="assignShift({{ $roster->id }}, {{ $employee->id }}, '{{ $date->format('Y-m-d') }}', this.value)"
                                class="w-14 text-xs rounded border border-gray-200 py-1 px-1 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                style="{{ $shift ? 'background-color: ' . $shift->color . '20; border-color: ' . $shift->color : '' }}">
                            <option value="">-</option>
                            @foreach($shifts as $s)
                            <option value="{{ $s->id }}" {{ $assignment && $assignment->shift_id == $s->id ? 'selected' : '' }}>{{ $s->code ?? substr($s->name, 0, 2) }}</option>
                            @endforeach
                        </select>
                        @else
                            @if($shift)
                            <span class="px-2 py-1 rounded text-white text-xs" style="background-color: {{ $shift->color }}">{{ $shift->code ?? substr($shift->name, 0, 1) }}</span>
                            @else
                            <span class="text-gray-300">-</span>
                            @endif
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function assignShift(rosterId, employeeId, date, shiftId) {
    fetch(`/rosters/${rosterId}/assign`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ employee_id: employeeId, date: date, shift_id: shiftId })
    });
}
</script>
@endsection




