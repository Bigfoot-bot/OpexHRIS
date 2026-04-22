@extends('central.layouts.app')

@section('page-title', 'New Facility — Step 5 of 5')
@section('page-subtitle', 'Review & Complete')

@section('content')

    {{-- Progress --}}
    <div class="flex items-center gap-2 mb-8">
        @foreach([1,2,3,4,5] as $step)
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium bg-emerald-700 text-white">
                @if($step < 5)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @else
                    5
                @endif
            </div>
            @if($step < 5)
            <div class="w-12 h-0.5 bg-emerald-700"></div>
            @endif
        </div>
        @endforeach
        <div class="ml-3 text-sm text-gray-500">Review & Launch</div>
    </div>

    <div class="max-w-2xl">

        <div class="space-y-5 mb-6">

            {{-- Facility Profile --}}
            <div class="bg-white rounded-xl border border-green-100 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-emerald-900">Facility Profile</h3>
                    <a href="{{ route('admin.onboarding.step1') }}" class="text-xs text-emerald-600 hover:text-emerald-800">Edit</a>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><p class="text-xs text-gray-400">Name</p><p class="text-gray-700">{{ $data['step1']['name'] }}</p></div>
                    <div><p class="text-xs text-gray-400">Type</p><p class="text-gray-700 capitalize">{{ str_replace('_', ' ', $data['step1']['facility_type']) }}</p></div>
                    <div><p class="text-xs text-gray-400">Email</p><p class="text-gray-700">{{ $data['step1']['email'] }}</p></div>
                    <div><p class="text-xs text-gray-400">Phone</p><p class="text-gray-700">{{ $data['step1']['phone'] }}</p></div>
                    <div><p class="text-xs text-gray-400">County</p><p class="text-gray-700">{{ $data['step1']['county'] }}</p></div>
                    <div><p class="text-xs text-gray-400">Plan</p><p class="text-gray-700 capitalize">{{ $data['step1']['subscription_plan'] }}</p></div>
                </div>
            </div>

            {{-- Departments --}}
            <div class="bg-white rounded-xl border border-green-100 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-emerald-900">Departments ({{ count($data['step2']) }})</h3>
                    <a href="{{ route('admin.onboarding.step2') }}" class="text-xs text-emerald-600 hover:text-emerald-800">Edit</a>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($data['step2'] as $dept)
                    <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">{{ $dept }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Leave Types --}}
            <div class="bg-white rounded-xl border border-green-100 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-emerald-900">Leave Types ({{ count($data['step3']) }})</h3>
                    <a href="{{ route('admin.onboarding.step3') }}" class="text-xs text-emerald-600 hover:text-emerald-800">Edit</a>
                </div>
                <div class="space-y-2">
                    @foreach($data['step3'] as $leave)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ $leave['name'] }}</span>
                        <span class="text-xs text-gray-400">{{ $leave['days'] }} days</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- HR Admin --}}
            <div class="bg-white rounded-xl border border-green-100 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-emerald-900">HR Admin Account</h3>
                    <a href="{{ route('admin.onboarding.step4') }}" class="text-xs text-emerald-600 hover:text-emerald-800">Edit</a>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><p class="text-xs text-gray-400">Name</p><p class="text-gray-700">{{ $data['step4']['admin_name'] }}</p></div>
                    <div><p class="text-xs text-gray-400">Email</p><p class="text-gray-700">{{ $data['step4']['admin_email'] }}</p></div>
                </div>
            </div>

        </div>

        <form method="POST" action="{{ route('admin.onboarding.complete') }}">
            @csrf
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.onboarding.step4') }}" class="text-sm text-gray-400 hover:text-gray-600">? Back</a>
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-8 py-2.5 rounded-lg transition-colors">
                    ?? Launch Facility
                </button>
            </div>
        </form>

    </div>

@endsection
