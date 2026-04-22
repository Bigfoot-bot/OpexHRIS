<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\Central\Tenant;
use App\Models\Central\SuperAdmin;
use App\Mail\SupportTicketReplied;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with('tenant')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
        }

        $tickets = $query->paginate(20);
        $stats = [
            'open'        => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved'    => SupportTicket::where('status', 'resolved')->count(),
            'urgent'      => SupportTicket::where('priority', 'urgent')->count(),
        ];

        return view('central.support.index', compact('tickets', 'stats'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load('tenant');
        return view('central.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'admin_reply' => ['required', 'string'],
        ]);

        $ticket->update([
            'admin_reply' => $request->admin_reply,
            'replied_at'  => now(),
            'status'      => 'in_progress',
        ]);

        // Notify tenant admin about the reply
        try {
            $tenant = $ticket->tenant;
            if ($tenant && $tenant->email) {
                $domain = $tenant->domains()->first()?->domain;
                $link = 'http://' . $domain . '/support/' . $ticket->id;
                Mail::to($tenant->email)->send(new SupportTicketReplied($ticket, $link));
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return back()->with('success', 'Reply sent successfully!');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
        ]);

        $ticket->update(['status' => $request->status]);

        return back()->with('success', 'Ticket status updated!');
    }
}
