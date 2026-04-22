@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', $grievance->grievance_number . ' — ' . $grievance->title)
@section('page-subtitle', $grievance->employee->full_name)

@section('page-actions')
    <a href="{{ route('tenant.grievances.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Grievances
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

        {{-- Grievance Details --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Grievance Details</h2>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Reference Number</p>
                    <p class="text-sm font-mono text-gray-700">{{ $grievance->grievance_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Category</p>
                    <p class="text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $grievance->category) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Submitted Date</p>
                    <p class="text-sm text-gray-700">{{ $grievance->submitted_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Resolution Date</p>
                    <p class="text-sm text-gray-700">{{ $grievance->resolution_date ? $grievance->resolution_date->format('M d, Y') : '—' }}</p>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Description</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $grievance->description }}</p>
            </div>
        </div>

        {{-- Resolution --}}
        @if($grievance->resolution)
        <div class="bg-emerald-50 rounded-xl border border-emerald-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-3">Resolution</h2>
            <p class="text-sm text-emerald-700 leading-relaxed">{{ $grievance->resolution }}</p>
        </div>
        @endif

        {{-- Update Form --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Update Grievance</h2>
            <form method="POST" action="{{ route('tenant.grievances.update', $grievance) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Status</label>
                        <select name="status"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="submitted" {{ $grievance->status === 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="under_review" {{ $grievance->status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="investigation" {{ $grievance->status === 'investigation' ? 'selected' : '' }}>Investigation</option>
                            <option value="resolved" {{ $grievance->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $grievance->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="escalated" {{ $grievance->status === 'escalated' ? 'selected' : '' }}>Escalated</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Resolution</label>
                        <textarea name="resolution" rows="3"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                  placeholder="Resolution details...">{{ $grievance->resolution }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Resolution Date</label>
                        <input type="date" name="resolution_date" value="{{ $grievance->resolution_date?->format('Y-m-d') }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <button type="submit"
                            class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors duration-150">
                        Update Grievance
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- Right Column --}}
    <div class="space-y-5">

        {{-- Status & Priority --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Status & Priority</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Status</p>
                    @php
                        $statusColors = [
                            'submitted'     => 'bg-gray-50 text-gray-500',
                            'under_review'  => 'bg-blue-50 text-blue-600',
                            'investigation' => 'bg-amber-50 text-amber-600',
                            'resolved'      => 'bg-emerald-50 text-emerald-600',
                            'closed'        => 'bg-gray-50 text-gray-500',
                            'escalated'     => 'bg-red-50 text-red-500',
                        ];
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$grievance->status] ?? '' }} capitalize">
                        {{ str_replace('_', ' ', $grievance->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Priority</p>
                    @php
                        $priorityColors = [
                            'low'      => 'bg-gray-50 text-gray-500',
                            'medium'   => 'bg-blue-50 text-blue-600',
                            'high'     => 'bg-amber-50 text-amber-600',
                            'critical' => 'bg-red-50 text-red-500',
                        ];
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $priorityColors[$grievance->priority] ?? '' }} capitalize">
                        {{ $grievance->priority }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Employee Info --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Employee</h2>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-sm font-medium">
                    {{ strtoupper(substr($grievance->employee->first_name, 0, 1) . substr($grievance->employee->last_name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-emerald-900">{{ $grievance->employee->full_name }}</p>
                    <p class="text-xs text-gray-400">{{ $grievance->employee->job_title }}</p>
                </div>
            </div>
            <div class="space-y-2">
                <div>
                    <p class="text-xs text-gray-400">Department</p>
                    <p class="text-sm text-gray-700">{{ $grievance->employee->department ?? '—' }}</p>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
