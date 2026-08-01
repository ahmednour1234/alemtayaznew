<?php

use Illuminate\Support\Facades\Request;

if (! function_exists('file_url')) {
    /**
     * Build a public URL for a file stored on the 'public' disk.
     *
     * Storage::url()/asset() derive their base from APP_URL, which breaks when the
     * app is served from a sub-path (e.g. https://host/public/...). This resolves
     * the base from the incoming request instead, so links work either way.
     */
    function file_url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        // Already an absolute URL — leave it alone.
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Routed through PHP via /file/... rather than /storage/..., which
        // Apache 403s when the public/storage symlink is missing or stale.
        $base = rtrim(Request::getSchemeAndHttpHost().Request::getBaseUrl(), '/');

        return $base.'/file/'.ltrim($path, '/');
    }
}
