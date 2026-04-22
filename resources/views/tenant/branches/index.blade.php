@extends('tenant.layouts.app')
@section('page-title', 'Branches')
@section('page-subtitle', 'Manage facility branches')
@section('page-actions')
    <a href="{{ route('tenant.branches.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + New Branch
    </a>
@endsection
@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    @if($branches->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-gray-400 text-sm">No branches yet.</p>
            <a href="{{ route('tenant.branches.create') }}" class="mt-3 inline-block text-emerald-600 text-sm hover:underline">Create your first branch</a>
        </div>
    @else
        <div class="grid grid-cols-3 gap-4">
            @foreach($branches as $branch)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ $branch->name }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $branch->address ?? 'No address' }}</p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $branch->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }} capitalize">
                        {{ $branch->status }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                        <p class="text-lg font-bold text-gray-800">{{ $branch->employees_count }}</p>
                        <p class="text-xs text-gray-400">Employees</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                        <p class="text-lg font-bold text-gray-800">{{ $branch->budgetAllocation ? 'KES ' . number_format($branch->budgetAllocation->allocated_amount, 0) : 'N/A' }}</p>
                        <p class="text-xs text-gray-400">Budget</p>
                    </div>
                </div>
                <div class="text-xs text-gray-400 mb-4">
                    <p>Manager: {{ $branch->manager->name ?? 'Not assigned' }}</p>
                    <p>Phone: {{ $branch->phone ?? 'N/A' }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('tenant.branches.show', $branch) }}" class="flex-1 text-center text-xs bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-medium px-3 py-1.5 rounded-lg">Manage</a>
                    <a href="{{ route('tenant.branch.dashboard', $branch) }}" class="flex-1 text-center text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 font-medium px-3 py-1.5 rounded-lg">Portal</a>
                    <a href="{{ route('tenant.branches.edit', $branch) }}" class="text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium px-3 py-1.5 rounded-lg">Edit</a>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

