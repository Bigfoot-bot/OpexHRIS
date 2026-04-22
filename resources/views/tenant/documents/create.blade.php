@extends(auth()->user()->is_admin || auth()->user()->tenantRoles()->count() > 0 ? 'tenant.layouts.app' : 'tenant.employee.layouts.app')
@section('page-title', 'Upload Document')
@section('page-subtitle', 'Upload a new document to the system')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('tenant.documents.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Title *</label>
                <input type="text" name="title" required value="{{ old('title') }}"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Category</label>
                <select name="category_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">File * (Max 10MB)</label>
                <input type="file" name="file" required class="w-full text-sm"/>
                @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Visibility *</label>
                <select name="visibility" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="all">All Employees</option>
                    <option value="hr_only">HR Only</option>
                </select>
            </div>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="requires_acknowledgment" value="1" {{ old('requires_acknowledgment') ? 'checked' : '' }} class="rounded"/>
                    Requires Acknowledgment
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="is_template" value="1" {{ old('is_template') ? 'checked' : '' }} class="rounded"/>
                    This is a Template
                </label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2 rounded-lg">Upload Document</button>
                <a href="{{ route('tenant.documents.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-6 py-2 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection


