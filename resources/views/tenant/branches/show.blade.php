@extends('tenant.layouts.app')
@section('page-title', $branch->name)
@section('page-subtitle', 'Branch management')
@section('page-actions')
    <div class="flex gap-2">
        <a href="{{ route('tenant.branch.dashboard', $branch) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Branch Portal</a>
        <a href="{{ route('tenant.branches.edit', $branch) }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">Edit</a>
    </div>
@endsection
@section('content')
<div class="grid grid-cols-3 gap-6">
    <div class="col-span-2 space-y-6">

        {{-- Branch Info --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Branch Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-xs text-gray-400">Address</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $branch->address ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-400">Phone</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $branch->phone ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-400">Email</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $branch->email ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-400">Manager</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $branch->manager->name ?? 'Not assigned' }}</p></div>
                <div><p class="text-xs text-gray-400">Status</p>
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $branch->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }} capitalize mt-1 inline-block">{{ $branch->status }}</span>
                </div>
                <div><p class="text-xs text-gray-400">Slug</p><p class="text-sm font-medium text-gray-800 mt-1">/branch/{{ $branch->slug }}</p></div>
            </div>
        </div>

        {{-- Employees --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-800">Employees ({{ $employees->count() }})</h2>
            </div>

            {{-- Assign Employee --}}
            <form method="POST" action="{{ route('tenant.branches.assign-employee', $branch) }}" class="flex gap-3 mb-4">
                @csrf
                <select name="employee_id" required class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Assign Employee to Branch</option>
                    @foreach(\App\Models\Tenant\Employee::where('tenant_id', tenant('id'))->whereNull('branch_id')->get() as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">Assign</button>
            </form>

            @if($employees->isEmpty())
                <p class="text-xs text-gray-400">No employees assigned to this branch yet.</p>
            @else
                <div class="space-y-2">
                    @foreach($employees as $emp)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                                <span class="text-xs font-medium text-emerald-700">{{ substr($emp->first_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $emp->first_name }} {{ $emp->last_name }}</p>
                                <p class="text-xs text-gray-400">{{ $emp->job_title ?? 'N/A' }} &middot; {{ $emp->department ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('tenant.branches.remove-employee', $branch) }}">
                            @csrf
                            <input type="hidden" name="employee_id" value="{{ $emp->id }}"/>
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700" onclick="return confirm('Remove from branch?')">Remove</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        {{-- Budget --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Budget Allocation</h2>
            @if($budget)
                <div class="space-y-3">
                    <div><p class="text-xs text-gray-400">Period</p><p class="text-sm font-medium text-gray-800">{{ $budget->period }}</p></div>
                    <div><p class="text-xs text-gray-400">Allocated</p><p class="text-sm font-medium text-gray-800">KES {{ number_format($budget->allocated_amount, 2) }}</p></div>
                    <div><p class="text-xs text-gray-400">Used</p><p class="text-sm font-medium text-gray-800">KES {{ number_format($budget->used_amount, 2) }}</p></div>
                    <div><p class="text-xs text-gray-400">Remaining</p><p class="text-sm font-medium text-emerald-700">KES {{ number_format($budget->remaining, 2) }}</p></div>
                    @php $pct = $budget->allocated_amount > 0 ? ($budget->used_amount / $budget->allocated_amount) * 100 : 0; @endphp
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ min($pct, 100) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400">{{ number_format($pct, 1) }}% used</p>
                </div>
            @else
                <p class="text-xs text-gray-400">No budget allocated.</p>
            @endif
        </div>

        {{-- Assign HR --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Assign Branch HR / Manager</h2>
            <form method="POST" action="{{ route('tenant.branches.assign-hr', $branch) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">User</label>
                    <select name="user_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select User</option>
                        @foreach($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Role</label>
                    <select name="role" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="branch_hr">Branch HR</option>
                        <option value="branch_manager">Branch Manager</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Assign</button>
            </form>
        </div>
    </div>
</div>
@endsection

