<?php

namespace Database\Seeders\Main;

use App\Models\Agent;
use App\Models\Nationality;
use Illuminate\Database\Seeder;

class AgentMainSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // ── سريلانكا ────────────────────────────────────────────────────
            'LK' => [
                'R.K.N. FOREIGN EMPLOYMENT AGENCY',
                'EMPIRE RECRUITMENT',
                'MUTHALIB ENTERPRISES',
                'WORLD AIR FOREIGN EMPLOYMENT AGENCY',
                'THE NATION RECRUITMENTS',
            ],
            // ── إثيوبيا ─────────────────────────────────────────────────────
            'ET' => [
                'ABI MIFTAH PRIVATE FORIEGN EMPLOYMENT AGENCY',
                'ALMAMORA FOREIGN EMPLOYMENT AGNT PLC',
                'ABURIJAL PLC',
                'GOLDEN SEASON FORIGN EMPLOYMENT PLC',
                'SABOLA FOREIGN EMPLOYMENT AGENCY',
            ],
            // ── الفلبين ─────────────────────────────────────────────────────
            'PH' => [
                'INCORPORATED SERVICES DEVELOPMENT',
                'LEILA INTERNATIONAL SERVICES INC',
            ],
            // ── بنجلاديش ────────────────────────────────────────────────────
            'BD' => [
                'M/S NATIONAL RECRUITING AGENCY',
                'RAFIQ AND SONS INTERNATIONAL',
                'RANGER INTERNATIONAL',
                '112A/CENTRAL OVERSEAS',
            ],
            // ── أوغندا ──────────────────────────────────────────────────────
            'UG' => [
                'TERRYSOME INVESTMENTS LIMITED',
                'KRYSTAL RECRUITERS (U) LTD',
            ],
            // ── كينيا ───────────────────────────────────────────────────────
            'KE' => [
                'GLOBAL DREAM AGENCY LIMITED',
                'JUEFELENT AGENCY LIMITED',
                'MAJORDOMO AGENCIES LIMITED',
            ],
            // ── بوروندي ─────────────────────────────────────────────────────
            'BI' => [
                'TARGET MANPOWER COMPANY S.U.R.L',
            ],
        ];

        $total = 0;
        foreach ($data as $code => $names) {
            $nationality = Nationality::where('code', $code)->first();
            if (! $nationality) {
                $this->command->warn("Nationality not found for code: {$code} — skipping");
                continue;
            }

            foreach ($names as $name) {
                Agent::firstOrCreate(
                    ['name' => $name, 'nationality_id' => $nationality->id],
                    ['active' => true]
                );
                $total++;
            }
        }

        $this->command->info("✓ Agents seeded ({$total})");
    }
}
