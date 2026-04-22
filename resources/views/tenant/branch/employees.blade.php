@extends('tenant.branch.layout')
@section('page-title', 'Branch Employees')
@section('page-actions')
    <a href="{{ route('tenant.branch.employees.create', $branch) }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Add Employee
    </a>
@endsection
@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($employees->isEmpty())
        <div class="p-12 text-center"><p class="text-gray-400 text-sm">No employees in this branch.</p></div>
    @else
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Department</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Job Title</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($employees as $emp)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                                <span class="text-xs font-medium text-emerald-700">{{ substr($emp->first_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $emp->first_name }} {{ $emp->last_name }}</p>
                                <p class="text-xs text-gray-400">{{ $emp->employee_number }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $emp->department ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $emp->job_title ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2.5 py-1 rounded-full {{ $emp->employment_status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }} capitalize">{{ $emp->employment_status }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $employees->links() }}</div>
    @endif
</div>
@endsection



