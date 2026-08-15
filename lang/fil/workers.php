<?php

return [

    'title'        => 'Mga Manggagawa — Pamamahala ng CV',
    'add'          => 'Magdagdag ng manggagawa',
    'bulk_upload'  => 'Mag-upload ng maraming CV',
    'worker'       => 'Manggagawa',
    'reserved_by'  => 'Nagreserba',
    'cv'           => 'CV',
    'no_cv'        => 'Wala',
    'no_workers'   => 'Walang nahanap na manggagawa',
    'upload_now'   => 'Mag-upload ng CV ngayon',
    'trashed'      => 'Nabura',
    'search_ph'    => 'Pangalan, pasaporte, telepono...',
    'all_nats'     => 'Lahat ng nasyonalidad',
    'all_statuses' => 'Lahat ng katayuan',
    'all_profs'    => 'Lahat ng propesyon',
    'no_name'      => 'Walang pangalan',

    'statuses' => [
        'available'            => 'Available',
        'reserved'             => 'Nakareserba',
        'assigned'             => 'Naitalaga',
        'in_housing'           => 'Nasa tirahan',
        'sponsorship_transfer' => 'Paglipat ng sponsorship',
        'deportation'          => 'Deportasyon',
        'returned'             => 'Bumalik',
    ],

    // ── WhatsApp ─────────────────────────────────────────────────────────────
    'whatsapp' => [
        'send'          => 'Ipadala sa WhatsApp',
        'phone_ph'      => 'Numero ng WhatsApp (hal. 966501234567)',
        'ready'         => ':count CV handa nang ipadala',
        'all_results'   => 'Lahat ng resulta (:count)',
        'preparing'     => 'Naghahanda...',
        'no_cv_msg'     => 'Walang CV ang mga napiling manggagawa.',
        'fetch_failed'  => 'Hindi makuha ang listahan ng manggagawa. Pakisubukan muli.',
        'only_with_cv'  => 'Ang mga manggagawang may CV lamang ang isasama sa mensahe sa WhatsApp.',
        'no_cv_warning' => ':count sa mga napili ay walang CV — hindi sila isasama sa WhatsApp, ngunit mabubura pa rin kung pipindutin ang "Burahin ang napili".',
        'msg_header'    => 'Mga CV ng manggagawa para sa pagsusuri',
        'batch_confirm' => 'Ang :count na manggagawa ay hindi kasya sa isang mensahe, kaya hahatiin ito sa :batches magkakasunod na mensahe. Magpatuloy?',
        'batch_next'    => 'Nabuksan ang mensahe :current ng :total. Ipadala ito, pagkatapos pindutin ang OK para buksan ang mensahe :next.',
        'limit_confirm' => 'Ang unang :limit na manggagawa lamang mula sa :total ang ipapadala (maximum sa bawat operasyon).',
    ],

    // ── Pagpili ──────────────────────────────────────────────────────────────
    'selection' => [
        'page_only'   => ':count ang napili sa pahinang ito lamang.',
        'select_all'  => 'Piliin ang lahat ng tumutugmang resulta (:count)',
        'all_matched' => 'Napili na ang lahat ng :count tumutugmang resulta sa bawat pahina.',
        'page_enough' => 'Panatilihin ang pahinang ito lamang',
    ],

    // ── Pagbura ──────────────────────────────────────────────────────────────
    'delete' => [
        'confirm_one'  => 'Burahin ang manggagawang ito?',
        'confirm_bulk' => 'Burahin ang :count na manggagawa? Hindi mabubura ang mga nakaugnay sa kontrata ng recruitment.',
        'has_contract' => 'Hindi mabubura ang manggagawang nakaugnay sa kontrata ng recruitment.',
        'done'         => ':count na manggagawa ang nabura.',
        'skipped'      => ':count ang nilaktawan dahil nakaugnay sa kontrata ng recruitment: :names',
        'none_deleted' => 'Walang nabura — lahat ng napili ay nakaugnay sa kontrata ng recruitment: :names',
    ],

    // ── Pagtatalaga ──────────────────────────────────────────────────────────
    'assign' => [
        'title'        => 'Italaga ang manggagawa sa kliyente',
        'reserved_for' => 'Nakareserba ang manggagawa sa loob ng :hours oras. Gumawa ng kontrata bago matapos ang palugit, kung hindi ay awtomatikong maaalis ang reserbasyon.',
        'reserved_go'  => 'Nakareserba ang manggagawa sa loob ng :hours oras — kumpletuhin ang detalye ng kontrata ngayon.',
        'warning'      => 'Ang reserbasyon ay may bisa lamang sa loob ng :hours oras. Kung walang kontrata na magagawa sa loob nito, awtomatikong maaalis ang reserbasyon at muling magiging available ang manggagawa, may abiso sa iyo at sa manedyer ng sangay.',
        'already'      => 'Nakareserba na ang manggagawang ito para sa parehong kliyente at hindi maaaring i-reserba nang dalawang beses.',
        'other_client' => 'Nakareserba ang manggagawang ito para sa ibang kliyente. Kanselahin muna ang kasalukuyang reserbasyon.',
        'unassigned'   => 'Naalis sa pagkakatalaga ang manggagawa at available na ngayon.',
    ],

];
