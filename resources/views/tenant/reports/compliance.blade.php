@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Compliance Report')
@section('page-subtitle', 'License status, expiries and disciplinary cases')

@section('page-actions')
    <a href="{{ route('tenant.reports.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Reports
    </a>
@endsection

@section('content')

    {{-- License Stats --}}
    <div class="grid grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-xs text-gray-400 mb-1">Total Licenses</p>
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                <p class="text-xs text-gray-400">Valid</p>
            </div>
            <p class="text-2xl font-medium text-emerald-600">{{ $stats['valid'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                <p class="text-xs text-gray-400">Expiring Soon</p>
            </div>
            <p class="text-2xl font-medium text-amber-600">{{ $stats['expiring'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                <p class="text-xs text-gray-400">Expired</p>
            </div>
            <p class="text-2xl font-medium text-red-500">{{ $stats['expired'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-5 mb-5">

        {{-- Expiring Licenses --}}
        <div class="bg-white rounded-xl border border-green-100">
            <div class="px-6 py-4 border-b border-gray-50">
                <h2 class="text-sm font-medium text-amber-600">⚠ Expiring Soon (Within 90 Days)</h2>
            </div>
            @if($expiring->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">No expiring licenses.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Employee</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">License</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Expires</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Days Left</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($expiring as $license)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3">
                                <p class="text-sm font-medium text-emerald-900">{{ $license->employee->full_name }}</p>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $license->license_name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $license->expiry_date->format('M d, Y') }}</td>
                            <td class="px-6 py-3">
                                <span class="text-sm font-medium text-amber-600">{{ $license->days_until_expiry }} days</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Expired Licenses --}}
        <div class="bg-white rounded-xl border border-green-100">
            <div class="px-6 py-4 border-b border-gray-50">
                <h2 class="text-sm font-medium text-red-600">🔴 Expired Licenses</h2>
            </div>
            @if($expired->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">No expired licenses. ✅</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Employee</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">License</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-3">Expired On</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($expired as $license)
                        <tr class="hover:bg-red-50/30">
                            <td class="px-6 py-3">
                                <p class="text-sm font-medium text-red-700">{{ $license->employee->full_name }}</p>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $license->license_name }}</td>
                            <td class="px-6 py-3 text-sm text-red-500">{{ $license->expiry_date->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

    {{-- Disciplinary Summary --}}
    <div class="bg-white rounded-xl border border-green-100 p-6">
        <h2 class="text-sm font-medium text-emerald-900 mb-4">Disciplinary Cases Summary</h2>
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-medium text-emerald-900">{{ $disciplinary['total'] }}</p>
                <p class="text-xs text-gray-400 mt-1">Total Cases</p>
            </div>
            <div class="bg-amber-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-medium text-amber-600">{{ $disciplinary['open'] }}</p>
                <p class="text-xs text-gray-400 mt-1">Open Cases</p>
            </div>
            <div class="bg-emerald-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-medium text-emerald-600">{{ $disciplinary['closed'] }}</p>
                <p class="text-xs text-gray-400 mt-1">Closed Cases</p>
            </div>
        </div>
    </div>

@endsection
