@extends('tenant.layouts.app')
@section('page-title', 'Report Results')
@section('page-subtitle', ucfirst(str_replace('_', ' ', $type)) . ' Report')
@section('page-actions')
    <a href="{{ route('tenant.report-builder.index') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">Back</a>
    <form method="POST" action="{{ route('tenant.report-builder.generate') }}" class="inline">
        @csrf
        <input type="hidden" name="report_type" value="{{ $type }}"/>
        <input type="hidden" name="date_from" value="{{ $request->date_from }}"/>
        <input type="hidden" name="date_to" value="{{ $request->date_to }}"/>
        <input type="hidden" name="format" value="csv"/>
        <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">Download CSV</button>
    </form>
@endsection
@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
        <p class="text-sm font-semibold text-gray-800">{{ count($data) }} records found</p>
    </div>
    @if(empty($data))
        <div class="p-12 text-center"><p class="text-gray-400 text-sm">No data found for the selected criteria.</p></div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        @foreach($headers as $header)
                            <th class="text-left text-xs text-gray-400 font-medium px-4 py-3 whitespace-nowrap">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($data as $row)
                    <tr class="hover:bg-gray-50/50">
                        @foreach($row as $value)
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $value }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
