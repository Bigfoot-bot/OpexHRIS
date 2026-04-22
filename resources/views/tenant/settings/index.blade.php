@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Settings')
@section('page-subtitle', 'Manage your facility profile and preferences')

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabs --}}
    @php $tab = request('tab', 'profile'); @endphp
    <div class="flex items-center gap-1 mb-6 bg-white rounded-xl border border-green-100 p-1">
        @foreach([
            'profile'  => 'Facility Profile',
            'departments' => 'Departments',
            'leave'    => 'Leave Policy',
            'payroll'  => 'Payroll',
            'holidays' => 'Public Holidays',
            'password' => 'Password',
        ] as $key => $label)
        <a href="{{ route('tenant.settings.index') }}?tab={{ $key }}"
           class="flex-1 text-center text-sm py-2 rounded-lg transition-colors
           {{ $tab === $key ? 'bg-emerald-700 text-white font-medium' : 'text-gray-500 hover:text-emerald-700' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Profile Tab --}}
    @if($tab === 'profile')
    <div class="grid grid-cols-3 gap-5">
        <div class="col-span-2">
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-5">Facility Profile</h2>
                <form method="POST" action="{{ route('tenant.settings.update') }}">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Facility Name *</label>
                            <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ old('email', $tenant->email) }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">County</label>
                            <input type="text" name="county" value="{{ old('county', $tenant->county ?? '') }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Facility Type</label>
                            <select name="facility_type"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Select Type</option>
                                @foreach(['hospital' => 'Hospital', 'clinic' => 'Clinic', 'health_centre' => 'Health Centre', 'dispensary' => 'Dispensary', 'nursing_home' => 'Nursing Home', 'specialist_clinic' => 'Specialist Clinic'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($tenant->facility_type ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">KEPH Level</label>
                            <select name="keph_level"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Select Level</option>
                                @foreach(['level_2' => 'Level 2 — Dispensary', 'level_3' => 'Level 3 — Health Centre', 'level_4' => 'Level 4 — Sub-County Hospital', 'level_5' => 'Level 5 — County Hospital', 'level_6' => 'Level 6 — National Referral'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($tenant->keph_level ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Bed Capacity</label>
                            <input type="number" name="bed_capacity" value="{{ old('bed_capacity', $tenant->bed_capacity ?? '') }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Address</label>
                            <textarea name="address" rows="2"
                                      class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('address', $tenant->address ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit"
                                class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
            {{-- Logo Upload --}}
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-4">Facility Logo <span class="text-xs text-gray-400 font-normal">(shown in sidebar & payslips)</span></h2>
                <div class="mb-4">
                    @if(tenant()->logo && file_exists(public_path('logos/' . tenant()->logo)))
                        <img src="{{ asset('logos/' . tenant()->logo) }}" alt="Facility Logo"
                             class="h-16 object-contain mb-3 border border-gray-100 rounded-lg p-2"/>
                        <p class="text-xs text-gray-400">Current logo</p>
                    @else
                        <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-400">No logo uploaded yet</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('tenant.settings.logo') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Upload Logo</label>
                            <input type="file" name="logo" accept="image/png,image/jpg,image/jpeg"
                                   class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"/>
                            <p class="text-xs text-gray-400 mt-1">PNG or JPG, max 2MB. Recommended: 200x200px</p>
                            @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit"
                                class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                            Upload Logo
                        </button>
                    </div>
                </form>
            </div>

        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-4">Facility Info</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-400">Tenant ID</p>
                        <p class="text-xs font-mono text-gray-600 break-all">{{ $tenant->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Subdomain</p>
                        <p class="text-sm text-gray-600">{{ $tenant->slug }}.hris-platform.test</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Plan</p>
                        <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 capitalize">
                            {{ $tenant->subscription_plan ?? 'Basic' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Member Since</p>
                        <p class="text-sm text-gray-600">{{ $tenant->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Departments Tab --}}
    @if($tab === 'departments')
    <div class="bg-white rounded-xl border border-green-100 p-6">
        <h2 class="text-sm font-medium text-emerald-900 mb-5">Departments</h2>
        <form method="POST" action="{{ route('tenant.settings.departments') }}" id="dept-form">
            @csrf
            <div id="departments-list" class="space-y-2 mb-4">
                @foreach($departments as $index => $dept)
                <div class="flex items-center gap-2 dept-row">
                    <input type="text" name="departments[]" value="{{ $dept }}" required
                           class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    <button type="button" onclick="this.closest('.dept-row').remove()"
                            class="text-gray-300 hover:text-red-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                @endforeach
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="addDept()"
                        class="text-sm text-emerald-600 hover:text-emerald-800">
                    + Add Department
                </button>
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg">
                    Save Departments
                </button>
            </div>
        </form>
    </div>
    <script>
        function addDept() {
            const list = document.getElementById('departments-list');
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 dept-row';
            row.innerHTML = `
                <input type="text" name="departments[]" required
                       class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Department name"/>
                <button type="button" onclick="this.closest('.dept-row').remove()"
                        class="text-gray-300 hover:text-red-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>`;
            list.appendChild(row);
        }
    </script>
    @endif

    {{-- Leave Policy Tab --}}
    @if($tab === 'leave')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Leave Policy</h2>
            <form method="POST" action="{{ route('tenant.settings.leave-policy') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Leave Year Start (MM-DD)</label>
                        <input type="text" name="leave_year_start"
                               value="{{ old('leave_year_start', $leavePolicy['leave_year_start']) }}"
                               placeholder="01-01"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        <p class="text-xs text-gray-400 mt-1">Format: MM-DD e.g. 01-01 for January 1st</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="carry_forward" id="carry_forward" value="1"
                               {{ $leavePolicy['carry_forward'] ? 'checked' : '' }}
                               class="w-4 h-4 text-emerald-600 rounded"/>
                        <label for="carry_forward" class="text-sm text-gray-600">Allow carry forward of unused leave</label>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Max Carry Forward Days</label>
                        <input type="number" name="max_carry_forward" min="0"
                               value="{{ old('max_carry_forward', $leavePolicy['max_carry_forward']) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Auto Approve After (Days)</label>
                        <input type="number" name="auto_approve_after_days" min="0"
                               value="{{ old('auto_approve_after_days', $leavePolicy['auto_approve_after_days']) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        <p class="text-xs text-gray-400 mt-1">Set to 0 to disable auto approval</p>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit"
                            class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg">
                        Save Leave Policy
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Payroll Settings Tab --}}
    @if($tab === 'payroll')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Payroll Settings</h2>
            <form method="POST" action="{{ route('tenant.settings.payroll') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Pay Day (Day of Month)</label>
                        <input type="number" name="pay_day" min="1" max="31"
                               value="{{ old('pay_day', $payrollSettings['pay_day']) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        <p class="text-xs text-gray-400 mt-1">e.g. 28 means salaries are paid on the 28th of every month</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Currency</label>
                        <select name="currency"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @foreach(['KES' => 'KES — Kenyan Shilling', 'USD' => 'USD — US Dollar', 'UGX' => 'UGX — Ugandan Shilling', 'TZS' => 'TZS — Tanzanian Shilling'] as $val => $label)
                                <option value="{{ $val }}" {{ $payrollSettings['currency'] === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Payroll Notification Email</label>
                        <input type="email" name="payroll_email"
                               value="{{ old('payroll_email', $payrollSettings['payroll_email']) }}"
                               placeholder="e.g. hr@hospital.com"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit"
                            class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg">
                        Save Payroll Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Public Holidays Tab --}}
    @if($tab === 'holidays')
    <div class="bg-white rounded-xl border border-green-100 p-6">
        <h2 class="text-sm font-medium text-emerald-900 mb-5">Public Holidays</h2>
        <form method="POST" action="{{ route('tenant.settings.holidays') }}" id="holidays-form">
            @csrf
            <div id="holidays-list" class="space-y-2 mb-4">
                <div class="grid grid-cols-2 gap-2 text-xs text-gray-400 px-1 mb-1">
                    <span>Holiday Name</span>
                    <span>Date</span>
                </div>
                @foreach($publicHolidays as $index => $holiday)
                <div class="flex items-center gap-2 holiday-row">
                    <input type="text" name="holidays[{{ $index }}][name]"
                           value="{{ $holiday['name'] }}" required
                           class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    <input type="date" name="holidays[{{ $index }}][date]"
                           value="{{ $holiday['date'] }}" required
                           class="w-44 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    <button type="button" onclick="this.closest('.holiday-row').remove()"
                            class="text-gray-300 hover:text-red-400 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                @endforeach
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="addHoliday()"
                        class="text-sm text-emerald-600 hover:text-emerald-800">
                    + Add Holiday
                </button>
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg">
                    Save Holidays
                </button>
            </div>
        </form>
    </div>
    <script>
        let holidayCount = {{ count($publicHolidays) }};
        function addHoliday() {
            const list = document.getElementById('holidays-list');
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 holiday-row';
            row.innerHTML = `
                <input type="text" name="holidays[${holidayCount}][name]" required
                       class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Holiday name"/>
                <input type="date" name="holidays[${holidayCount}][date]" required
                       class="w-44 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                <button type="button" onclick="this.closest('.holiday-row').remove()"
                        class="text-gray-300 hover:text-red-400 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>`;
            list.appendChild(row);
            holidayCount++;
        }
    </script>
    @endif

    {{-- Password Tab --}}
    @if($tab === 'password')
    <div class="max-w-lg">
        <div class="bg-white rounded-xl border border-green-100 p-6">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Change Password</h2>
            <form method="POST" action="{{ route('tenant.settings.password') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Current Password *</label>
                        <input type="password" name="current_password" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">New Password *</label>
                        <input type="password" name="password" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit"
                            class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

@endsection



