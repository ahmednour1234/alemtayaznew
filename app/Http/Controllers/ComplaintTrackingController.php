<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\View\View;

class ComplaintTrackingController extends Controller
{
    public function show(string $token): View
    {
        $complaint = Complaint::where('public_token', $token)
            ->with(['client', 'worker', 'branch', 'attachments'])
            ->firstOrFail();

        $daysOpen = (int) $complaint->created_at->diffInDays(now());

        return view('complaints.track', compact('complaint', 'daysOpen'));
    }
}
