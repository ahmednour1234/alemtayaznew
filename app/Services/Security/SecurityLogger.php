<?php

namespace App\Services\Security;

use App\Models\Security\AccessLog;
use App\Models\Security\ExportLog;
use App\Models\Security\FailedLoginLog;
use App\Models\Security\PermissionChangeLog;
use App\Models\Security\SecurityIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Single entry point for all security/compliance logging. Keeps logging logic out
 * of controllers and guarantees consistent redaction of sensitive payloads so that
 * no secrets, tokens, passwords or PII are ever persisted to the audit tables.
 */
class SecurityLogger
{
    /** Keys whose values must never be written to a log row. */
    private const SENSITIVE_KEYS = [
        'password', 'password_confirmation', 'current_password', 'new_password',
        'remember', '_token', 'token', 'api_token', 'access_token', 'secret',
        'api_key', 'authorization', 'national_id', 'national_id_hash',
        'passport_number', 'iqama_number', 'phone', 'phone_hash',
    ];

    /** Replace sensitive values with a placeholder, recursively. */
    public function redact(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->redact($value);
            } elseif (in_array(strtolower((string) $key), self::SENSITIVE_KEYS, true)) {
                $data[$key] = '***';
            }
        }
        return $data;
    }

    private function currentAdminId(): ?int
    {
        return Auth::guard('admin')->id();
    }

    public function logAccess(Request $request, ?int $statusCode, string $actionType, array $metadata = []): AccessLog
    {
        return AccessLog::create([
            'user_id'     => $this->currentAdminId(),
            'guard'       => 'admin',
            'route_name'  => $request->route()?->getName(),
            'url'         => $request->fullUrl(),
            'method'      => $request->method(),
            'ip_address'  => $request->ip(),
            'user_agent'  => substr((string) $request->userAgent(), 0, 1000),
            'status_code' => $statusCode,
            'action_type' => $actionType,
            'metadata'    => $metadata ? $this->redact($metadata) : null,
        ]);
    }

    public function logFailedLogin(?string $email, string $guard, Request $request, string $reason): FailedLoginLog
    {
        return FailedLoginLog::create([
            'email'          => $email,
            'guard'          => $guard,
            'ip_address'     => $request->ip(),
            'user_agent'     => substr((string) $request->userAgent(), 0, 1000),
            'failure_reason' => $reason,
        ]);
    }

    public function logExport(Request $request, string $exportType, ?string $modelType = null, ?int $modelId = null, ?string $fileName = null, array $filters = []): ExportLog
    {
        return ExportLog::create([
            'user_id'     => $this->currentAdminId(),
            'export_type' => $exportType,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'file_name'   => $fileName,
            'filters'     => $filters ? $this->redact($filters) : null,
            'ip_address'  => $request->ip(),
            'user_agent'  => substr((string) $request->userAgent(), 0, 1000),
        ]);
    }

    public function logPermissionChange(string $action, array $attributes = []): PermissionChangeLog
    {
        $request = request();

        return PermissionChangeLog::create(array_merge([
            'changed_by'     => $this->currentAdminId(),
            'target_user_id' => null,
            'role_id'        => null,
            'permission_id'  => null,
            'action'         => $action,
            'before'         => null,
            'after'          => null,
            'ip_address'     => $request?->ip(),
            'user_agent'     => substr((string) $request?->userAgent(), 0, 1000),
        ], $attributes));
    }

    public function logIncident(string $type, string $severity, string $description, array $metadata = [], ?string $relatedType = null, ?int $relatedId = null): SecurityIncident
    {
        $request = request();

        return SecurityIncident::create([
            'type'               => $type,
            'severity'           => $severity,
            'description'        => $description,
            'user_id'            => $this->currentAdminId(),
            'ip_address'         => $request?->ip(),
            'user_agent'         => substr((string) $request?->userAgent(), 0, 1000),
            'related_model_type' => $relatedType,
            'related_model_id'   => $relatedId,
            'metadata'           => $metadata ? $this->redact($metadata) : null,
            'status'             => 'open',
        ]);
    }
}
