<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Branch;
use App\Models\Tenant\Employee;
use App\Models\Tenant\Notification as TenantNotification;
use App\Models\Tenant\User;
use App\Mail\AnnouncementAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AnnouncementController extends Controller
{
    public function index()
    {
        $tenantId = tenant('id');
        $user     = auth()->user();

        // Build query for facility announcements visible to this user
        $query = Announcement::where('tenant_id', $tenantId)
                    ->where('type', 'facility');

        // If employee portal, only show announcements addressed to everyone or specifically to this employee
        if ($user->portal_preference === 'employee' && $user->employee_id) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('employee_id')
                  ->orWhere('employee_id', $user->employee_id);
            });
        } else {
            // HR/admin: show facility-wide + one representative row per employee-targeted batch
            $query->where(function ($q) use ($tenantId) {
                $q->whereNull('employee_id')
                  ->orWhereIn('id', function ($sub) use ($tenantId) {
                      $sub->selectRaw('MIN(id)')
                          ->from('announcements')
                          ->where('tenant_id', $tenantId)
                          ->where('type', 'facility')
                          ->whereNotNull('employee_id')
                          ->groupByRaw("title, body, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i')");
                  });
            });
        }

        $announcements = $query->latest()->paginate(15);

        // Also include global announcements from Super Admin
        $globalAnnouncements = Announcement::where('type', 'global')->latest()->get();

        // Also include targeted-to-this-tenant from Super Admin
        $targetedAnnouncements = Announcement::where('type', 'targeted')
                                    ->where('tenant_id', $tenantId)
                                    ->latest()->get();

        return view('tenant.announcements.index', compact('announcements', 'globalAnnouncements', 'targetedAnnouncements'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', tenant('id'))
                        ->where('employment_status', 'active')
                        ->with('branch')
                        ->orderBy('first_name')
                        ->get();

        $branches = Branch::where('tenant_id', tenant('id'))->orderBy('name')->get();

        return view('tenant.announcements.create', compact('employees', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'body'         => ['required', 'string'],
            'send_email'   => ['nullable', 'boolean'],
            'audience'     => ['required', 'in:all_employees,specific_employees'],
            'employee_ids' => ['required_if:audience,specific_employees', 'array', 'min:1'],
        ]);

        $tenantId  = tenant('id');
        $sendEmail = $request->boolean('send_email');

        if ($request->audience === 'all_employees') {
            Announcement::create([
                'title'        => $request->title,
                'body'         => $request->body,
                'meeting_link' => $request->meeting_link,
                'type'         => 'facility',
                'sender_type'  => 'tenant',
                'tenant_id'    => $tenantId,
                'send_email'   => $sendEmail,
                'employee_id'  => null,
                'branch_id'    => null,
            ]);

            $users = User::where('tenant_id', $tenantId)->whereNotNull('employee_id')->get();
            foreach ($users as $empUser) {
                TenantNotification::create([
                    'tenant_id' => $tenantId,
                    'user_id'   => $empUser->id,
                    'title'     => 'New Announcement',
                    'message'   => $request->title,
                    'type'      => 'info',
                    'link'      => route('tenant.announcements.index'),
                ]);

                if ($sendEmail) {
                    try {
                        $ann = new Announcement(['title' => $request->title, 'body' => $request->body, 'meeting_link' => $request->meeting_link]);
                        $ann->created_at = now();
                        Mail::to($empUser->email)->send(new AnnouncementAlert(
                            $ann,
                            $empUser->name,
                            tenant('name') ?? 'Facility Admin',
                            'https://' . request()->getHost() . '/announcements'
                        ));
                    } catch (\Exception $e) {}
                }
            }

            return redirect()->route('tenant.announcements.index')
                             ->with('success', 'Announcement sent to all employees.');
        }

        // Specific employees
        $employees = Employee::whereIn('id', $request->employee_ids)
                        ->where('tenant_id', $tenantId)
                        ->get();

        foreach ($employees as $employee) {
            Announcement::create([
                'title'        => $request->title,
                'body'         => $request->body,
                'meeting_link' => $request->meeting_link,
                'type'         => 'facility',
                'sender_type'  => 'tenant',
                'tenant_id'    => $tenantId,
                'send_email'   => $sendEmail,
                'employee_id'  => $employee->id,
                'branch_id'    => null,
            ]);

            $empUser = User::where('tenant_id', $tenantId)->where('employee_id', $employee->id)->first();
            if ($empUser) {
                TenantNotification::create([
                    'tenant_id' => $tenantId,
                    'user_id'   => $empUser->id,
                    'title'     => 'New Announcement',
                    'message'   => $request->title,
                    'type'      => 'info',
                    'link'      => route('tenant.announcements.index'),
                ]);
            }

            if ($sendEmail && $employee->email) {
                try {
                    $ann = new Announcement(['title' => $request->title, 'body' => $request->body, 'meeting_link' => $request->meeting_link]);
                    $ann->created_at = now();
                    Mail::to($employee->email)->send(new AnnouncementAlert(
                        $ann,
                        $employee->first_name . ' ' . $employee->last_name,
                        tenant('name') ?? 'Facility Admin',
                        'https://' . request()->getHost() . '/announcements'
                    ));
                } catch (\Exception $e) {}
            }
        }

        $count = $employees->count();
        return redirect()->route('tenant.announcements.index')
                         ->with('success', "Announcement sent to {$count} " . ($count === 1 ? 'employee' : 'employees') . '.');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->tenant_id !== tenant('id')) {
            abort(403);
        }

        // If employee-targeted, delete all sibling rows in the same batch
        if ($announcement->employee_id !== null) {
            Announcement::where('tenant_id', $announcement->tenant_id)
                ->where('title', $announcement->title)
                ->where('body', $announcement->body)
                ->whereNotNull('employee_id')
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') = DATE_FORMAT(?, '%Y-%m-%d %H:%i')", [$announcement->created_at])
                ->delete();
        } else {
            $announcement->delete();
        }

        return back()->with('success', 'Announcement deleted.');
    }
}
