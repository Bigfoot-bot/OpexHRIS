@extends(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0 ? 'tenant.layouts.app' : 'tenant.employee.layouts.app')
@section('page-title', $document->title)
@section('page-subtitle', 'Document details and acknowledgments')
@section('page-actions')
    <a href="{{ route('tenant.documents.download', $document) }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
        Download
    </a>
@endsection
@section('content')
<div class="grid grid-cols-3 gap-6">
    <div class="col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Document Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400">Category</p>
                    <p class="text-sm font-medium text-gray-800 mt-1">{{ $document->category->name ?? 'Uncategorized' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">File Type</p>
                    <p class="text-sm font-medium text-gray-800 mt-1">{{ strtoupper($document->file_type) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">File Size</p>
                    <p class="text-sm font-medium text-gray-800 mt-1">{{ $document->file_size_formatted }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Visibility</p>
                    <p class="text-sm font-medium text-gray-800 mt-1 capitalize">{{ str_replace('_', ' ', $document->visibility) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Uploaded</p>
                    <p class="text-sm font-medium text-gray-800 mt-1">{{ $document->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Requires Acknowledgment</p>
                    <p class="text-sm font-medium text-gray-800 mt-1">{{ $document->requires_acknowledgment ? 'Yes' : 'No' }}</p>
                </div>
            </div>
            @if($document->description)
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-400 mb-1">Description</p>
                    <p class="text-sm text-gray-700">{{ $document->description }}</p>
                </div>
            @endif

            @if($document->requires_acknowledgment)
                @php $employee = auth()->user()->employee; @endphp
                @if($employee && !$document->isAcknowledgedBy($employee->id))
                    <form method="POST" action="{{ route('tenant.documents.acknowledge', $document) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
                            I Acknowledge This Document
                        </button>
                    </form>
                @elseif($employee)
                    <div class="mt-4 bg-emerald-50 border border-emerald-100 rounded-lg px-4 py-2 text-emerald-700 text-sm">
                        ? You have acknowledged this document
                    </div>
                @endif
            @endif
        </div>
    </div>

    <div class="space-y-6">
        @if($document->requires_acknowledgment)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Acknowledgments ({{ $acknowledgments->count() }})</h2>
            @if($acknowledgments->isEmpty())
                <p class="text-xs text-gray-400">No acknowledgments yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($acknowledgments as $ack)
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center">
                            <span class="text-xs font-medium text-emerald-700">{{ substr($ack->employee->first_name ?? 'U', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-800">{{ $ack->employee->first_name ?? 'Unknown' }} {{ $ack->employee->last_name ?? '' }}</p>
                            <p class="text-xs text-gray-400">{{ $ack->acknowledged_at?->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection


