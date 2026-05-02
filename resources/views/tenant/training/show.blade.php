@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', $training->title)
@section('page-subtitle', $training->provider ?? $training->type . ' · ' . $training->category)

@section('page-actions')
    <a href="{{ route('tenant.training.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Training
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

        {{-- Enrollments --}}
        <div class="bg-white rounded-xl border border-green-100">
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                <h2 class="text-sm font-medium text-emerald-900">Enrolled Staff ({{ $training->enrollments->count() }})</h2>
            </div>

            {{-- Enroll Form --}}
            <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                <form method="POST" action="{{ route('tenant.training.enroll', $training) }}" class="flex items-end gap-3">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Enroll Employee</label>
                        <select name="employee_id" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }} — {{ $employee->job_title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        Enroll
                    </button>
                </form>
            </div>

            @if($training->enrollments->isEmpty())
                <div class="text-center py-10">
                    <p class="text-gray-400 text-sm">No staff enrolled yet.</p>
                </div>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Employee</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Status</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">CPD Points</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Score</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Certificate</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($training->enrollments as $enrollment)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3">
                                <p class="text-sm font-medium text-emerald-900">{{ $enrollment->employee->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $enrollment->employee->job_title }}</p>
                            </td>
                            <td class="px-6 py-3">
                                @php
                                    $statusColors = [
                                        'enrolled'  => 'bg-blue-50 text-blue-600',
                                        'attended'  => 'bg-amber-50 text-amber-600',
                                        'completed' => 'bg-emerald-50 text-emerald-600',
                                        'cancelled' => 'bg-red-50 text-red-500',
                                    ];
                                @endphp
                                <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$enrollment->status] ?? '' }} capitalize">
                                    {{ $enrollment->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm text-emerald-700">{{ $enrollment->cpd_points_earned }} pts</span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm text-gray-600">{{ $enrollment->score ? $enrollment->score . '%' : '—' }}</span>
                            </td>
                            <td class="px-6 py-3">
                                @if($enrollment->certificate_issued)
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs bg-emerald-50 text-emerald-600 px-2 py-1 rounded-full">Issued</span>
                                        <a href="{{ route('tenant.training.certificate', $enrollment) }}"
                                           class="text-xs text-blue-600 hover:text-blue-800 underline">Download</a>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <form method="POST" action="{{ route('tenant.training.enrollment.update', $enrollment) }}"
                                      class="flex items-center gap-2 flex-wrap">
                                    @csrf
                                    <select name="status"
                                            class="px-2 py-1 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                        <option value="enrolled" {{ $enrollment->status === 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                                        <option value="attended" {{ $enrollment->status === 'attended' ? 'selected' : '' }}>Attended</option>
                                        <option value="completed" {{ $enrollment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $enrollment->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    <input type="number" name="cpd_points_earned" value="{{ $enrollment->cpd_points_earned }}"
                                           min="0" placeholder="CPD"
                                           class="w-16 px-2 py-1 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"/>
                                    <input type="number" name="score" value="{{ $enrollment->score }}"
                                           min="0" max="100" placeholder="Score"
                                           class="w-16 px-2 py-1 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"/>
                                    @if($training->certificate_provided)
                                        <input type="hidden" name="certificate_issued" value="0">
                                        <label class="flex items-center gap-1 text-xs text-gray-600 cursor-pointer">
                                            <input type="checkbox" name="certificate_issued" value="1"
                                                   {{ $enrollment->certificate_issued ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-emerald-600">
                                            Issue Cert
                                        </label>
                                    @endif
                                    <button type="submit"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-3 py-1 rounded-lg">
                                        Save
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

    {{-- Right Column --}}
    <div class="space-y-5">

        {{-- Program Info --}}
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-4">Program Info</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-400">Status</p>
                    @php
                        $statusColors = [
                            'planned'   => 'bg-blue-50 text-blue-600',
                            'ongoing'   => 'bg-amber-50 text-amber-600',
                            'completed' => 'bg-emerald-50 text-emerald-600',
                            'cancelled' => 'bg-red-50 text-red-500',
                        ];
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$training->status] ?? '' }} capitalize">
                        {{ $training->status }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Type</p>
                    <p class="text-sm text-gray-700 capitalize">{{ $training->type }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Category</p>
                    <p class="text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $training->category) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">CPD Points</p>
                    <p class="text-sm font-medium text-emerald-700">{{ $training->cpd_points }} points</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Cost</p>
                    <p class="text-sm text-gray-700">KES {{ number_format($training->cost) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Location</p>
                    <p class="text-sm text-gray-700">{{ $training->location ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Dates</p>
                    <p class="text-sm text-gray-700">{{ $training->start_date->format('M d') }} — {{ $training->end_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Max Participants</p>
                    <p class="text-sm text-gray-700">{{ $training->max_participants ?? 'Unlimited' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Certificate</p>
                    @if($training->certificate_provided)
                        <span class="text-xs bg-amber-50 text-amber-600 px-2 py-1 rounded-full">Provided on completion</span>
                    @else
                        <p class="text-sm text-gray-400">Not provided</p>
                    @endif
                </div>
            </div>

            {{-- Update Status --}}
            <div class="mt-4 pt-4 border-t border-gray-50">
                <form method="POST" action="{{ route('tenant.training.update', $training) }}">
                    @csrf
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Update Status</label>
                    <select name="status"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-2">
                        <option value="planned" {{ $training->status === 'planned' ? 'selected' : '' }}>Planned</option>
                        <option value="ongoing" {{ $training->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ $training->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $training->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit"
                            class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2 rounded-lg transition-colors">
                        Update Status
                    </button>
                </form>
            </div>
        </div>

        {{-- Description --}}
        @if($training->description)
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-3">Description</h2>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $training->description }}</p>
        </div>
        @endif

    </div>

</div>

@endsection
