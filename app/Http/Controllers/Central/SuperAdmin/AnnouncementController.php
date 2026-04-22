<?php

namespace App\Http\Controllers\Central\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Central\Tenant;
use App\Mail\AnnouncementAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('type', 'global')->latest()->paginate(15);
        return view('central.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('central.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'body'       => ['required', 'string'],
            'send_email' => ['boolean'],
        ]);

        $announcement = Announcement::create([
            'title'        => $request->title,
            'body'         => $request->body,
            'meeting_link' => $request->meeting_link,
            'type'         => 'global',
            'sender_type'  => 'super_admin',
            'tenant_id'    => null,
            'send_email'   => $request->boolean('send_email'),
        ]);

        // Send email to all tenant admins
        if ($announcement->send_email) {
            $tenants = Tenant::where('is_active', true)->get();
            foreach ($tenants as $tenant) {
                try {
                    $link = 'http://' . ($tenant->domains()->first()?->domain) . '/announcements';
                    Mail::to($tenant->email)->send(new AnnouncementAlert(
                        $announcement,
                        $tenant->name,
                        'OpEx HRIS',
                        $link
                    ));
                } catch (\Exception $e) {
                    // Silently fail
                }
            }
        }

        return redirect()->route('admin.announcements.index')
                         ->with('success', 'Announcement published and tenants notified!');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}

