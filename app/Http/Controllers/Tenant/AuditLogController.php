<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::where('tenant_id', tenant('id'))
                    ->latest();

        // Filter by module
        if ($request->module) {
            $query->where('module', $request->module);
        }

        // Filter by action
        if ($request->action) {
            $query->where('action', $request->action);
        }

        // Filter by date
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        // Search
        if ($request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(20);

        $modules = AuditLog::where('tenant_id', tenant('id'))
                    ->distinct()->pluck('module');

        $actions = AuditLog::where('tenant_id', tenant('id'))
                    ->distinct()->pluck('action');

        return view('tenant.audit.index', compact('logs', 'modules', 'actions'));
    }
}