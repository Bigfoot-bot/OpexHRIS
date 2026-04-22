@extends('central.layouts.app')

@section('page-title', 'Add Facility')
@section('page-subtitle', 'Onboard a new healthcare facility')

@section('page-actions')
    <a href="{{ route('admin.tenants.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Facilities
    </a>
@endsection

@section('content')

<div class="max-w-2xl">

    @if($errors->any())
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.tenants.store') }}">
        @csrf

        {{-- Facility Details --}}
        <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">

            <h2 class="text-sm font-medium text-emerald-900 mb-5">Facility Information</h2>

            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Facility Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                           placeholder="e.g. Nairobi West Hospital" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Facility Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                           placeholder="info@facility.com" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                           placeholder="+254 700 000 000" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Subscription Plan</label>
                    <select name="subscription_plan" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800
                                   focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="">Select a plan</option>
                        <option value="basic" {{ old('subscription_plan') === 'basic' ? 'selected' : '' }}>
                            Basic — Up to 15 employees (KES 10,000/mo)
                        </option>
                        <option value="professional" {{ old('subscription_plan') === 'professional' ? 'selected' : '' }}>
                            Professional — Up to 30 employees (KES 25,000/mo)
                        </option>
                        <option value="enterprise" {{ old('subscription_plan') === 'enterprise' ? 'selected' : '' }}>
                            Enterprise — Up to 100 employees (KES 90,000/mo)
                        </option>
                    </select>
                </div>

            </div>
        </div>

        {{-- Admin Account --}}
        <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">

            <h2 class="text-sm font-medium text-emerald-900 mb-1">Facility Admin Account</h2>
            <p class="text-xs text-gray-400 mb-5">This person will be the HR Admin for this facility.</p>

            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Admin Full Name</label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                           placeholder="e.g. Jane Mwangi" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Admin Email</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                           placeholder="admin@facility.com" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Admin Password</label>
                    <input type="password" name="admin_password" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                           placeholder="Minimum 8 characters" />
                </div>

            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium
                           px-6 py-2.5 rounded-lg transition-colors duration-150">
                Onboard Facility
            </button>
            <a href="{{ route('admin.tenants.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600">
                Cancel
            </a>
        </div>

    </form>

</div>

@endsection