@extends('tenant.layouts.app')
@section('page-title', 'IP Whitelist')
@section('page-subtitle', 'Restrict access to specific IP addresses')
@section('content')
<div class="grid grid-cols-3 gap-6">
    {{-- Add IP Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-2">Add IP Address</h2>
        <p class="text-xs text-gray-400 mb-4">Leave empty to allow all IPs. Adding IPs will restrict access to only those listed.</p>
        @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('tenant.ip-whitelist.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">IP Address *</label>
                <input type="text" name="ip_address" required placeholder="e.g. 192.168.1.1 or 192.168.1.0/24"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                <p class="text-xs text-gray-400 mt-1">Supports CIDR notation (e.g. 192.168.1.0/24)</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Label</label>
                <input type="text" name="label" placeholder="e.g. Office Network"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Add IP</button>
        </form>

        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-500 font-medium mb-1">Your Current IP</p>
            <p class="text-sm font-mono text-emerald-700">{{ request()->ip() }}</p>
        </div>
    </div>

    {{-- IP List --}}
    <div class="col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-sm font-semibold text-gray-800">Whitelisted IPs</h2>
            @if($ips->isEmpty())
                <p class="text-xs text-emerald-600 mt-1">No IPs whitelisted - all IPs are allowed</p>
            @else
                <p class="text-xs text-amber-600 mt-1">Access restricted to {{ $ips->where('is_active', true)->count() }} whitelisted IP(s)</p>
            @endif
        </div>
        @if($ips->isEmpty())
            <div class="p-12 text-center"><p class="text-gray-400 text-sm">No IP restrictions configured.</p></div>
        @else
            <table class="w-full">
                <thead><tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">IP Address</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Label</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Added</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($ips as $ip)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 text-sm font-mono font-medium text-gray-800">{{ $ip->ip_address }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ip->label ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $ip->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $ip->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $ip->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <form method="POST" action="{{ route('tenant.ip-whitelist.toggle', $ip) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $ip->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('tenant.ip-whitelist.destroy', $ip) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Remove</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
