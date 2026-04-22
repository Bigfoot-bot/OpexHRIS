@extends('tenant.layouts.app')
@section('page-title', 'Separation Details')
@section('page-subtitle', ($separation->employee->first_name ?? '') . ' ' . ($separation->employee->last_name ?? '') . ' - ' . ucfirst(str_replace('_', ' ', $separation->type)))
@section('page-actions')
    @if($separation->status === 'cleared')
        <form method="POST" action="{{ route('tenant.separations.complete', $separation) }}" class="inline">@csrf<button type="submit" style="background-color:#064e3b;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1rem;border-radius:0.5rem;border:none;cursor:pointer;">Complete Separation</button></form>
    @endif
    <form method="POST" action="{{ route('tenant.separations.certificate', $separation) }}" class="inline">@csrf<button type="submit" style="background-color:#1d4ed8;color:white;font-size:0.875rem;font-weight:500;padding:0.5rem 1rem;border-radius:0.5rem;border:none;cursor:pointer;">Certificate of Service</button></form>
    <a href="{{ route('tenant.separations.index') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">Back</a>
@endsection
@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>@endif

    <div class="grid grid-cols-3 gap-6">
        {{-- Left Column --}}
        <div class="col-span-2 space-y-6">
            {{-- Clearance Checklist --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-semibold text-gray-800">Clearance Checklist</h2>
                    <p class="text-xs text-gray-400 mt-1">{{ $separation->clearanceItems->where('status', 'cleared')->count() }}/{{ $separation->clearanceItems->count() }} items cleared</p>
                </div>
                @php $departments = $separation->clearanceItems->groupBy('department'); @endphp
                @foreach($departments as $dept => $items)
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-600">{{ $dept }}</p>
                </div>
                @foreach($items as $item)
                <div class="px-6 py-3 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($item->status === 'cleared')
                            <div class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        @elseif($item->status === 'waived')
                            <div class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </div>
                        @else
                            <div class="w-5 h-5 rounded-full border-2 border-gray-200"></div>
                        @endif
                        <span class="text-sm text-gray-700 {{ $item->status !== 'pending' ? 'line-through text-gray-400' : '' }}">{{ $item->item }}</span>
                    </div>
                    @if($item->status === 'pending')
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('tenant.separations.clear-item', $item) }}">
                            @csrf
                            <input type="hidden" name="status" value="cleared"/>
                            <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Clear</button>
                        </form>
                        <form method="POST" action="{{ route('tenant.separations.clear-item', $item) }}">
                            @csrf
                            <input type="hidden" name="status" value="waived"/>
                            <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 font-medium">Waive</button>
                        </form>
                    </div>
                    @else
                        <span class="text-xs text-gray-400 capitalize">{{ $item->status }}</span>
                    @endif
                </div>
                @endforeach
                @endforeach
            </div>

            {{-- Exit Interview --}}
            @if($separation->exitInterview)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">Exit Interview</h2>
                @if($separation->exitInterview->is_submitted)
                    <div class="space-y-3 text-sm">
                        <div class="grid grid-cols-5 gap-3">
                            @foreach(['rating_overall' => 'Overall', 'rating_management' => 'Management', 'rating_work_environment' => 'Work Env', 'rating_compensation' => 'Pay', 'rating_growth' => 'Growth'] as $field => $label)
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <p class="text-xl font-bold text-emerald-600">{{ $separation->exitInterview->$field ?? '-' }}</p>
                                <p class="text-xs text-gray-400">{{ $label }}</p>
                            </div>
                            @endforeach
                        </div>
                        @if($separation->exitInterview->reason_leaving)
                        <div><p class="text-xs text-gray-400 mb-1">Reason for Leaving</p><p class="text-sm text-gray-700">{{ $separation->exitInterview->reason_leaving }}</p></div>
                        @endif
                        @if($separation->exitInterview->what_worked_well)
                        <div><p class="text-xs text-gray-400 mb-1">What Worked Well</p><p class="text-sm text-gray-700">{{ $separation->exitInterview->what_worked_well }}</p></div>
                        @endif
                        @if($separation->exitInterview->what_could_improve)
                        <div><p class="text-xs text-gray-400 mb-1">Areas for Improvement</p><p class="text-sm text-gray-700">{{ $separation->exitInterview->what_could_improve }}</p></div>
                        @endif
                        <div class="flex gap-4 text-xs text-gray-500">
                            <span>Would recommend: <strong>{{ $separation->exitInterview->would_recommend ?? 'N/A' }}</strong></span>
                            <span>Would return: <strong>{{ $separation->exitInterview->would_return ? 'Yes' : 'No' }}</strong></span>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('tenant.separations.exit-interview', $separation->exitInterview) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-5 gap-3">
                            @foreach(['rating_overall' => 'Overall', 'rating_management' => 'Management', 'rating_work_environment' => 'Work Env', 'rating_compensation' => 'Pay', 'rating_growth' => 'Growth'] as $field => $label)
                            <div>
                                <label class="block text-xs text-gray-500 mb-1 text-center">{{ $label }} (1-5)</label>
                                <input type="number" name="{{ $field }}" min="1" max="5" class="w-full px-2 py-1.5 rounded border border-gray-200 text-sm text-center focus:outline-none focus:ring-1 focus:ring-emerald-500"/>
                            </div>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Reason for Leaving</label>
                                <textarea name="reason_leaving" rows="2" class="w-full px-2 py-1.5 rounded border border-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">What Worked Well</label>
                                <textarea name="what_worked_well" rows="2" class="w-full px-2 py-1.5 rounded border border-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">What Could Improve</label>
                                <textarea name="what_could_improve" rows="2" class="w-full px-2 py-1.5 rounded border border-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Would you recommend us?</label>
                                <textarea name="would_recommend" rows="2" class="w-full px-2 py-1.5 rounded border border-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"></textarea>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="would_return" id="would_return" class="rounded"/>
                            <label for="would_return" class="text-sm text-gray-600">Would consider returning to the organization</label>
                        </div>
                        <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Submit Exit Interview</button>
                    </form>
                @endif
            </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">Separation Details</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-400">Employee</span><span class="font-medium">{{ $separation->employee->first_name ?? 'N/A' }} {{ $separation->employee->last_name ?? '' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Type</span><span class="capitalize">{{ str_replace('_', ' ', $separation->type) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Notice Date</span><span>{{ $separation->notice_date->format('M d, Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Last Working Day</span><span>{{ $separation->last_working_date->format('M d, Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Effective Date</span><span>{{ $separation->effective_date->format('M d, Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Status</span>
                        @php $colors = ['pending' => 'text-amber-600', 'in_progress' => 'text-blue-600', 'cleared' => 'text-purple-600', 'completed' => 'text-emerald-700']; @endphp
                        <span class="font-medium {{ $colors[$separation->status] }} capitalize">{{ str_replace('_', ' ', $separation->status) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">Final Dues</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-400">Gratuity</span><span>KES {{ number_format($separation->gratuity, 0) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Pending Claims</span><span>KES {{ number_format($separation->pending_claims, 0) }}</span></div>
                    <div class="flex justify-between text-red-600"><span>Loan Balance</span><span>- KES {{ number_format($separation->loan_balance, 0) }}</span></div>
                    <div class="flex justify-between font-bold text-gray-800 border-t border-gray-100 pt-2"><span>Total Final Dues</span><span>KES {{ number_format($separation->final_dues, 0) }}</span></div>
                </div>
            </div>

            @if($separation->certificate_issued)
            <div class="bg-emerald-50 rounded-xl p-4 text-center">
                <p class="text-xs text-emerald-700 font-medium">Certificate of Service Issued</p>
                <p class="text-xs text-emerald-600">{{ $separation->certificate_date?->format('M d, Y') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
