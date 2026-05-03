@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Edit Employee')
@section('page-subtitle', $employee->full_name)

@section('page-actions')
    <a href="{{ route('tenant.employees.show', $employee) }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Profile
    </a>
@endsection

@section('content')

<form method="POST" action="{{ route('tenant.employees.update', $employee) }}">
@csrf
@method('PUT')

    @if($errors->any())
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-3 gap-5">

        {{-- Left Column --}}
        <div class="col-span-2 space-y-5">

            {{-- Personal Information --}}
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-5">Personal Information</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">First Name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name', $employee->middle_name) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone *</label>
                        <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Gender *</label>
                        <select name="gender" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Select</option>
                            <option value="male" {{ old('gender', $employee->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $employee->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $employee->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">National ID</label>
                        <input type="text" name="national_id" value="{{ old('national_id', $employee->national_id) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">KRA PIN</label>
                        <input type="text" name="kra_pin" value="{{ old('kra_pin', $employee->kra_pin) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">NHIF Number</label>
                        <input type="text" name="nhif_number" value="{{ old('nhif_number', $employee->nhif_number) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">NSSF Number</label>
                        <input type="text" name="nssf_number" value="{{ old('nssf_number', $employee->nssf_number) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>
            {{-- Bank Details --}}
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-5">Bank Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Bank Name</label>
                        <select name="bank_name" id="bank_name_select" data-current="{{ old('bank_name', $employee->bank_name) }}"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Select Bank</option>
                        </select>
                        <input type="text" id="bank_name_manual" name="bank_name_manual"
                               value="{{ old('bank_name_manual') }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mt-2"
                               placeholder="Enter bank name manually" style="display:none;"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Account Name</label>
                        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $employee->bank_account_name) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Account holder name"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Bank Branch</label>
                        <select name="bank_branch" id="bank_branch_select" data-current="{{ old('bank_branch', $employee->bank_branch) }}"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Select Branch</option>
                        </select>
                        <input type="text" name="bank_branch_manual" id="bank_branch_manual"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mt-2"
                               placeholder="Type branch name manually" style="display:none;"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Account Number</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $employee->bank_account_number) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Account number"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Bank Code</label>
                        <input type="text" name="bank_code" id="bank_code" value="{{ old('bank_code', $employee->bank_code) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Auto-filled on bank selection"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Branch Code</label>
                        <input type="text" name="branch_code" value="{{ old('branch_code', $employee->branch_code) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="e.g. 001, 002 (enter manually)"/>
                    </div>
                </div>
            </div>
            <script src="{{ asset('js/kenyan-banks.js') }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    initBankFields('bank_name_select', 'bank_branch_select', 'bank_branch_manual', 'bank_code', 'bank_name_manual');

                    // Handle form submission - merge branch and bank name values
                    document.querySelector('form').addEventListener('submit', function() {
                        const bankSelect  = document.getElementById('bank_name_select');
                        const bankManual  = document.getElementById('bank_name_manual');
                        if (bankSelect.value === 'Other' && bankManual.value.trim()) {
                            bankSelect.name = '';
                            bankManual.name = 'bank_name';
                        }

                        const branchSelect = document.getElementById('bank_branch_select');
                        const branchManual = document.getElementById('bank_branch_manual');
                        if (branchSelect.value === '__other__' && branchManual.value) {
                            branchSelect.name = '';
                            branchManual.name = 'bank_branch';
                        }
                    });
                });
            </script>
            </div>

            {{-- Employment Information --}}
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-5">Employment Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Department *</label>
                        <select name="department" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ old('department', $employee->department) === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Job Title *</label>
                        <input type="text" name="job_title" value="{{ old('job_title', $employee->job_title) }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Branch</label>
                        <select name="branch_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">No Branch (Head Office)</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $employee->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Employment Type *</label>
                        <select name="employment_type" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="permanent" {{ old('employment_type', $employee->employment_type) === 'permanent' ? 'selected' : '' }}>Permanent</option>
                            <option value="contract" {{ old('employment_type', $employee->employment_type) === 'contract' ? 'selected' : '' }}>Contract</option>
                            <option value="casual" {{ old('employment_type', $employee->employment_type) === 'casual' ? 'selected' : '' }}>Casual</option>
                            <option value="intern" {{ old('employment_type', $employee->employment_type) === 'intern' ? 'selected' : '' }}>Intern</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Employment Status *</label>
                        <select name="employment_status" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="probation" {{ old('employment_status', $employee->employment_status) === 'probation' ? 'selected' : '' }}>Probation</option>
                            <option value="active" {{ old('employment_status', $employee->employment_status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ old('employment_status', $employee->employment_status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="terminated" {{ old('employment_status', $employee->employment_status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                            <option value="resigned" {{ old('employment_status', $employee->employment_status) === 'resigned' ? 'selected' : '' }}>Resigned</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Hire Date *</label>
                        <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Basic Salary (KES)</label>
                        <input type="number" name="basic_salary" value="{{ old('basic_salary', $employee->basic_salary) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Confirmation Date</label>
                        <input type="date" name="confirmation_date" value="{{ old('confirmation_date', $employee->confirmation_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Work Location</label>
                        <input type="text" name="work_location" value="{{ old('work_location', $employee->work_location) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>
            </div>

            {{-- Healthcare Information --}}
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-5">Healthcare & Professional Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Professional Cadre</label>
                        <input type="text" name="professional_cadre" value="{{ old('professional_cadre', $employee->professional_cadre) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Registration Body</label>
                        <input type="text" name="registration_body" value="{{ old('registration_body', $employee->registration_body) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Registration Number</label>
                        <input type="text" name="registration_number" value="{{ old('registration_number', $employee->registration_number) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">License Expiry Date</label>
                        <input type="date" name="license_expiry_date" value="{{ old('license_expiry_date', $employee->license_expiry_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Specialty</label>
                        <input type="text" name="specialty" value="{{ old('specialty', $employee->specialty) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">CPD Points Required</label>
                        <input type="number" name="cpd_points_required" value="{{ old('cpd_points_required', $employee->cpd_points_required) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">CPD Points Earned</label>
                        <input type="number" name="cpd_points_earned" value="{{ old('cpd_points_earned', $employee->cpd_points_earned) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Column --}}
        <div class="space-y-5">

            {{-- Emergency Contact --}}
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <h2 class="text-sm font-medium text-emerald-900 mb-5">Emergency Contact</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Full Name</label>
                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone</label>
                        <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Relationship</label>
                        <input type="text" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="bg-white rounded-xl border border-green-100 p-6">
                <button type="submit"
                        class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium
                               py-2.5 rounded-lg transition-colors duration-150">
                    Update Employee
                </button>
                <a href="{{ route('tenant.employees.show', $employee) }}"
                   class="block text-center text-sm text-gray-400 hover:text-gray-600 mt-3">
                    Cancel
                </a>
            </div>

        </div>

    </div>

</form>

@endsection
