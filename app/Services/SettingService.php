<?php

namespace App\Services;

use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Support\Jalali;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    private const CACHE_KEY = 'site.settings';

    /** کلیدهای قابل ویرایش در پنل ادمین => برچسب فارسی */
    public const EDITABLE = [
        'companyName' => 'نام شرکت',
        'companyNameEn' => 'نام انگلیسی',
        'manager' => 'مدیر',
        'phone1' => 'تلفن ۱',
        'phone2' => 'تلفن ۲',
        'mobile' => 'موبایل',
        'email' => 'ایمیل',
        'instagram' => 'اینستاگرام (نام)',
        'instagramUrl' => 'لینک اینستاگرام',
        'workingHours' => 'ساعات کار',
        'warranty' => 'گارانتی',
        'experience' => 'سابقه',
        'lastUpdate' => 'بروزرسانی',
        'lastPriceUpdate' => 'آخرین بروزرسانی قیمت',
        'logoUrl' => 'لوگو (URL)',
        'address' => 'آدرس',
        'notice' => 'اطلاعیه',
    ];

    public function __construct(private readonly SettingRepositoryInterface $settings)
    {
    }

    /** @return array<string, string> */
    public function all(): array
    {
        $stored = Cache::rememberForever(self::CACHE_KEY, fn () => $this->settings->all());

        $defaults = array_fill_keys(array_keys(self::EDITABLE), '');
        $defaults['logoPath'] = '';

        return array_map(fn ($v) => (string) $v, array_merge($defaults, $stored));
    }

    /** تنظیمات قابل نمایش در سایت عمومی (لوگوی آپلودی جایگزین لینک می‌شود) */
    public function forPublic(): array
    {
        $s = $this->all();
        $s['logo'] = $this->logoSrc($s);
        unset($s['logoPath']);

        return $s;
    }

    public function logoSrc(?array $s = null): string
    {
        $s ??= $this->all();

        return $s['logoPath'] !== ''
            ? Storage::disk('public')->url($s['logoPath'])
            : $s['logoUrl'];
    }

    /** @param array<string, string|null> $values */
    public function update(array $values, ?UploadedFile $logo = null, bool $removeLogo = false): void
    {
        $values = array_intersect_key($values, self::EDITABLE);
        $current = $this->all();

        if (($removeLogo || $logo) && $current['logoPath'] !== '') {
            Storage::disk('public')->delete($current['logoPath']);
            $values['logoPath'] = null;
        }

        if ($logo) {
            $values['logoPath'] = $logo->store('logos', 'public');
        }

        $this->settings->putMany($values);
        $this->flush();
    }

    /** با هر تغییر محصول، زمان آخرین بروزرسانی قیمت ثبت می‌شود */
    public function touchLastPriceUpdate(): void
    {
        $this->settings->putMany(['lastPriceUpdate' => Jalali::dateTime(now())]);
        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
