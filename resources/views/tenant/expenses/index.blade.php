@extends('tenant.layouts.app')
@section('page-title', 'Expense Claims')
@section('page-subtitle', 'Submit and manage expense reimbursements')
@section('page-actions')
    <a href="{{ route('tenant.expenses.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">+ New Claim</a>
@endsection
@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 text-red-700 text-sm rounded-lg px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Pending</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['approved'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Approved</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['paid'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Paid</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">KES {{ number_format($stats['total'], 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Paid</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex gap-3">
            <form method="GET" class="flex gap-2">
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:outline-none">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </form>
        </div>
        @if($claims->isEmpty())
            <div class="p-12 text-center"><p class="text-gray-400 text-sm">No expense claims yet.</p></div>
        @else
            <table class="w-full">
                <thead><tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Claim No.</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Employee</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Title</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Amount</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($claims as $claim)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 text-sm font-medium text-emerald-700">{{ $claim->claim_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $claim->employee->first_name ?? 'N/A' }} {{ $claim->employee->last_name ?? '' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $claim->title }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">KES {{ number_format($claim->total_amount, 0) }}</td>
                        <td class="px-6 py-4">
                            @php $colors = ['draft' => 'bg-gray-100 text-gray-600', 'submitted' => 'bg-amber-50 text-amber-600', 'approved' => 'bg-emerald-50 text-emerald-700', 'paid' => 'bg-blue-50 text-blue-700', 'rejected' => 'bg-red-50 text-red-600']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$claim->status] }} capitalize">{{ $claim->status }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenant.expenses.show', $claim) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">View</a>
                                @if($claim->status === 'draft')
                                <form method="POST" action="{{ route('tenant.expenses.destroy', $claim) }}">@csrf @method('DELETE')<button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button></form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $claims->links() }}</div>
        @endif
    </div>
</div>
@endsection

