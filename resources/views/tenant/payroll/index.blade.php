@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Payroll')
@section('page-subtitle', 'Manage monthly payroll runs')

@section('page-actions')
    <a href="{{ route('tenant.payroll.create') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        + New Payroll Run
    </a>
@endsection

@section('content')

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-green-100 p-4 mb-4">
        <form method="GET" class="flex gap-3 flex-wrap">
            @if(isset($branches) && $branches->count() > 0)
            <select name="branch_id" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">All Branches</option>
                @foreach($branches as $br)
                    <option value="{{ $br->id }}" {{ request('branch_id') == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                @endforeach
            </select>
            @endif
            <button type="submit" class="bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
            @if(request('branch_id'))
            <a href="{{ route('tenant.payroll.index') }}" class="bg-gray-100 text-gray-600 text-sm px-4 py-2 rounded-lg">Clear</a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-green-100">

        @if($periods->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No payroll runs yet.</p>
                <a href="{{ route('tenant.payroll.create') }}"
                   class="inline-block mt-3 text-sm text-emerald-600 hover:text-emerald-800">
                    Run first payroll →
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Period</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employees</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Gross Payroll</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Net Payroll</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Payment Date</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($periods as $period)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-emerald-900">{{ $period->name }}</p>
                            <p class="text-xs text-gray-400">{{ $period->start_date->format('M d') }} — {{ $period->end_date->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $period->records->count() }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700">KES {{ number_format($period->records->sum('gross_salary')) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-emerald-900">KES {{ number_format($period->records->sum('net_salary')) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500">{{ $period->payment_date ? $period->payment_date->format('M d, Y') : '—' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'draft'      => 'bg-gray-50 text-gray-500',
                                    'processing' => 'bg-blue-50 text-blue-600',
                                    'approved'   => 'bg-emerald-50 text-emerald-600',
                                    'paid'       => 'bg-teal-50 text-teal-600',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$period->status] ?? '' }} capitalize">
                                {{ $period->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('tenant.payroll.show', $period) }}"
                               class="text-xs text-emerald-600 hover:text-emerald-800">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($periods->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $periods->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection
