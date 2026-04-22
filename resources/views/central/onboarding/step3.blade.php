@extends('central.layouts.app')

@section('page-title', 'New Facility — Step 3 of 5')
@section('page-subtitle', 'Leave Types')

@section('content')

    {{-- Progress --}}
    <div class="flex items-center gap-2 mb-8">
        @foreach([1,2,3,4,5] as $step)
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium
                {{ $step < 3 ? 'bg-emerald-700 text-white' : ($step === 3 ? 'bg-emerald-700 text-white' : 'bg-gray-100 text-gray-400') }}">
                @if($step < 3)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @else
                    {{ $step }}
                @endif
            </div>
            @if($step < 5)
            <div class="w-12 h-0.5 {{ $step < 3 ? 'bg-emerald-700' : 'bg-gray-100' }}"></div>
            @endif
        </div>
        @endforeach
        <div class="ml-3 text-sm text-gray-500">Leave Types</div>
    </div>

    <div class="max-w-2xl">

        @if($errors->any())
            <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.onboarding.step3.store') }}">
            @csrf

            <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-sm font-medium text-emerald-900">Configure Leave Types</h2>
                    <button type="button" onclick="addLeaveType()"
                            class="text-xs text-emerald-600 hover:text-emerald-800">+ Add Leave Type</button>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <p class="text-xs text-gray-400 font-medium">Leave Type</p>
                    <p class="text-xs text-gray-400 font-medium">Days Allowed</p>
                </div>

                <div id="leave-types-list" class="space-y-3">
                    @php
                        $defaultLeaves = [
                            ['name' => 'Annual Leave', 'days' => 21],
                            ['name' => 'Sick Leave', 'days' => 14],
                            ['name' => 'Maternity Leave', 'days' => 90],
                            ['name' => 'Paternity Leave', 'days' => 14],
                            ['name' => 'Compassionate Leave', 'days' => 3],
                        ];
                    @endphp
                    @foreach($defaultLeaves as $index => $leave)
                    <div class="flex items-center gap-3 leave-row">
                        <input type="text" name="leave_types[{{ $index }}][name]" value="{{ $leave['name'] }}"
                               class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Leave type name"/>
                        <input type="number" name="leave_types[{{ $index }}][days]" value="{{ $leave['days'] }}"
                               class="w-24 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Days"/>
                        <button type="button" onclick="removeLeaveType(this)"
                                class="text-red-400 hover:text-red-600 text-xs">Remove</button>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.onboarding.step2') }}" class="text-sm text-gray-400 hover:text-gray-600">? Back</a>
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Next: HR Admin ?
                </button>
            </div>

        </form>
    </div>

    <script>
        let leaveCount = {{ count($defaultLeaves) }};

        function addLeaveType() {
            const list = document.getElementById('leave-types-list');
            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 leave-row';
            div.innerHTML = `
                <input type="text" name="leave_types[${leaveCount}][name]"
                       class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Leave type name"/>
                <input type="number" name="leave_types[${leaveCount}][days]"
                       class="w-24 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Days"/>
                <button type="button" onclick="removeLeaveType(this)"
                        class="text-red-400 hover:text-red-600 text-xs">Remove</button>
            `;
            list.appendChild(div);
            leaveCount++;
        }

        function removeLeaveType(btn) {
            btn.closest('.leave-row').remove();
        }
    </script>

@endsection
