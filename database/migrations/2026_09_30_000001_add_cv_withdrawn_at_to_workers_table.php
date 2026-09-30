<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * لحظة سحب السيرة من العرض العام.
 *
 * تُملأ عند أوّل حجز ولا تُفرَّغ بعدها أبداً، فالسيرة التي رآها عميل وحُجزت
 * له لا تعود إلى الموقع حتى لو فُكّ الحجز وعادت حالة العاملة إلى «متاحة».
 *
 * نستخدم عموداً مستقلاً لا حالة العاملة، لأن الحالة تتغيّر من ثمانية مواضع
 * في النظام (فكّ تعيين، إلغاء عقد، إلغاء تأشيرة، تنظيف تلقائي…) وكلّ واحد
 * منها كان سيُعيد السيرة إلى العرض من حيث لا يُقصد.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->timestamp('cv_withdrawn_at')->nullable()->after('cv_disk');

            // الاستعلام العام يُصفّي بهذا العمود مع الحالة
            $table->index('cv_withdrawn_at');
        });

        // السير المحجوزة أو المُسندة الآن سُحبت فعلاً من العرض،
        // فنُثبّت ذلك حتى لا تعود لو فُكّ ارتباطها لاحقاً.
        \Illuminate\Support\Facades\DB::table('workers')
            ->whereNull('cv_withdrawn_at')
            ->where(function ($q) {
                $q->whereIn('status', ['reserved', 'assigned'])
                  ->orWhereNotNull('client_id');
            })
            ->update(['cv_withdrawn_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropIndex(['cv_withdrawn_at']);
            $table->dropColumn('cv_withdrawn_at');
        });
    }
};
