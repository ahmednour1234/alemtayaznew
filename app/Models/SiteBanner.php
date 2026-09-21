<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * بانر إعلاني في الموقع العام.
 *
 * الصورة هي المحتوى كلّه؛ والضغط عليها يفتح واتساب برسالة استفسار جاهزة
 * ما لم يُحدَّد رابط بديل.
 */
class SiteBanner extends Model
{
    use HasFactory, SoftDeletes;

    /** المواضع المتاحة لعرض البانر. */
    public const PLACEMENTS = [
        'home'  => 'الصفحة الرئيسية',
        'cvs'   => 'صفحة السير الذاتية',
        'all'   => 'كل الصفحات',
    ];

    protected $fillable = [
        'title', 'image', 'image_mobile', 'alt', 'whatsapp_message',
        'link', 'placement', 'sort_order', 'active', 'starts_at', 'ends_at',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'active'    => 'boolean',
            'starts_at' => 'datetime',
            'ends_at'   => 'datetime',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * البانرات المعروضة الآن في موضع بعينه.
     *
     * «كل الصفحات» يظهر مع أي موضع، والنافذة الزمنية الفارغة تعني بلا حدّ —
     * فبانر بلا تواريخ يظل ظاهراً حتى يُعطَّل يدوياً.
     */
    public function scopeVisible(Builder $q, string $placement): Builder
    {
        return $q->where('active', true)
            ->whereIn('placement', [$placement, 'all'])
            ->where(fn ($w) => $w->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($w) => $w->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }

    public function imageUrl(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    public function imageMobileUrl(): ?string
    {
        return $this->image_mobile ? Storage::disk('public')->url($this->image_mobile) : null;
    }

    /**
     * وجهة الضغط على البانر.
     *
     * الرابط الصريح يسبق واتساب؛ وإن لم يوجد رقم واتساب في إعدادات الموقع
     * رجّعنا null فيُعرض البانر صورةً بلا رابط بدل رابط مكسور.
     */
    public function targetUrl(): ?string
    {
        if ($this->link) {
            return $this->link;
        }

        $phone = preg_replace('/\D/', '', (string) SiteSetting::value('whatsapp'));

        if (! $phone) {
            return null;
        }

        $text = $this->whatsapp_message ?: 'السلام عليكم، أرغب في الاستفسار عن العرض.';

        return 'https://wa.me/' . $phone . '?text=' . urlencode($text);
    }
}
