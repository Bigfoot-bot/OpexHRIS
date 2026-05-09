<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetAssignment;
use App\Models\Tenant\Employee;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $isEmployee = auth()->user()->isInEmployeePortal()
            || (!auth()->user()->is_admin && auth()->user()->tenantRoles()->count() === 0);
        $query = Asset::where('tenant_id', tenant('id'))->with(['currentAssignment', 'assetCategory']);

        if ($isEmployee) {
            $employee = auth()->user()->employee;
            if ($employee) {
                $assignedAssetIds = AssetAssignment::where('employee_id', $employee->id)->where('status', 'active')->pluck('asset_id');
                $query->whereIn('id', $assignedAssetIds);
            } else {
                $query->whereRaw('1=0');
            }
        }

        if ($request->status && !$isEmployee) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('asset_code', 'like', '%' . $request->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $request->search . '%')
                  ->orWhere('number_plate', 'like', '%' . $request->search . '%');
            });
        }

        $assets = $query->latest()->paginate(15);
        return view('tenant.assets.index', compact('assets'));
    }

    public function categories()
    {
        $categories = AssetCategory::where('tenant_id', tenant('id'))->withCount('assets')->get();
        return view('tenant.assets.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:100']]);
        AssetCategory::create([
            'tenant_id'        => tenant('id'),
            'name'             => $request->name,
            'description'      => $request->description,
            'color'            => $request->color ?? '#064e3b',
            'has_number_plate' => $request->boolean('has_number_plate'),
        ]);
        return back()->with('success', 'Category created successfully!');
    }

    public function destroyCategory(AssetCategory $category)
    {
        if ($category->tenant_id !== tenant('id')) abort(403);
        $category->delete();
        return back()->with('success', 'Category deleted successfully!');
    }

    public function create()
    {
        $categories = AssetCategory::where('tenant_id', tenant('id'))->get();
        return view('tenant.assets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'purchase_date'  => ['nullable', 'date'],
        ]);

        $assetCode = 'AST-' . strtoupper(substr(tenant('id'), 0, 4)) . '-' . str_pad(Asset::where('tenant_id', tenant('id'))->count() + 1, 4, '0', STR_PAD_LEFT);

        Asset::create([
            'tenant_id'         => tenant('id'),
            'asset_category_id' => $request->asset_category_id,
            'asset_code'        => $assetCode,
            'name'              => $request->name,
            'category'          => $request->category,
            'description'       => $request->description,
            'serial_number'     => $request->serial_number,
            'number_plate'      => $request->number_plate,
            'brand'             => $request->brand,
            'model'             => $request->model,
            'purchase_price'    => $request->purchase_price,
            'purchase_date'     => $request->purchase_date,
            'current_value'     => $request->current_value,
            'status'            => 'available',
            'location'          => $request->location,
            'notes'             => $request->notes,
        ]);

        return redirect()->route('tenant.assets.index')->with('success', 'Asset added successfully!');
    }

    public function show(Asset $asset)
    {
        if ($asset->tenant_id !== tenant('id')) abort(403);
        $assignments = AssetAssignment::where('asset_id', $asset->id)->with('employee')->latest()->get();
        $employees   = Employee::where('tenant_id', tenant('id'))->get();
        return view('tenant.assets.show', compact('asset', 'assignments', 'employees'));
    }

    public function edit(Asset $asset)
    {
        if ($asset->tenant_id !== tenant('id')) abort(403);
        $categories = AssetCategory::where('tenant_id', tenant('id'))->get();
        return view('tenant.assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, Asset $asset)
    {
        if ($asset->tenant_id !== tenant('id')) abort(403);
        $request->validate(['name' => ['required', 'string', 'max:255']]);
        $asset->update($request->only([
            'name', 'asset_category_id', 'category', 'description', 'serial_number',
            'number_plate', 'brand', 'model', 'purchase_price', 'purchase_date',
            'current_value', 'status', 'location', 'notes',
        ]));
        return redirect()->route('tenant.assets.show', $asset)->with('success', 'Asset updated successfully!');
    }

    public function assign(Request $request, Asset $asset)
    {
        if ($asset->tenant_id !== tenant('id')) abort(403);
        $request->validate([
            'employee_id'   => ['required', 'exists:employees,id'],
            'assigned_date' => ['required', 'date'],
        ]);

        AssetAssignment::where('asset_id', $asset->id)->where('status', 'active')
            ->update(['status' => 'returned', 'return_date' => now()]);

        AssetAssignment::create([
            'tenant_id'     => tenant('id'),
            'asset_id'      => $asset->id,
            'employee_id'   => $request->employee_id,
            'assigned_date' => $request->assigned_date,
            'status'        => 'active',
            'notes'         => $request->notes,
            'assigned_by'   => auth()->id(),
        ]);

        $asset->update(['status' => 'assigned']);
        return back()->with('success', 'Asset assigned successfully!');
    }

    public function returnAsset(Asset $asset)
    {
        if ($asset->tenant_id !== tenant('id')) abort(403);
        AssetAssignment::where('asset_id', $asset->id)->where('status', 'active')
            ->update(['status' => 'returned', 'return_date' => now()]);
        $asset->update(['status' => 'available']);
        return back()->with('success', 'Asset returned successfully!');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->tenant_id !== tenant('id')) abort(403);
        $asset->delete();
        return redirect()->route('tenant.assets.index')->with('success', 'Asset deleted successfully!');
    }
}
