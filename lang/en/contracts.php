<?php

return [

    'title'        => 'Recruitment contracts',
    'add'          => 'Add new contract',
    'edit'         => 'Edit contract',
    'contract'     => 'Contract',
    'trashed'      => 'Deleted contracts',
    'search_ph'    => 'Search by passport / national ID / mobile / worker name / client name / visa no. / Musaned no...',
    'search_hint'  => 'Passport, national ID and mobile numbers must be entered in full (exact match)',
    'all_statuses' => '— All statuses —',
    'all_depts'    => '— All departments —',
    'no_contracts' => 'No contracts found',

    // ── Contract stages (15) ─────────────────────────────────────────────────
    'statuses' => [
        1  => 'New',
        2  => 'Foreign embassy approval',
        3  => 'Awaiting foreign office approval',
        4  => 'Foreign labour office accepted',
        5  => 'Awaiting approval (Musaned)',
        6  => 'Contract accepted by foreign labour office',
        7  => 'Visa sent to Saudi embassy',
        8  => 'Visa stamped',
        9  => 'Visa cancelled',
        10 => 'Travel permit after stamping',
        11 => 'Awaiting flight booking',
        12 => 'Arrival scheduled',
        13 => 'Received',
        14 => 'Returned within warranty period',
        15 => 'Absconded',
    ],

    // ── Stage descriptions ───────────────────────────────────────────────────
    'status_desc' => [
        1  => 'Client request received and contract file opened',
        2  => 'Worker file sent to the foreign embassy, awaiting approval',
        3  => 'Contract sent to the foreign office in the worker\'s country, awaiting reply',
        4  => 'Official acceptance received from the foreign office',
        5  => 'Awaiting electronic approval from the Musaned platform',
        6  => 'Final contract signed and confirmed by the foreign party',
        7  => 'Worker passport and file delivered to the Saudi embassy for stamping',
        8  => 'Worker passport received stamped from the Saudi embassy',
        9  => 'Visa refused or cancelled by the embassy for any reason',
        10 => 'Official travel permit issued for the worker',
        11 => 'Coordinating with the travel agency and booking the flight',
        12 => 'Flight and worker arrival time confirmed',
        13 => 'Worker received from the airport and handed over to the client',
        14 => 'Worker returned to the company within the warranty period (two years from arrival)',
        15 => 'Worker absconding recorded and reported',
    ],

    // ── Date hint per stage (shown in the form) ──────────────────────────────
    'status_example' => [
        1  => 'e.g. 2026-01-10 (date the client request was received)',
        2  => 'e.g. 2026-01-15 (date the file was submitted to the embassy)',
        3  => 'e.g. 2026-01-20 (date the contract was sent to the office)',
        4  => 'e.g. 2026-01-25 (date the acceptance reply arrived)',
        5  => 'e.g. 2026-01-28 (date approval was requested on Musaned)',
        6  => 'e.g. 2026-02-01 (date the contract was signed)',
        7  => 'e.g. 2026-02-05 (date the worker passport was sent)',
        8  => 'e.g. 2026-02-15 (date the stamped passport was received)',
        9  => 'e.g. 2026-02-10 (date of the refusal decision)',
        10 => 'e.g. 2026-02-20 (date the travel permit was issued)',
        11 => 'e.g. 2026-02-22 (date the booking was requested)',
        12 => 'e.g. 2026-03-01 (flight date and number)',
        13 => 'e.g. 2026-03-01 (actual handover date)',
        14 => 'e.g. 2026-04-10 (return date and reason)',
        15 => 'e.g. 2026-04-15 (date absconding was reported)',
    ],

    // ── Departments ──────────────────────────────────────────────────────────
    'departments' => [
        'customer_service' => 'Customer service',
        'accounts'         => 'Accounts',
        'coordination'     => 'Coordination',
    ],

    // ── Payment status ───────────────────────────────────────────────────────
    'payment' => [
        'pending' => 'Pending',
        'partial' => 'Partial',
        'full'    => 'Paid in full',
    ],

    // ── Visa types ───────────────────────────────────────────────────────────
    'visa_types' => [
        'domestic'       => 'Domestic worker visa',
        'rehabilitation' => 'Comprehensive rehabilitation visa',
    ],

    // ── Form fields ──────────────────────────────────────────────────────────
    'fields' => [
        'branch'          => 'Branch',
        'request_date'    => 'Request date (Gregorian)',
        'department'      => 'Contract at department',
        'visa_data'       => 'Visa details',
        'visa_image'      => 'Visa image',
        'visa_type'       => 'Visa type',
        'arrival_airport' => 'Arrival airport',
        'origin'          => 'Departure country (worker nationality)',
        'delivery_city'   => 'Delivery city',
        'musaned_date'    => 'Musaned contract date',
        'musaned_file'    => 'Musaned contract file',
        'worker_passport' => 'Worker passport number',
        'e_doc'           => 'Musaned e-documentation number',
        'agent'           => 'Agent',
        'total_cost'      => 'Total cost',
        'dates'           => 'Dates',
        'trial_end'       => 'Trial end date',
        'contract_end'    => 'Contract end date',
        'choose_worker'   => '— Select worker —',
        'choose_client'   => '— Select client —',
        'choose_branch'   => '— Select branch —',
        'choose'          => '— Select —',
    ],

    // ── Messages ─────────────────────────────────────────────────────────────
    'messages' => [
        'created'        => 'Contract :number created successfully',
        'updated'        => 'Contract updated successfully',
        'deleted'        => 'Contract deleted',
        'restored'       => 'Contract restored',
        'branch_hint'    => 'Selected automatically from the branch of the employee who reserved the worker — you can change it.',
        'passport_hint'  => 'Saved to the worker profile automatically.',
        'worker_locked'  => 'This worker is assigned to another client. The contract must be for the same client the worker is assigned to.',
        'dates_auto'     => 'Trial and contract end dates are calculated automatically from the arrival date',
        'cs_only'        => 'Creating contracts is restricted to the customer service department.',
        'wrong_branch'   => 'You cannot add contracts for a branch other than your own. Your branch: :branch',
    ],

];
