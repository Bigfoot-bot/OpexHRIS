@extends(auth()->check() && auth()->user()->portal_preference === 'employee' ? 'tenant.employee.layouts.app' : 'tenant.layouts.app')

@section('page-title', 'My Wallet')
@section('page-subtitle', 'Manage your facility wallet and top-up requests')

@section('page-actions')
    <a href="{{ route('tenant.wallet.top-up') }}"
       class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
        + Top Up Wallet
    </a>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg px-4 py-3">{{ session('error') }}</div>
    @endif

    {{-- Balance Card --}}
    <div class="bg-emerald-700 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-70 uppercase tracking-widest">Wallet Balance</p>
                <p class="text-4xl font-bold mt-1">KES {{ number_format($wallet->balance, 2) }}</p>
                <p class="text-xs opacity-50 mt-2">{{ tenant('name') }} &middot; {{ now()->format('M d, Y') }}</p>
            </div>
            <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>
        @if($wallet->balance < 10000)
            <div class="mt-4 bg-white/10 rounded-lg px-4 py-2 text-xs">
                ?? Low balance — top up your wallet to run payroll
            </div>
        @endif
    </div>

    {{-- Recent Requests --}}
    @if($requests->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-800">Recent Top-Up Requests</h2>
        </div>
        <div class="space-y-3">
            @foreach($requests as $req)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div>
                    <p class="text-sm font-medium text-gray-800">KES {{ number_format($req->amount, 2) }}</p>
                    <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $req->payment_method)) }} &middot; {{ $req->created_at->format('M d, Y') }}</p>
                </div>
                @php $colors = ['pending' => 'bg-amber-50 text-amber-600', 'approved' => 'bg-emerald-50 text-emerald-700', 'rejected' => 'bg-red-50 text-red-600']; @endphp
                <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$req->status] ?? '' }} capitalize">{{ $req->status }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Transaction History --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Transaction History</h2>
        @if($transactions->isEmpty())
            <p class="text-center text-gray-400 text-sm py-8">No transactions yet.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Description</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Type</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Amount</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Balance After</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-4 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($transactions as $tx)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $tx->description }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $tx->type === 'credit' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }} capitalize">
                                {{ $tx->type }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm font-medium {{ $tx->type === 'credit' ? 'text-emerald-700' : 'text-red-600' }}">
                            {{ $tx->type === 'credit' ? '+' : '-' }} KES {{ number_format($tx->amount, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">KES {{ number_format($tx->balance_after, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $transactions->links() }}</div>
        @endif
    </div>

</div>
@endsection
