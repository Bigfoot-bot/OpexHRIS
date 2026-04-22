@extends(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0 ? 'tenant.layouts.app' : 'tenant.employee.layouts.app')
@section('page-title', 'Document Categories')
@section('page-subtitle', 'Manage document categories')
@section('page-actions')
    <a href="{{ route('tenant.documents.index') }}" class="bg-white border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg">
        Back to Documents
    </a>
@endsection
@section('content')
<div class="grid grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Add Category</h2>
        <form method="POST" action="{{ route('tenant.documents.categories.store') }}" class="space-y-4">
            @csrf
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
            @endif
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Category Name *</label>
                <input type="text" name="name" required placeholder="e.g. HR Policies"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
                <input type="text" name="description" placeholder="Brief description"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Color</label>
                <input type="color" name="color" value="#064e3b" class="w-full h-10 rounded-lg border border-gray-200"/>
            </div>
            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-5 py-2 rounded-lg">
                Add Category
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Existing Categories</h2>
        @if($categories->isEmpty())
            <p class="text-sm text-gray-400">No categories yet.</p>
        @else
            <div class="space-y-3">
                @foreach($categories as $cat)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $cat->color }}"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $cat->name }}</p>
                            <p class="text-xs text-gray-400">{{ $cat->documents_count }} documents</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('tenant.documents.categories.destroy', $cat) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700" onclick="return confirm('Delete category?')">Delete</button>
                    </form>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection


