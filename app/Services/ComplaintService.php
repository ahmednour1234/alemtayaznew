<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\RecruitmentContract;
use App\Repositories\Contracts\ComplaintRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComplaintService
{
    public function __construct(private readonly ComplaintRepositoryInterface $repo) {}

    public function list(array $filters = [])  { return $this->repo->getAll($filters); }
    public function find(int $id)              { return $this->repo->findById($id); }
    public function trashed()                  { return $this->repo->getTrashed(); }

    /**
     * @param  UploadedFile[]  $attachments
     */
    public function store(array $data, array $attachments = []): Complaint
    {
        return DB::transaction(function () use ($data, $attachments) {
            // Auto-derive client/worker/branch from contract if not provided
            if (!empty($data['contract_id'])) {
                $contract = RecruitmentContract::find($data['contract_id']);
                if ($contract) {
                    $data['client_id']     = $data['client_id']     ?? $contract->client_id;
                    $data['worker_id']     = $data['worker_id']     ?? $contract->worker_id;
                    $data['branch_id']     = $data['branch_id']     ?? $contract->branch_id;
                    $data['contract_type'] = $data['contract_type'] ?? 'recruitment';
                }
            }

            $data['complaint_number'] = Complaint::generateNumber();
            $data['public_token']     = Str::random(48);
            $data['status']           = $data['status']   ?? 'new';
            $data['priority']         = $data['priority'] ?? 'medium';
            $data['on_musaned']       = !empty($data['on_musaned']);

            if (($data['status'] ?? null) === 'in_progress' && empty($data['processed_at'])) {
                $data['processed_at'] = now();
            }
            if (($data['status'] ?? null) === 'resolved' && empty($data['resolved_at'])) {
                $data['resolved_at'] = now();
            }

            $complaint = $this->repo->create($data);
            $this->storeAttachments($complaint, $attachments);
            $this->notifyAssignment($complaint, null);
            $this->notifyBranch($complaint, 'new');

            return $complaint;
        });
    }

    /**
     * @param  UploadedFile[]  $attachments
     */
    public function update(int $id, array $data, array $attachments = []): Complaint
    {
        return DB::transaction(function () use ($id, $data, $attachments) {
            $old = $this->repo->findById($id);

            // Re-derive from contract if changed
            if (!empty($data['contract_id']) && $data['contract_id'] != $old->contract_id) {
                $contract = RecruitmentContract::find($data['contract_id']);
                if ($contract) {
                    $data['client_id']     = $data['client_id']     ?: $contract->client_id;
                    $data['worker_id']     = $data['worker_id']     ?: $contract->worker_id;
                    $data['branch_id']     = $data['branch_id']     ?: $contract->branch_id;
                    $data['contract_type'] = $data['contract_type'] ?: 'recruitment';
                }
            }

            $data['on_musaned'] = !empty($data['on_musaned']);

            $newStatus = $data['status']   ?? $old->status;
            $oldStatus = $old->status;
            if ($newStatus === 'in_progress' && !$old->processed_at && empty($data['processed_at'])) {
                $data['processed_at'] = now();
            }
            if ($newStatus === 'resolved' && !$old->resolved_at && empty($data['resolved_at'])) {
                $data['resolved_at'] = now();
            }

            $complaint = $this->repo->update($id, $data);
            $this->storeAttachments($complaint, $attachments);

            // Notifications
            if (($data['assigned_admin_id'] ?? null) && $data['assigned_admin_id'] != $old->assigned_admin_id) {
                $this->notifyAssignment($complaint, $old->assigned_admin_id);
            }
            if ($newStatus !== $oldStatus) {
                $this->notifyBranch($complaint, 'status_changed');
            }

            return $complaint;
        });
    }

    public function destroy(int $id): void
    {
        $this->repo->delete($id);
    }

    public function restore(int $id): void
    {
        $this->repo->restore($id);
    }

    public function deleteAttachment(int $attachmentId): void
    {
        $att = ComplaintAttachment::findOrFail($attachmentId);
        Storage::disk('public')->delete($att->path);
        $att->delete();
    }

    /** @param  UploadedFile[]  $files */
    protected function storeAttachments(Complaint $complaint, array $files): void
    {
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) continue;
            $path = $file->store('complaints/attachments', 'public');
            $complaint->attachments()->create([
                'path'          => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
            ]);
        }
    }

    protected function notifyAssignment(Complaint $complaint, ?int $previousAdminId): void
    {
        if (!$complaint->assigned_admin_id) return;

        AdminNotification::create([
            'admin_id' => $complaint->assigned_admin_id,
            'type'     => 'complaint_assigned',
            'title'    => 'تم تعيين شكوى لك',
            'body'     => 'شكوى رقم ' . $complaint->complaint_number . ' — ' . $complaint->problem_type_label,
            'url'      => route('admin.complaints.show', $complaint->id),
        ]);
    }

    protected function notifyBranch(Complaint $complaint, string $event): void
    {
        if (!$complaint->branch_id) return;

        $branchAdmins = \App\Models\Admin::where('branch_id', $complaint->branch_id)
            ->where('id', '!=', $complaint->assigned_admin_id)
            ->pluck('id');

        $title = $event === 'new' ? 'شكوى جديدة في فرعك' : 'تحديث حالة شكوى';
        foreach ($branchAdmins as $adminId) {
            AdminNotification::create([
                'admin_id' => $adminId,
                'type'     => 'complaint_' . $event,
                'title'    => $title,
                'body'     => 'شكوى رقم ' . $complaint->complaint_number . ' — ' . $complaint->status_label,
                'url'      => route('admin.complaints.show', $complaint->id),
            ]);
        }
    }

    /** Used by NotifyStaleComplaints command */
    public function notifyStale(int $days = 7): int
    {
        $stale = $this->repo->getStaleNew($days);
        $count = 0;

        foreach ($stale as $c) {
            $recipients = collect();
            if ($c->assigned_admin_id) $recipients->push($c->assigned_admin_id);
            if ($c->branch_id) {
                $recipients = $recipients->merge(
                    \App\Models\Admin::where('branch_id', $c->branch_id)->pluck('id')
                );
            }
            // Plus super admins
            $recipients = $recipients->merge(
                \App\Models\Admin::whereNull('branch_id')->pluck('id')
            )->unique();

            foreach ($recipients as $adminId) {
                AdminNotification::create([
                    'admin_id' => $adminId,
                    'type'     => 'complaint_stale',
                    'title'    => 'شكوى بدون حل منذ أكثر من ' . $days . ' أيام',
                    'body'     => 'شكوى رقم ' . $c->complaint_number . ' لم يتم حلها — يرجى المتابعة عاجلًا',
                    'url'      => route('admin.complaints.show', $c->id),
                ]);
            }

            $c->update(['last_stale_notified_at' => now()]);
            $count++;
        }

        return $count;
    }
}
