@extends('tenant.layouts.app')
@section('page-title', 'Scheduled Reports')
@section('page-subtitle', 'Automate report delivery to your team')
@section('content')
<div class="grid grid-cols-3 gap-6">
    {{-- Create Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Schedule New Report</h2>
        @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('tenant.scheduled-reports.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Report Name *</label>
                <input type="text" name="name" required placeholder="e.g. Monthly Payroll Summary" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Report Type *</label>
                <select name="report_type" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @foreach($reportTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Frequency *</label>
                <select name="frequency" required onchange="toggleFrequencyOptions(this.value)" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>
            <div id="weekly-options" class="hidden">
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Day of Week</label>
                <select name="day_of_week" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="mon">Monday</option>
                    <option value="tue">Tuesday</option>
                    <option value="wed">Wednesday</option>
                    <option value="thu">Thursday</option>
                    <option value="fri">Friday</option>
                </select>
            </div>
            <div id="monthly-options" class="hidden">
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Day of Month</label>
                <input type="number" name="day_of_month" min="1" max="31" value="1" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Send Time *</label>
                <input type="time" name="send_time" value="08:00" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Format</label>
                <select name="format" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="csv">CSV</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Recipients (comma-separated) *</label>
                <textarea name="recipients" required rows="2" placeholder="hr@hospital.com, admin@hospital.com" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Schedule Report</button>
        </form>
    </div>

    {{-- Scheduled Reports List --}}
    <div class="col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-sm font-semibold text-gray-800">Scheduled Reports</h2>
        </div>
        @if($reports->isEmpty())
            <div class="p-12 text-center"><p class="text-gray-400 text-sm">No scheduled reports yet.</p></div>
        @else
            <table class="w-full">
                <thead><tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Name</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Type</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Frequency</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Next Send</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($reports as $report)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-800">{{ $report->name }}</p>
                            <p class="text-xs text-gray-400">{{ $report->recipients }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $reportTypes[$report->report_type] ?? $report->report_type }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $report->frequency }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $report->next_send_at?->format('M d, Y H:i') ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $report->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $report->is_active ? 'Active' : 'Paused' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <form method="POST" action="{{ route('tenant.scheduled-reports.toggle', $report) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium">{{ $report->is_active ? 'Pause' : 'Resume' }}</button>
                                </form>
                                <form method="POST" action="{{ route('tenant.scheduled-reports.destroy', $report) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $reports->links() }}</div>
        @endif
    </div>
</div>

<script>
function toggleFrequencyOptions(value) {
    document.getElementById('weekly-options').classList.toggle('hidden', value !== 'weekly');
    document.getElementById('monthly-options').classList.toggle('hidden', value !== 'monthly');
}
</script>
@endsection
