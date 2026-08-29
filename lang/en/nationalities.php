<?php

/**
 * English nationality names keyed by ISO country code.
 *
 * The `nationalities` table stores the Arabic name only, so English names live
 * here and are matched by `code` — which is stable and unaffected by edits to
 * the Arabic name. A code with no entry falls back to the stored Arabic name.
 */
return [
    'BD' => 'Bangladesh',
    'LK' => 'Sri Lanka',
    'PH' => 'Philippines',
    'KE' => 'Kenya',
    'ET' => 'Ethiopia',
    'BI' => 'Burundi',
    'UG' => 'Uganda',
    'IN' => 'India',
    'ID' => 'Indonesia',
    'NP' => 'Nepal',
    'GH' => 'Ghana',
    'TZ' => 'Tanzania',
    'RW' => 'Rwanda',
    'MG' => 'Madagascar',
    'PK' => 'Pakistan',
    'MM' => 'Myanmar',
    'VN' => 'Vietnam',
    'TH' => 'Thailand',
];
