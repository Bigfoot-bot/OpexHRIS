@extends('central.layouts.app')

@section('page-title', 'New Facility — Step 2 of 5')
@section('page-subtitle', 'Departments')

@section('content')

    {{-- Progress --}}
    <div class="flex items-center gap-2 mb-8">
        @foreach([1,2,3,4,5] as $step)
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium
                {{ $step < 2 ? 'bg-emerald-700 text-white' : ($step === 2 ? 'bg-emerald-700 text-white' : 'bg-gray-100 text-gray-400') }}">
                @if($step < 2)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @else
                    {{ $step }}
                @endif
            </div>
            @if($step < 5)
            <div class="w-12 h-0.5 {{ $step < 2 ? 'bg-emerald-700' : 'bg-gray-100' }}"></div>
            @endif
        </div>
        @endforeach
        <div class="ml-3 text-sm text-gray-500">Departments</div>
    </div>

    <div class="max-w-2xl">

        @if($errors->any())
            <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.onboarding.step2.store') }}">
            @csrf

            <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-sm font-medium text-emerald-900">Add Departments</h2>
                    <button type="button" onclick="addDepartment()"
                            class="text-xs text-emerald-600 hover:text-emerald-800">+ Add Department</button>
                </div>

                <div id="departments-list" class="space-y-3">
                    @php $defaultDepts = ['Nursing', 'Medical', 'Pharmacy', 'Laboratory', 'Administration', 'HR']; @endphp
                    @foreach($defaultDepts as $index => $dept)
                    <div class="flex items-center gap-3 department-row">
                        <input type="text" name="departments[]" value="{{ $dept }}"
                               class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Department name"/>
                        <button type="button" onclick="removeDepartment(this)"
                                class="text-red-400 hover:text-red-600 text-xs">Remove</button>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.onboarding.step1') }}" class="text-sm text-gray-400 hover:text-gray-600">? Back</a>
                <button type="submit"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Next: Leave Types ?
                </button>
            </div>

        </form>
    </div>

    <script>
        function addDepartment() {
            const list = document.getElementById('departments-list');
            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 department-row';
            div.innerHTML = `
                <input type="text" name="departments[]"
                       class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Department name"/>
                <button type="button" onclick="removeDepartment(this)"
                        class="text-red-400 hover:text-red-600 text-xs">Remove</button>
            `;
            list.appendChild(div);
        }

        function removeDepartment(btn) {
            btn.closest('.department-row').remove();
        }
    </script>

@endsection
