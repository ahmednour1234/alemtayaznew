<?php

namespace App\Http\Controllers\Admin\Security;

use App\Http\Controllers\Controller;
use App\Models\Security\AccessLog;
use App\Models\Security\ExportLog;
use App\Models\Security\FailedLoginLog;
use App\Models\Security\PermissionChangeLog;
use App\Models\Security\SecurityIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecurityLogController extends Controller
{
    // ── Security incidents ──────────────────────────────────────────────────
    public function incidents(Request $request)
    {
        $incidents = SecurityIncident::with(['user', 'resolver'])
            ->when($request->filled('status'),   fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('severity'), fn ($q) => $q->where('severity', $request->severity))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.security.incidents', compact('incidents'));
    }

    public function updateIncidentStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', SecurityIncident::STATUSES)],
        ]);

        $incident = SecurityIncident::findOrFail($id);
        $incident->status = $data['status'];

        if (in_array($data['status'], ['resolved', 'false_positive'], true)) {
            $incident->resolved_by = Auth::guard('admin')->id();
            $incident->resolved_at = now();
        } else {
            $incident->resolved_by = null;
            $incident->resolved_at = null;
        }
        $incident->save();

        return back()->with('success', 'تم تحديث حالة الحادثة الأمنية.');
    }

    // ── Access logs ─────────────────────────────────────────────────────────
    public function accessLogs(Request $request)
    {
        $logs = AccessLog::with('user')
            ->when($request->filled('action_type'), fn ($q) => $q->where('action_type', $request->action_type))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.security.access-logs', compact('logs'));
    }

    // ── Failed logins ───────────────────────────────────────────────────────
    public function failedLogins(Request $request)
    {
        $logs = FailedLoginLog::query()
            ->when($request->filled('email'), fn ($q) => $q->where('email', 'like', '%' . $request->email . '%'))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.security.failed-logins', compact('logs'));
    }

    // ── Export / download logs ──────────────────────────────────────────────
    public function exportLogs(Request $request)
    {
        $logs = ExportLog::with('user')
            ->when($request->filled('export_type'), fn ($q) => $q->where('export_type', $request->export_type))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.security.export-logs', compact('logs'));
    }

    // ── Role / permission change logs ───────────────────────────────────────
    public function permissionChanges(Request $request)
    {
        $logs = PermissionChangeLog::with(['changedBy', 'targetUser'])
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->action))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.security.permission-changes', compact('logs'));
    }
}
