@extends('central.layouts.app')
@section('page-title', 'MFA Settings')
@section('page-subtitle', 'Manage Multi-Factor Authentication per facility')
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>@endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-1">About MFA</h2>
        <p class="text-xs text-gray-400">When MFA is forced for a facility, all users must verify their identity via email code on every login. Individual users can also enable MFA from their own accounts.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-sm font-semibold text-gray-800">Facility MFA Settings</h2>
        </div>
        <table class="w-full">
            <thead><tr class="border-b border-gray-50">
                <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Facility</th>
                <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Domain</th>
                <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">MFA Forced</th>
                <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Action</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($tenants as $tenant)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $tenant->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $tenant->domains->first()->domain ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $tenant->mfa_forced ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $tenant->mfa_forced ? 'Forced' : 'Optional' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <form method="POST" action="{{ route('admin.mfa.update') }}" class="flex items-center gap-3">
                            @csrf
                            <input type="hidden" name="tenant_id" value="{{ $tenant->id }}"/>
                            <input type="hidden" name="mfa_forced" value="{{ $tenant->mfa_forced ? '0' : '1' }}"/>
                            <button type="submit" style="background-color:{{ $tenant->mfa_forced ? '#dc2626' : '#064e3b' }};color:white;font-size:0.75rem;font-weight:500;padding:0.375rem 0.75rem;border-radius:0.5rem;border:none;cursor:pointer;">
                                {{ $tenant->mfa_forced ? 'Disable Force' : 'Force MFA' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
