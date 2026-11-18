# سایت لیست قیمت تجهیزات دندانپزشکی بلوچی (Laravel 12)

نسخه‌ی لاراولی سایت تک‌صفحه‌ای لیست قیمت، با ظاهر همان طرح اصلی، به‌همراه ثبت‌نام و ورود و پنل مدیریتی که فقط ادمین به آن دسترسی دارد.

## معماری (چندلایه بر پایه MVC)

```
Route → Middleware (auth, admin, throttle) → FormRequest (اعتبارسنجی)
      → Controller → Service (منطق کسب‌وکار، آپلود فایل)
      → Repository (Interface + Eloquent) → Model → Database
      → View (Blade)
```

| لایه | مسیر |
|---|---|
| روت‌ها | `routes/web.php`، دستور `admin:create` در `routes/console.php` |
| کنترلرها | `app/Http/Controllers` (`HomeController`، `Auth/*`، `Admin/*`) |
| اعتبارسنجی | `app/Http/Requests` |
| میدل‌ورها | `app/Http/Middleware` (`EnsureUserIsAdmin`، `SecurityHeaders`) |
| سرویس‌ها | `app/Services` |
| ریپازیتوری‌ها | `app/Repositories/Contracts` و `app/Repositories/Eloquent` |
| مدل‌ها و Enumها | `app/Models` و `app/Enums` |
| مایگریشن‌ها و سیدرها | `database/migrations` و `database/seeders` (داده‌ی اولیه در `seeders/data/catalog.json`) |
| ویوها | `resources/views` (`public`، `auth`، `admin`) |
| CSS و JS | `public/css/app.css`، `public/js/price-list.js`، `public/js/admin.js` |

## امکانات پنل مدیریت (`/admin`)

- **محصولات:** افزودن، ویرایش و حذف، جستجو، فیلتر دسته، صفحه‌بندی، چهار قیمت به‌همراه شرایط اقساط، آپلود عکس و PDF یا وارد کردن لینک.
- **دسته‌ها:** افزودن، ویرایش و حذف (نوع، دیجیتال، آیکون، ترتیب). دسته‌ای که محصول دارد حذف نمی‌شود.
- **تنظیمات:** همه‌ی اطلاعات شرکت، اطلاعیه، آدرس و لوگو (آپلود یا لینک).
- **کاربران:** فهرست کاربران، مدیر کردن یا لغو دسترسی مدیر، و حذف کاربر.
- **خروجی:** دانلود نسخه‌ی پشتیبان JSON.
- **راهنما**
- با هر ذخیره یا حذف محصول، «آخرین بروزرسانی قیمت‌ها» به‌صورت خودکار با تاریخ شمسی ثبت می‌شود.

## امنیت

- هیچ ایمیل یا رمزی داخل کد یا صفحه نیست. رمزها با bcrypt هش می‌شوند.
- کسی که ثبت‌نام می‌کند همیشه کاربر عادی است (`is_admin` قابل Mass Assignment نیست).
- همه‌ی مسیرهای `/admin` پشت `auth` و `admin` هستند و برای کاربر عادی خطای 403 برمی‌گردانند.
- محدودیت تعداد تلاش برای ورود (۵ بار ناموفق ← قفل موقت)، برای ثبت‌نام و برای پنل.
- محافظت CSRF، بازسازی Session بعد از ورود و رمزنگاری Session.
- هدرهای امنیتی، از جمله CSP سخت‌گیرانه (`script-src 'self'`، بدون اسکریپت inline)، `X-Frame-Options`، `nosniff` و HSTS روی HTTPS.
- جلوگیری از XSS: خروجی Blade escape می‌شود، JSON صفحه با `JSON_HEX_*` درج می‌شود و JS همه‌ی داده‌ها را escape می‌کند.
- اعتبارسنجی آپلود: فقط jpg/png/webp و PDF با محدودیت حجم (SVG پذیرفته نمی‌شود). لینک‌ها فقط با `http` یا `https` پذیرفته می‌شوند. آیکون دسته فقط از لیست سفید انتخاب می‌شود.

## نصب

```bash
composer install
cp .env.example .env
php artisan key:generate
# دیتابیس: پیش‌فرض SQLite است؛ برای MySQL مقادیر DB_* را در .env تنظیم کنید
touch database/database.sqlite
php artisan migrate --seed      # تنظیمات، ۱۵ دسته و ۷۳ محصول فایل اصلی
php artisan storage:link
php artisan admin:create        # ساخت ادمین (ایمیل و رمز به‌صورت مخفی پرسیده می‌شود)
php artisan serve
```

برای ساخت ادمین می‌توانید `ADMIN_EMAIL` و `ADMIN_PASSWORD` را هم در `.env` بگذارید و `php artisan db:seed` را اجرا کنید. بعد از ساخت ادمین، این مقادیر را از `.env` پاک کنید.

### Production

`APP_ENV=production`، `APP_DEBUG=false`، `SESSION_SECURE_COOKIE=true` و استفاده از HTTPS.

## تست

```bash
php artisan test
```
