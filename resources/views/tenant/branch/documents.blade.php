@extends('tenant.branch.layout')
@section('page-title', 'Branch Documents')
@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($documents->isEmpty())
        <div class="p-12 text-center"><p class="text-gray-400 text-sm">No documents found.</p></div>
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
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $doc->file_size_formatted }}</td>
                    <td class="px-6 py-4 text-sm text-gray-400">{{ $doc->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('tenant.documents.download', $doc) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Download</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $documents->links() }}</div>
    @endif
</div>
@endsection
