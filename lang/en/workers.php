<?php

return [

    'title'        => 'Workers — CV Management',
    'add'          => 'Add worker',
    'bulk_upload'  => 'Bulk upload CVs',
    'worker'       => 'Worker',
    'reserved_by'  => 'Reserved by',
    'cv'           => 'CV',
    'no_cv'        => 'None',
    'no_workers'   => 'No workers found',
    'upload_now'   => 'Upload CVs now',
    'trashed'      => 'Deleted',
    'search_ph'    => 'ID, name, passport, phone...',
    'all_nats'     => 'All nationalities',
    'all_statuses' => 'All statuses',
    'all_profs'    => 'All professions',
    'no_name'      => 'Unnamed',

    // Worker form field labels
    'fields' => [
        'name'           => 'Name (Arabic)',
        'passport'       => 'Passport number',
        'nationality'    => 'Nationality',
        'profession'     => 'Profession',
        'gender'         => 'Gender',
        'experience'     => 'Experience',
        'religion'       => 'Religion',
        'age'            => 'Age',
        'phone'          => 'Phone',
        'status'         => 'Status',
        'branch'         => 'Branch',
        'notes'          => 'Notes',
        'cv_file'        => 'CV file (PDF)',
        'passport_image' => 'Passport image',
        'choose'         => 'Select...',
        'no_branch'      => 'No branch',
        'basic_data'     => 'Basic information',
        'edit_title'     => 'Edit worker details',
        'cv_current'     => 'Current CV — click to view',
        'cv_replace'     => 'Upload a new file to replace',
        'max_size'       => 'JPG / PNG — max 5MB',
        'save'           => 'Save changes',
        'cancel'         => 'Cancel',
        'img_replace'    => 'Upload a new image to replace',
        'passport_alt'   => 'Passport',
    ],


    'statuses' => [
        'available'            => 'Available',
        'reserved'             => 'Reserved',
        'assigned'             => 'Assigned',
        'in_housing'           => 'In housing',
        'sponsorship_transfer' => 'Sponsorship transfer',
        'deportation'          => 'Deportation',
        'returned'             => 'Returned',
    ],

    // ── WhatsApp ─────────────────────────────────────────────────────────────
    'whatsapp' => [
        'send'          => 'Send via WhatsApp',
        'phone_ph'      => 'WhatsApp number (e.g. 966501234567)',
        'ready'         => ':count CV ready to send',
        'all_results'   => 'All results (:count)',
        'preparing'     => 'Preparing...',
        'no_cv_msg'     => 'None of the selected workers have a CV.',
        'fetch_failed'  => 'Could not load the worker list. Please try again.',
        'only_with_cv'  => 'Only workers who have a CV will be included in the WhatsApp message.',
        'no_cv_warning' => ':count selected workers have no CV — they will be excluded from WhatsApp, but will still be deleted if you press "Delete selected".',
        'msg_header'    => 'Worker CVs for review',
        'batch_confirm' => ':count workers do not fit in one message, so they will be split into :batches consecutive messages. Continue?',
        'batch_next'    => 'Message :current of :total opened. Send it, then press OK to open message :next.',
        'limit_confirm' => 'Only the first :limit workers out of :total will be sent (maximum per operation).',
    ],

    // ── Bulk selection ───────────────────────────────────────────────────────
    'selection' => [
        'page_only'   => ':count selected on this page only.',
        'select_all'  => 'Select all matching results (:count)',
        'all_matched' => 'All :count matching results across every page are now selected.',
        'page_enough' => 'Keep this page only',
    ],

    // ── Deletion ─────────────────────────────────────────────────────────────
    'delete' => [
        'confirm_one'  => 'Delete this worker?',
        'confirm_bulk' => 'Delete :count workers? Workers linked to a recruitment contract will not be deleted.',
        'has_contract' => 'A worker linked to a recruitment contract cannot be deleted.',
        'done'         => ':count workers deleted.',
        'skipped'      => ':count skipped because they are linked to recruitment contracts: :names',
        'none_deleted' => 'No workers were deleted — all selected are linked to recruitment contracts: :names',
    ],

    // ── Assignment ───────────────────────────────────────────────────────────
    'assign' => [
        'title'        => 'Assign worker to client',
        'reserved_for' => 'Worker reserved for :hours hours. Create the recruitment contract before the deadline or the reservation is released automatically.',
        'reserved_go'  => 'Worker reserved for :hours hours — complete the contract details now.',
        'warning'      => 'The reservation is valid for :hours hours only. If no recruitment contract is created within that time, the reservation is released automatically and the worker becomes available again, with a notification sent to you and the branch manager.',
        'already'      => 'This worker is already reserved for the same client and cannot be reserved twice.',
        'other_client' => 'This worker is reserved for another client. Cancel the current reservation first.',
        'unassigned'   => 'Worker unassigned and is now available.',
        'confirm_unassign' => 'Unassign this worker and make her available again?',
        'no_permission'    => 'Only the employee who made the assignment can cancel it',
        'has_contract'     => 'Worker is linked to a recruitment contract — unlink from the contract page',
    ],

];
