@extends(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0 ? 'tenant.layouts.app' : 'tenant.employee.layouts.app')
@section('page-title', 'Documents')
@section('page-subtitle', 'Manage company documents and files')
@section('page-actions')
@if(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0)
    <div class="flex gap-2">
        <a href="{{ route('tenant.documents.categories') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50">Categories</a>
        <a href="{{ route('tenant.documents.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-4 py-2 rounded-lg">+ Upload Document</a>
    </div>
@endif
@endsection
@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search documents..."
                   class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            <select name="category_id" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @if(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0)
            <select name="type" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">All Documents</option>
                <option value="templates" {{ request('type') == 'templates' ? 'selected' : '' }}>Templates Only</option>
            </select>
            @endif
            <button type="submit" class="bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
        </form>
    </div>

    @if($documents->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-gray-400 text-sm">No documents found.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Document</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Category</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Size</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Visibility</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Uploaded</th>
                        <th class="text-left text-xs text-gray-400 font-medium px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($documents as $doc)
                    @php
                        $ext = strtoupper($doc->file_type ?? 'FILE');
                        $extColors = ['PDF' => 'bg-red-100 text-red-700', 'DOC' => 'bg-blue-100 text-blue-700', 'DOCX' => 'bg-blue-100 text-blue-700', 'XLS' => 'bg-green-100 text-green-700', 'XLSX' => 'bg-green-100 text-green-700', 'PNG' => 'bg-purple-100 text-purple-700', 'JPG' => 'bg-purple-100 text-purple-700', 'JPEG' => 'bg-purple-100 text-purple-700'];
                        $extColor = $extColors[$ext] ?? 'bg-gray-100 text-gray-600';
                    @endphp
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
                                @if($doc->is_template)
                                    <span class="text-xs bg-purple-50 text-purple-600 px-2 py-0.5 rounded-full">Template</span>
                                @endif
                                @if($doc->requires_acknowledgment)
                                    <span class="text-xs bg-amber-50 text-amber-600 px-2 py-0.5 rounded-full">Req. ACK</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($doc->category)
                                <span class="text-xs px-2.5 py-1 rounded-full text-white" style="background-color: {{ $doc->category->color }}">{{ $doc->category->name }}</span>
                            @else
                                <span class="text-xs text-gray-400">Uncategorized</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $doc->file_size_formatted }}</td>
                        <td class="px-6 py-4">
                            @php $vColors = ['all' => 'bg-emerald-50 text-emerald-700', 'hr_only' => 'bg-blue-50 text-blue-600', 'specific' => 'bg-amber-50 text-amber-600']; @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $vColors[$doc->visibility] ?? '' }} capitalize">{{ str_replace('_', ' ', $doc->visibility) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $doc->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('tenant.documents.show', $doc) }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">View</a>
                                <a href="{{ route('tenant.documents.download', $doc) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Download</a>
                                @if(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0)
                                <form method="POST" action="{{ route('tenant.documents.destroy', $doc) }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium" onclick="return confirm('Delete this document?')">Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">{{ $documents->links() }}</div>
        </div>
    @endif
</div>
@endsection


