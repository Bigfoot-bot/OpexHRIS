<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentAcknowledgment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $isEmployee = auth()->user()->isInEmployeePortal()
            || (!auth()->user()->is_admin && auth()->user()->tenantRoles()->count() === 0);
        $categories = DocumentCategory::where('tenant_id', tenant('id'))->get();
        $query      = Document::where('tenant_id', tenant('id'))->with('category');

        if ($isEmployee) {
            $query->where('visibility', 'all');
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->type === 'templates') {
            $query->where('is_template', true);
        }

        $documents = $query->latest()->paginate(15);
        $canManage = !$isEmployee;
        return view('tenant.documents.index', compact('documents', 'categories', 'canManage'));
    }

    public function create()
    {
        $categories = DocumentCategory::where('tenant_id', tenant('id'))->get();
        return view('tenant.documents.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:document_categories,id'],
            'file'        => ['required', 'file', 'max:10240'],
            'visibility'  => ['required', 'in:all,hr_only,specific'],
        ]);

        $file         = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension    = $file->getClientOriginalExtension();
        $fileSize     = $file->getSize();
        $filename     = time() . '_' . $originalName;
        $storagePath  = 'documents/' . tenant('id') . '/' . $filename;
        Storage::disk('local')->putFileAs('documents/' . tenant('id'), $file, $filename);

        Document::create([
            'tenant_id'               => tenant('id'),
            'category_id'             => $request->category_id,
            'title'                   => $request->title,
            'description'             => $request->description,
            'file_path'               => $storagePath,
            'file_name'               => $originalName,
            'file_type'               => $extension,
            'file_size'               => $fileSize,
            'visibility'              => $request->visibility,
            'requires_acknowledgment' => $request->boolean('requires_acknowledgment'),
            'is_template'             => $request->boolean('is_template'),
            'uploaded_by'             => auth()->id(),
        ]);

        return redirect()->route('tenant.documents.index')->with('success', 'Document uploaded successfully!');
    }

    public function show(Document $document)
    {
        if ($document->tenant_id !== tenant('id')) abort(403);
        $acknowledgments = DocumentAcknowledgment::where('document_id', $document->id)->with('employee')->get();
        return view('tenant.documents.show', compact('document', 'acknowledgments'));
    }

    public function download(Document $document)
    {
        if ($document->tenant_id !== tenant('id')) abort(403);

        // Support both old public path and new storage path
        if (Storage::disk('local')->exists($document->file_path)) {
            return Storage::disk('local')->download($document->file_path, $document->file_name);
        }

        // Fallback: old public path (for files uploaded before migration)
        $publicPath = public_path($document->file_path);
        if (file_exists($publicPath)) {
            return response()->download($publicPath, $document->file_name);
        }

        abort(404);
    }

    public function destroy(Document $document)
    {
        if ($document->tenant_id !== tenant('id')) abort(403);

        // Try storage disk first, then public fallback
        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        } else {
            $publicPath = public_path($document->file_path);
            if (file_exists($publicPath)) unlink($publicPath);
        }

        $document->delete();
        return back()->with('success', 'Document deleted successfully!');
    }

    public function acknowledge(Document $document)
    {
        if ($document->tenant_id !== tenant('id')) abort(403);
        $employee = auth()->user()->employee;
        if (!$employee) return back()->with('error', 'No employee record found.');

        DocumentAcknowledgment::updateOrCreate(
            ['document_id' => $document->id, 'employee_id' => $employee->id],
            ['tenant_id' => tenant('id'), 'acknowledged_at' => now(), 'ip_address' => request()->ip()]
        );

        return back()->with('success', 'Document acknowledged successfully!');
    }

    public function categories()
    {
        $categories = DocumentCategory::where('tenant_id', tenant('id'))->withCount('documents')->get();
        return view('tenant.documents.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:100']]);

        DocumentCategory::create([
            'tenant_id'   => tenant('id'),
            'name'        => $request->name,
            'description' => $request->description,
            'color'       => $request->color ?? '#064e3b',
        ]);

        return back()->with('success', 'Category created successfully!');
    }

    public function destroyCategory(DocumentCategory $category)
    {
        if ($category->tenant_id !== tenant('id')) abort(403);
        $category->delete();
        return back()->with('success', 'Category deleted successfully!');
    }
}
