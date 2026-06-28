<?php

namespace App\Http\Middleware;

use App\Services\Security\SecurityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records access to sensitive admin actions and any export/download of sensitive
 * data. Deliberately NOT a catch-all: only routes matching the sensitive/export
 * allowlists below are logged, so static assets, dashboards, polling and other
 * noisy routes never reach the audit tables.
 */
class LogAccess
{
    /** Route-name suffixes that represent an export/download of data. */
    private const EXPORT_SUFFIXES = ['.export', '.template', '.print', '.download'];

    /** Explicit export/download routes that don't match the suffixes above. */
    private const EXPORT_ROUTES = [
        'admin.workers.cv', 'admin.workers.passport',
        'admin.reports.type-breakdown.excel', 'admin.reports.type-breakdown.pdf',
    ];

    /** Sensitive-data detail views worth recording on access. */
    private const SENSITIVE_ROUTES = [
        'admin.clients.show', 'admin.workers.show', 'admin.contracts.show',
        'admin.complaints.show', 'admin.agents.show', 'admin.hr.employees.show',
        'admin.sponsorship-transfers.show',
    ];

    /** Route-name prefixes whose every action is sensitive (RBAC + security admin). */
    private const SENSITIVE_PREFIXES = [
        'admin.settings.admins.', 'admin.settings.roles.', 'admin.settings.permissions',
        'admin.security.',
    ];

    public function __construct(private readonly SecurityLogger $logger) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $name = $request->route()?->getName();
            if ($name === null) {
                return $response;
            }

            $status = $response->getStatusCode();

            if ($this->isExport($name)) {
                $this->logger->logExport(
                    $request,
                    $this->exportType($name),
                    null,
                    $this->routeModelId($request),
                    null,
                    $request->query(),
                );
            } elseif ($this->isSensitive($name)) {
                $this->logger->logAccess($request, $status, 'sensitive_access', [
                    'route'  => $name,
                    'params' => $request->route()?->parameters() ?? [],
                ]);
            }
        } catch (\Throwable) {
            // Logging must never break the request lifecycle.
        }

        return $response;
    }

    private function isExport(string $name): bool
    {
        foreach (self::EXPORT_SUFFIXES as $suffix) {
            if (str_ends_with($name, $suffix)) {
                return true;
            }
        }
        return in_array($name, self::EXPORT_ROUTES, true);
    }

    private function isSensitive(string $name): bool
    {
        if (in_array($name, self::SENSITIVE_ROUTES, true)) {
            return true;
        }
        foreach (self::SENSITIVE_PREFIXES as $prefix) {
            if (str_starts_with($name, $prefix)) {
                return true;
            }
        }
        return false;
    }

    private function exportType(string $name): string
    {
        return match (true) {
            str_contains($name, 'pdf') || str_ends_with($name, '.print') => 'pdf',
            str_contains($name, 'excel')                                  => 'excel',
            str_ends_with($name, '.template')                             => 'template',
            str_ends_with($name, '.download')
                || str_ends_with($name, '.cv')
                || str_ends_with($name, '.passport')                      => 'download',
            default                                                       => 'export',
        };
    }

    private function routeModelId(Request $request): ?int
    {
        $id = $request->route('id') ?? $request->route('document') ?? $request->route('trip');
        return is_numeric($id) ? (int) $id : null;
    }
}
