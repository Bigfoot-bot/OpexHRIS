<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Central\Invoice;
use App\Models\Central\Tenant;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('tenant')->latest()->paginate(20);

        $stats = [
            'total'         => Invoice::count(),
            'paid'          => Invoice::where('status', 'paid')->count(),
            'pending'       => Invoice::whereIn('status', ['sent', 'draft'])->count(),
            'overdue'       => Invoice::where('status', 'overdue')->count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('total'),
            'pending_value' => Invoice::whereIn('status', ['sent', 'draft'])->sum('total'),
        ];

        return view('central.invoices.index', compact('invoices', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
        ]);

        $tenant  = Tenant::find($request->tenant_id);
        $invoice = Invoice::generateForTenant($tenant);

        return redirect()->route('admin.invoices.index')
                         ->with('success', "Invoice {$invoice->invoice_number} generated for {$tenant->name}!");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('tenant');
        return view('central.invoices.show', compact('invoice'));
    }

    public function markPaid(Request $request, Invoice $invoice)
    {
        $request->validate([
            'payment_method'    => ['required', 'string'],
            'payment_reference' => ['nullable', 'string'],
        ]);

        $invoice->update([
            'status'             => 'paid',
            'paid_date'          => now(),
            'payment_method'     => $request->payment_method,
            'payment_reference'  => $request->payment_reference,
        ]);

        // Extend subscription by 1 month
        $tenant = $invoice->tenant;
        $tenant->update([
            'subscription_ends_at' => now()->addMonth(),
        ]);

        return back()->with('success', 'Invoice marked as paid and subscription extended!');
    }

    public function markOverdue()
    {
        $count = Invoice::where('status', 'sent')
                    ->where('due_date', '<', now())
                    ->update(['status' => 'overdue']);

        return back()->with('success', "{$count} invoice(s) marked as overdue.");
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.invoices.index')
                         ->with('success', 'Invoice deleted.');
    }
}