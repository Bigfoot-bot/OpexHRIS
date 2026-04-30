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
        $announcements = Announcement::whereIn('type', ['global', 'targeted'])
            ->where(function ($q) {
                $q->where('type', 'global')
                  ->orWhere(function ($q2) {
                      // For targeted, only show one row per title+created_at group
                      // (we show the representative row — the first one per batch)
                      $q2->where('type', 'targeted')->whereNotNull('tenant_id');
                  });
            })
            ->latest()
            ->paginate(15);

        return view('central.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $tenants = Tenant::where('is_active', true)->orderBy('name')->get();
        return view('central.announcements.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'body'         => ['required', 'string'],
            'send_email'   => ['nullable', 'boolean'],
            'audience'     => ['required', 'in:all,specific'],
            'tenant_ids'   => ['required_if:audience,specific', 'array', 'min:1'],
            'tenant_ids.*' => ['exists:tenants,id'],
        ]);

        if ($request->audience === 'all') {
            // Single global announcement
            $announcement = Announcement::create([
                'title'        => $request->title,
                'body'         => $request->body,
                'meeting_link' => $request->meeting_link,
                'type'         => 'global',
                'sender_type'  => 'super_admin',
                'tenant_id'    => null,
                'send_email'   => $request->boolean('send_email'),
            ]);

            if ($announcement->send_email) {
                $tenants = Tenant::where('is_active', true)->get();
                foreach ($tenants as $tenant) {
                    try {
                        $link = 'http://' . ($tenant->domains()->first()?->domain) . '/announcements';
                        Mail::to($tenant->email)->send(new AnnouncementAlert(
                            $announcement, $tenant->name, 'OpEx HRIS', $link
                        ));
                    } catch (\Exception $e) {}
                }
            }

            return redirect()->route('admin.announcements.index')
                             ->with('success', 'Announcement sent to all facilities.');
        }

        // Targeted — create one record per selected tenant
        $tenants = Tenant::whereIn('id', $request->tenant_ids)->get();
        $sendEmail = $request->boolean('send_email');
        $first = null;

        foreach ($tenants as $tenant) {
            $announcement = Announcement::create([
                'title'        => $request->title,
                'body'         => $request->body,
                'meeting_link' => $request->meeting_link,
                'type'         => 'targeted',
                'sender_type'  => 'super_admin',
                'tenant_id'    => $tenant->id,
                'send_email'   => $sendEmail,
            ]);
            $first = $first ?? $announcement;

            if ($sendEmail) {
                try {
                    $link = 'http://' . ($tenant->domains()->first()?->domain) . '/announcements';
                    Mail::to($tenant->email)->send(new AnnouncementAlert(
                        $announcement, $tenant->name, 'OpEx HRIS', $link
                    ));
                } catch (\Exception $e) {}
            }
        }

        $count = $tenants->count();
        return redirect()->route('admin.announcements.index')
                         ->with('success', "Announcement sent to {$count} " . ($count === 1 ? 'facility' : 'facilities') . '.');
    }

    public function destroy(Announcement $announcement)
    {
        // If targeted, delete all rows with the same title + body + created minute
        if ($announcement->type === 'targeted') {
            Announcement::where('type', 'targeted')
                ->where('title', $announcement->title)
                ->where('body', $announcement->body)
                ->where('sender_type', 'super_admin')
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') = DATE_FORMAT(?, '%Y-%m-%d %H:%i')", [$announcement->created_at])
                ->delete();
        } else {
            $announcement->delete();
        }

        return back()->with('success', 'Announcement deleted.');
    }
}
