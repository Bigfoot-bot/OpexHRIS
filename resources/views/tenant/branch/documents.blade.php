@extends('tenant.branch.layout')
@section('page-title', 'Documents')
@section('page-subtitle', 'Upload and manage branch documents')
@section('content')

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 border border-red-100 text-red-600 text-sm rounded-xl px-4 py-3 mb-6">{{ session('error') }}</div>
@endif

<div class="grid grid-cols-3 gap-6">
    {{-- Upload Form --}}
    <div class="col-span-1">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Upload Document</h2>
            @if($errors->any())
                <div class="bg-red-50 border border-red-100 text-red-600 text-xs rounded-lg px-3 py-2 mb-3">
                    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('tenant.branch.documents.store', $branch) }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Title *</label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Document title"
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Optional description"
                              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">File * <span class="text-gray-400">(max 10MB)</span></label>
                    <input type="file" name="file" required
                           class="w-full text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"/>
                </div>
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium py-2 rounded-lg">Upload</button>
            </form>
        </div>
    </div>

    {{-- Documents List --}}
    <div class="col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if($documents->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-gray-400 text-sm">No documents uploaded yet.</p>
                </div>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-50">
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Document</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Size</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Uploaded</th>
                            <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($documents as $doc)
                        @php $ext = strtoupper($doc->file_type ?? 'FILE'); $extColors = ['PDF' => 'bg-red-100 text-red-700', 'DOC' => 'bg-blue-100 text-blue-700', 'DOCX' => 'bg-blue-100 text-blue-700']; $extColor = $extColors[$ext] ?? 'bg-gray-100 text-gray-600'; @endphp
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg {{ $extColor }} flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold">{{ $ext }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $doc->title }}</p>
                                        <p class="text-xs text-gray-400">{{ $doc->file_name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $doc->file_size_formatted ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-400">{{ $doc->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('tenant.branch.documents.download', [$branch, $doc]) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Download</a>
                                    @if($doc->uploaded_by === auth()->id() || auth()->user()->is_admin)
                                    <form method="POST" action="{{ route('tenant.branch.documents.destroy', [$branch, $doc]) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this document?')" class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-6 py-4">{{ $documents->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
