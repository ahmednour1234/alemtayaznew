<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Client;
use App\Models\RecruitmentContract;
use App\Models\Worker;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // ── Workers ──────────────────────────────────────────────────────────
        // passport_number & phone are encrypted at rest → exact-match via hash columns.
        Worker::where(function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('passport_number_hash', Worker::hashPii($q))
                  ->orWhere('phone_hash', Worker::hashPii($q));
        })->limit(5)->get()
          ->each(function ($w) use (&$results) {
              $results[] = [
                  'type'     => 'worker',
                  'label'    => 'عاملة',
                  'icon'     => 'user',
                  'title'    => $w->name,
                  'subtitle' => $w->passport_number ?? $w->phone,
                  'url'      => route('admin.workers.show', $w->id),
              ];
          });

        // ── Clients ───────────────────────────────────────────────────────────
        // national_id & phone are encrypted at rest → exact-match via hash columns.
        Client::where(function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('national_id_hash', Client::hashPii($q))
                  ->orWhere('phone_hash', Client::hashPii($q));
        })->limit(5)->get()
          ->each(function ($c) use (&$results) {
              $results[] = [
                  'type'     => 'client',
                  'label'    => 'عميل',
                  'icon'     => 'client',
                  'title'    => $c->name,
                  'subtitle' => $c->phone ?? $c->national_id,
                  'url'      => route('admin.clients.show', $c->id),
              ];
          });

        // ── Contracts ─────────────────────────────────────────────────────────
        RecruitmentContract::with(['client', 'worker'])
            ->where(function ($query) use ($q) {
                $query->where('contract_number', 'like', "%{$q}%")
                      ->orWhere('visa_number', 'like', "%{$q}%")
                      ->orWhere('musaned_number', 'like', "%{$q}%");
            })->limit(5)->get()
              ->each(function ($rc) use (&$results) {
                  $results[] = [
                      'type'     => 'contract',
                      'label'    => 'عقد',
                      'icon'     => 'contract',
                      'title'    => 'عقد #' . $rc->contract_number,
                      'subtitle' => optional($rc->client)->name ?? optional($rc->worker)->name,
                      'url'      => route('admin.contracts.show', $rc->id),
                  ];
              });

        // ── Agents ────────────────────────────────────────────────────────────
        Agent::where(function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('phone_hash', Agent::hashPii($q));
        })->limit(3)->get()
          ->each(function ($a) use (&$results) {
              $results[] = [
                  'type'     => 'agent',
                  'label'    => 'وسيط',
                  'icon'     => 'agent',
                  'title'    => $a->name,
                  'subtitle' => $a->phone,
                  'url'      => route('admin.agents.show', $a->id),
              ];
          });

        return response()->json(['results' => array_slice($results, 0, 15)]);
    }
}
