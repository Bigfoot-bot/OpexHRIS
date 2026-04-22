@extends('central.layouts.app')

@section('page-title', 'New Facility — Step 4 of 5')
@section('page-subtitle', 'First HR Admin')

@section('content')

    {{-- Progress --}}
    <div class="flex items-center gap-2 mb-8">
        @foreach([1,2,3,4,5] as $step)
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium
                {{ $step < 4 ? 'bg-emerald-700 text-white' : ($step === 4 ? 'bg-emerald-700 text-white' : 'bg-gray-100 text-gray-400') }}">
                @if($step < 4)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @else
                    {{ $step }}
                @endif
            </div>
            @if($step < 5)
            <div class="w-12 h-0.5 {{ $step < 4 ? 'bg-emerald-700' : 'bg-gray-100' }}"></div>
            @endif
        </div>
        @endforeach
        <div class="ml-3 text-sm text-gray-500">HR Admin Account</div>
    </div>

    <div class="max-w-2xl">

        @if($errors->any())
            <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.onboarding.step4.store') }}">
            @csrf

            <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
                <h2 class="text-sm font-medium text-emerald-900 mb-2">Create First HR Admin</h2>
                <p class="text-xs text-gray-400 mb-5">This person will manage the facility HR portal.</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Full Name *</label>
                        <input type="text" name="admin_name" value="{{ old('admin_name', session('wizard.step4.admin_name')) }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Email Address *</label>
                        <input type="email" name="admin_email" value="{{ old('admin_email', session('wizard.step4.admin_email')) }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Password *</label>
                        <input type="password" name="admin_password" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Minimum 8 characters"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Confirm Password *</label>
                        <input type="password" name="admin_password_confirmation" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Repeat password"/>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.onboarding.step3') }}" class="text-sm text-gray-400 hover:text-gray-600">? Back</a>
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Next: Review ?
                </button>
            </div>

        </form>
    </div>

@endsection
