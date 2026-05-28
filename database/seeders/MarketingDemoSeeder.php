<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Campaign;
use App\Models\Lead;
use App\Models\LeadCallLog;
use App\Models\Nationality;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MarketingDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = Admin::query()->orderBy('id')->first();
        $branches = Branch::where('active', true)->get();
        if (! $admin || $branches->isEmpty()) {
            $this->command->warn('MarketingDemoSeeder: لا يوجد مديرين أو فروع. تجاوز.');
            return;
        }

        $nationalityIds = Nationality::pluck('id')->all();

        $campaignsData = [
            ['name' => 'حملة سناب شات — الرياض',       'budget' => 5000,  'source' => 'snapchat'],
            ['name' => 'حملة تيك توك — جدة',          'budget' => 3500,  'source' => 'tiktok'],
            ['name' => 'حملة إنستجرام — الدمام',      'budget' => 4200,  'source' => 'instagram'],
            ['name' => 'حملة جوجل ادز — عام',         'budget' => 8000,  'source' => 'google_ads'],
            ['name' => 'حملة واتساب بزنس — رمضان',  'budget' => 2000,  'source' => 'whatsapp'],
        ];

        foreach ($campaignsData as $i => $cd) {
            $branch   = $branches[$i % $branches->count()];
            $campaign = Campaign::firstOrCreate(
                ['name' => $cd['name']],
                [
                    'description' => 'حملة تجريبية للقسم التسويقي',
                    'budget'      => $cd['budget'],
                    'start_date'  => Carbon::now()->subDays(rand(30, 90))->toDateString(),
                    'end_date'    => Carbon::now()->addDays(rand(15, 60))->toDateString(),
                    'branch_id'   => $branch->id,
                    'admin_id'    => $admin->id,
                    'active'      => true,
                ]
            );

            // Skip if already seeded leads for this campaign
            if ($campaign->leads()->exists()) continue;

            $sampleNames = [
                'محمد علي', 'فاطمة الزهراء', 'أحمد الشهري', 'نورة العتيبي', 'خالد الحربي',
                'سارة المطيري', 'عبدالله القحطاني', 'منى الدوسري', 'سعد الغامدي', 'هدى الزهراني',
                'ياسر السبيعي', 'ريم العنزي', 'فهد المالكي', 'لمى الشمري', 'بدر الرشيدي',
            ];
            $cities = ['الرياض', 'جدة', 'الدمام', 'مكة', 'الخبر', 'الطائف', 'تبوك', 'بريدة'];

            $leadsCount = rand(10, 18);
            for ($j = 0; $j < $leadsCount; $j++) {
                $status = $this->pickStatus();

                $lead = Lead::create([
                    'campaign_id'        => $campaign->id,
                    'name'               => $sampleNames[array_rand($sampleNames)] . ' ' . ($j + 1),
                    'phone'              => '+9665' . rand(10000000, 99999999),
                    'city'               => $cities[array_rand($cities)],
                    'nationality_id'     => $nationalityIds ? $nationalityIds[array_rand($nationalityIds)] : null,
                    'branch_id'          => $branch->id,
                    'assigned_admin_id'  => $admin->id,
                    'source'             => $cd['source'],
                    'status'             => $status,
                    'last_contacted_at'  => $status === 'new' ? null : Carbon::now()->subDays(rand(0, 14)),
                    'created_at'         => Carbon::now()->subDays(rand(0, 60)),
                ]);

                // Add some call logs
                if ($status !== 'new') {
                    $logsCount = rand(1, 3);
                    for ($k = 0; $k < $logsCount; $k++) {
                        LeadCallLog::create([
                            'lead_id'  => $lead->id,
                            'admin_id' => $admin->id,
                            'status'   => $this->pickCallStatus(),
                            'notes'    => 'مكالمة تجريبية رقم ' . ($k + 1),
                            'created_at' => $lead->created_at->copy()->addDays($k + 1),
                        ]);
                    }
                }
            }
        }

        $this->command->info('MarketingDemoSeeder: تم إنشاء بيانات تجريبية للقسم التسويقي.');
    }

    private function pickStatus(): string
    {
        $r = rand(1, 100);
        if ($r <= 35) return 'new';
        if ($r <= 75) return 'in_progress';
        if ($r <= 90) return 'converted';
        return 'archived';
    }

    private function pickCallStatus(): string
    {
        $options = ['no_answer', 'need_followup', 'nationality_unavailable', 'wants_rent', 'not_suitable', 'profiles_rejected'];
        return $options[array_rand($options)];
    }
}
