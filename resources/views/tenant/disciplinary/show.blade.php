@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', $disciplinary->case_number . ' — ' . $disciplinary->title)
@section('page-subtitle', $disciplinary->employee->full_name)

@section('page-actions')
    <a href="{{ route('tenant.disciplinary.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Cases
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

<div class="grid grid-cols-3 gap-5">

    {{-- Left Column --}}
    <div class="col-span-2 space-y-5">

        {{-- Case Details --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Case Details</h2>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Case Number</p>
                    <p class="text-sm font-mono text-gray-700">{{ $disciplinary->case_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Employee</p>
                    <p class="text-sm text-gray-700">{{ $disciplinary->employee->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Type</p>
                    <p class="text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $disciplinary->type) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Severity</p>
                    <p class="text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $disciplinary->severity) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Incident Date</p>
                    <p class="text-sm text-gray-700">{{ $disciplinary->incident_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Hearing Date</p>
                    <p class="text-sm text-gray-700">{{ $disciplinary->hearing_date ? $disciplinary->hearing_date->format('M d, Y') : '—' }}</p>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Description</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $disciplinary->description }}</p>
            </div>
        </div>

        {{-- Employee Response --}}
        @if($disciplinary->employee_response)
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-3">Employee Response</h2>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $disciplinary->employee_response }}</p>
        </div>
        @endif

        {{-- Outcome --}}
        @if($disciplinary->outcome)
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-3">Outcome</h2>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $disciplinary->outcome }}</p>
            @if($disciplinary->resolution_date)
                <p class="text-xs text-gray-400 mt-2">Resolved on {{ $disciplinary->resolution_date->format('M d, Y') }}</p>
            @endif
        </div>
        @endif

        {{-- Update Form --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Update Case</h2>
            <form method="POST" action="{{ route('tenant.disciplinary.update', $disciplinary) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Status</label>
                        <select name="status"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="open" {{ $disciplinary->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="under_investigation" {{ $disciplinary->status === 'under_investigation' ? 'selected' : '' }}>Under Investigation</option>
                            <option value="hearing_scheduled" {{ $disciplinary->status === 'hearing_scheduled' ? 'selected' : '' }}>Hearing Scheduled</option>
                            <option value="closed" {{ $disciplinary->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="appealed" {{ $disciplinary->status === 'appealed' ? 'selected' : '' }}>Appealed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Hearing Date</label>
                        <input type="date" name="hearing_date" value="{{ $disciplinary->hearing_date?->format('Y-m-d') }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Employee Response</label>
                        <textarea name="employee_response" rows="3"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                  placeholder="Employee's response to the case...">{{ $disciplinary->employee_response }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Outcome</label>
                        <textarea name="outcome" rows="3"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                  placeholder="Case outcome and decision...">{{ $disciplinary->outcome }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Resolution Date</label>
                        <input type="date" name="resolution_date" value="{{ $disciplinary->resolution_date?->format('Y-m-d') }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <button type="submit"
                            class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors duration-150">
                        Update Case
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- Right Column --}}
    <div class="space-y-5">

        {{-- Status Card --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Status</h2>
            @php
                $statusColors = [
                    'open'                => 'bg-amber-50 text-amber-600',
                    'under_investigation' => 'bg-blue-50 text-blue-600',
                    'hearing_scheduled'   => 'bg-purple-50 text-purple-600',
                    'closed'              => 'bg-emerald-50 text-emerald-600',
                    'appealed'            => 'bg-red-50 text-red-500',
                ];
            @endphp
            <span class="text-xs px-3 py-1.5 rounded-full {{ $statusColors[$disciplinary->status] ?? '' }} capitalize">
                {{ str_replace('_', ' ', $disciplinary->status) }}
            </span>
        </div>

        {{-- Employee Info --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Employee</h2>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-sm font-medium">
                    {{ strtoupper(substr($disciplinary->employee->first_name, 0, 1) . substr($disciplinary->employee->last_name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-emerald-900">{{ $disciplinary->employee->full_name }}</p>
                    <p class="text-xs text-gray-400">{{ $disciplinary->employee->job_title }}</p>
                </div>
            </div>
            <div class="space-y-2">
                <div>
                    <p class="text-xs text-gray-400">Department</p>
                    <p class="text-sm text-gray-700">{{ $disciplinary->employee->department ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Employee No.</p>
                    <p class="text-sm text-gray-700">{{ $disciplinary->employee->employee_number }}</p>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
