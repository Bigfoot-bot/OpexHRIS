@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'New Payroll Run')
@section('page-subtitle', 'Generate payroll for a specific month')

@section('page-actions')
    <a href="{{ route('tenant.payroll.index') }}"
       class="text-sm text-gray-500 hover:text-emerald-700">
        ← Back to Payroll
    </a>
@endsection

@section('content')

<div class="max-w-lg">

    @if($errors->any())
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tenant.payroll.store') }}">
        @csrf

        <div class="bg-white rounded-xl border border-green-100 p-6 mb-5">
            <h2 class="text-sm font-medium text-emerald-900 mb-5">Payroll Period</h2>

            <div class="space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Month *</label>
                        <select name="month" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ old('month', date('n')) == $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Year *</label>
                        <select name="year" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ old('year', date('Y')) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>

                <div class="bg-emerald-50 rounded-lg p-4">
                    <p class="text-xs text-emerald-700 font-medium mb-1">What happens when you run payroll?</p>
                    <ul class="text-xs text-emerald-600 space-y-1">
                        <li>• Payroll records generated for all active employees</li>
                        <li>• PAYE, NHIF, NSSF & Housing Levy auto-calculated</li>
                        <li>• Net salary computed per employee</li>
                        <li>• Records can be adjusted before approval</li>
                    </ul>
                </div>

            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition-colors duration-150">
                Generate Payroll
            </button>
            <a href="{{ route('tenant.payroll.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600">Cancel</a>
        </div>

    </form>
</div>

@endsection
