@extends('tenant.layouts.app')
@section('page-title', 'Shifts & Roster')
@section('page-subtitle', 'Manage shifts and staff scheduling')
@section('page-actions')
    <a href="{{ route('tenant.shifts.roster.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">+ New Roster</a>
@endsection
@section('content')
<div class="space-y-6">
    {{-- Shift Types --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Shift Types</h2>
        <div class="grid grid-cols-4 gap-4 mb-6">
            @forelse($shifts as $shift)
            <div class="border border-gray-100 rounded-xl p-4 relative" style="border-left: 4px solid {{ $shift->color }}">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $shift->name }}</p>
                        <p class="text-xs text-gray-400">{{ $shift->start_time }} - {{ $shift->end_time }}</p>
                        <p class="text-xs text-gray-400">{{ $shift->duration_hours }}hrs{{ $shift->is_night_shift ? ' - Night Shift' : '' }}</p>
                    </div>
                    <form method="POST" action="{{ route('tenant.shifts.destroy', $shift) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">?</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 col-span-4">No shift types defined yet.</p>
            @endforelse
        </div>

        {{-- Add Shift Form --}}
        <form method="POST" action="{{ route('tenant.shifts.store') }}" class="border-t border-gray-100 pt-4">
            @csrf
            @if(session('success'))<div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-2 mb-3">{{ session('success') }}</div>@endif
            <p class="text-xs font-medium text-gray-600 mb-3">Add New Shift Type</p>
            <div class="grid grid-cols-6 gap-3">
                <input type="text" name="name" placeholder="Shift Name *" required class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                <input type="text" name="code" placeholder="Code (e.g. M)" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                <input type="time" name="start_time" required class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                <input type="time" name="end_time" required class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                <input type="color" name="color" value="#064e3b" class="h-10 w-full rounded-lg border border-gray-200 cursor-pointer"/>
                <button type="submit" class="bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">Add Shift</button>
            </div>
            <div class="flex items-center gap-4 mt-3">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="is_night_shift" class="rounded"/> Night Shift
                </label>
                <input type="number" name="night_shift_allowance" placeholder="Night Allowance (KES)" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 w-48"/>
                <input type="number" name="break_duration_minutes" placeholder="Break (minutes)" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 w-40"/>
            </div>
        </form>
    </div>

    {{-- Rosters --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-sm font-semibold text-gray-800">Rosters</h2>
        </div>
        @if($rosters->isEmpty())
            <div class="p-12 text-center"><p class="text-gray-400 text-sm">No rosters created yet.</p></div>
        @else
            <table class="w-full">
                <thead><tr class="border-b border-gray-50">
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Roster</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Department</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Period</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Status</th>
                    <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($rosters as $roster)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $roster->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $roster->department ?? 'All' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $roster->start_date->format('M d') }} - {{ $roster->end_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            @php $colors = ['draft' => 'bg-gray-100 text-gray-600', 'published' => 'bg-emerald-50 text-emerald-700', 'archived' => 'bg-amber-50 text-amber-600']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $colors[$roster->status] }} capitalize">{{ $roster->status }}</span>
                        </td>
                        <td class="px-6 py-4 flex gap-2">
                            <a href="{{ route('tenant.shifts.roster', $roster) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">View</a>
                            <form method="POST" action="{{ route('tenant.shifts.roster.destroy', $roster) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $rosters->links() }}</div>
        @endif
    </div>
</div>
@endsection


