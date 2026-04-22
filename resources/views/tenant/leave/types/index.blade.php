@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'Leave Types')
@section('page-subtitle', 'Configure leave types for your facility')

@section('page-actions')
    <a href="{{ route('tenant.leave-types.create') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150">
        + Add Leave Type
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-green-100">

        @if($leaveTypes->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No leave types configured yet.</p>
                <a href="{{ route('tenant.leave-types.create') }}"
                   class="inline-block mt-3 text-sm text-emerald-600 hover:text-emerald-800">
                    Add your first leave type →
                </a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Name</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Code</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Days Allowed</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Paid</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Carry Forward</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($leaveTypes as $leaveType)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-emerald-900">{{ $leaveType->name }}</p>
                            @if($leaveType->description)
                                <p class="text-xs text-gray-400">{{ Str::limit($leaveType->description, 50) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500">{{ $leaveType->code ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700">{{ $leaveType->days_allowed }} days</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($leaveType->is_paid)
                                <span class="text-xs bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full">Paid</span>
                            @else
                                <span class="text-xs bg-gray-50 text-gray-500 px-2.5 py-1 rounded-full">Unpaid</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($leaveType->carry_forward)
                                <span class="text-xs bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full">Yes ({{ $leaveType->max_carry_forward_days }} days)</span>
                            @else
                                <span class="text-xs text-gray-400">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($leaveType->is_active)
                                <span class="text-xs bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full">Active</span>
                            @else
                                <span class="text-xs bg-red-50 text-red-500 px-2.5 py-1 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenant.leave-types.edit', $leaveType) }}"
                                   class="text-xs text-blue-500 hover:text-blue-700">Edit</a>
                                <form method="POST" action="{{ route('tenant.leave-types.destroy', $leaveType) }}"
                                      onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>

@endsection
