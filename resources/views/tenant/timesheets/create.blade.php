@extends('tenant.layouts.app')
@section('page-title', 'New Timesheet')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('tenant.timesheets.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-3 gap-4">
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
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Week Start *</label>
                    <input type="date" name="week_start" value="{{ $weekStart->format('Y-m-d') }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Week End *</label>
                    <input type="date" name="week_end" value="{{ $weekEnd->format('Y-m-d') }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Daily Entries</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b border-gray-100">
                            <th class="text-left text-xs text-gray-400 font-medium py-2 px-2">Date</th>
                            <th class="text-left text-xs text-gray-400 font-medium py-2 px-2">Clock In</th>
                            <th class="text-left text-xs text-gray-400 font-medium py-2 px-2">Clock Out</th>
                            <th class="text-left text-xs text-gray-400 font-medium py-2 px-2">Work Type</th>
                            <th class="text-left text-xs text-gray-400 font-medium py-2 px-2">Project</th>
                            <th class="text-left text-xs text-gray-400 font-medium py-2 px-2">Description</th>
                        </tr></thead>
                        <tbody>
                            @for($i = 0; $i < 7; $i++)
                            @php $date = $weekStart->copy()->addDays($i); @endphp
                            <tr class="border-b border-gray-50">
                                <td class="py-2 px-2">
                                    <input type="hidden" name="entries[{{ $i }}][date]" value="{{ $date->format('Y-m-d') }}"/>
                                    <span class="text-xs font-medium text-gray-700">{{ $date->format('D, M d') }}</span>
                                </td>
                                <td class="py-2 px-2"><input type="time" name="entries[{{ $i }}][clock_in]" class="px-2 py-1 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"/></td>
                                <td class="py-2 px-2"><input type="time" name="entries[{{ $i }}][clock_out]" class="px-2 py-1 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"/></td>
                                <td class="py-2 px-2">
                                    <select name="entries[{{ $i }}][work_type]" class="px-2 py-1 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                        <option value="regular">Regular</option>
                                        <option value="overtime">Overtime</option>
                                        <option value="remote">Remote</option>
                                        <option value="leave">Leave</option>
                                        <option value="holiday">Holiday</option>
                                    </select>
                                </td>
                                <td class="py-2 px-2"><input type="text" name="entries[{{ $i }}][project]" placeholder="Project" class="px-2 py-1 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500 w-28"/></td>
                                <td class="py-2 px-2"><input type="text" name="entries[{{ $i }}][description]" placeholder="Description" class="px-2 py-1 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500 w-36"/></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2 rounded-lg">Save Timesheet</button>
                <a href="{{ route('tenant.timesheets.index') }}" class="bg-gray-100 text-gray-600 text-sm font-medium px-6 py-2 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
