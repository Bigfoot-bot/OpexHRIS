@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Professional Licenses')
@section('page-subtitle', 'Track and manage staff professional licenses')

@section('page-actions')
    <a href="{{ route('tenant.licenses.create') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        + Add License
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
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
            <p class="text-2xl font-medium text-emerald-900">{{ $stats['valid'] }}</p>
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

    {{-- Licenses Table --}}
    <div class="bg-white rounded-xl border border-green-100">

        @if($licenses->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No licenses tracked yet.</p>
                <a href="{{ route('tenant.licenses.create') }}"
                   class="inline-block mt-3 text-sm text-emerald-600 hover:text-emerald-800">
                    Add first license →
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">License</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Issuing Body</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">License No.</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Expiry Date</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Days Left</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($licenses as $license)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-emerald-900">{{ $license->employee->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $license->employee->job_title }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700">{{ $license->license_name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500">{{ $license->issuing_body }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600 font-mono">{{ $license->license_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $license->expiry_date->format('M d, Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php $days = $license->days_until_expiry; @endphp
                            <span class="text-sm {{ $days < 0 ? 'text-red-500' : ($days <= 90 ? 'text-amber-600' : 'text-emerald-600') }}">
                                {{ $days < 0 ? abs($days) . ' days ago' : $days . ' days' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'valid'    => 'bg-emerald-50 text-emerald-600',
                                    'expiring' => 'bg-amber-50 text-amber-600',
                                    'expired'  => 'bg-red-50 text-red-500',
                                ];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $statusColors[$license->status] ?? '' }} capitalize">
                                {{ $license->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenant.licenses.edit', $license) }}"
                                   class="text-xs text-blue-500 hover:text-blue-700">Edit</a>
                                <form method="POST" action="{{ route('tenant.licenses.destroy', $license) }}"
                                      onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($licenses->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $licenses->links() }}
                </div>
            @endif
        @endif

    </div>

@endsection
