<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\Central\SuperAdmin;
use App\Mail\SupportTicketOpened;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('tenant_id', tenant('id'))
                        ->latest()->paginate(10);
        return view('tenant.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('tenant.support.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'  => ['required', 'string', 'max:255'],
            'message'  => ['required', 'string'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'category' => ['required', 'in:general,billing,technical,feature_request'],
        ]);

        $ticket = SupportTicket::create([
            'tenant_id' => tenant('id'),
            'subject'   => $validated['subject'],
            'message'   => $validated['message'],
            'priority'  => $validated['priority'],
            'category'  => $validated['category'],
            'status'    => 'open',
        ]);

        // Notify Super Admin
        try {
            $superAdmin = SuperAdmin::first();
            if ($superAdmin) {
                Mail::to($superAdmin->email)->send(new SupportTicketOpened($ticket));
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return redirect()->route('tenant.support.index')
                         ->with('success', 'Support ticket submitted successfully! We will get back to you shortly.');
    }

    public function show(SupportTicket $ticket)
    {
        if ($ticket->tenant_id !== tenant('id')) {
            abort(403);
        }
        return view('tenant.support.show', compact('ticket'));
    }
}
