<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Tenant\User;
use App\Mail\AnnouncementAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('tenant_id', tenant('id'))
                            ->where('type', 'facility')
                            ->latest()->paginate(15);

        // Also include global announcements from Super Admin
        $globalAnnouncements = Announcement::where('type', 'global')->latest()->get();

        return view('tenant.announcements.index', compact('announcements', 'globalAnnouncements'));
    }

    public function create()
    {
        return view('tenant.announcements.create');
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
            'type'         => 'facility',
            'sender_type'  => 'tenant',
            'tenant_id'    => tenant('id'),
            'send_email'   => $request->boolean('send_email'),
        ]);

        // Send email to all employees
        if ($announcement->send_email) {
            $users = User::where('tenant_id', tenant('id'))
                         ->where('status', 'active')
                         ->get();
            foreach ($users as $user) {
                try {
                    $link = 'http://' . request()->getHost() . '/announcements';
                    Mail::to($user->email)->send(new AnnouncementAlert(
                        $announcement,
                        $user->name,
                        tenant('name') ?? 'Facility Admin',
                        $link
                    ));
                } catch (\Exception $e) {
                    // Silently fail
                }
            }
        }

        return redirect()->route('tenant.announcements.index')
                         ->with('success', 'Announcement published and employees notified!');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->tenant_id !== tenant('id')) {
            abort(403);
        }
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}

