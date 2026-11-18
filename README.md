# سایت لیست قیمت تجهیزات دندانپزشکی بلوچی (Laravel 12)

نسخه‌ی لاراولی سایت تک‌صفحه‌ای لیست قیمت با ظاهر همان طرح اصلی.
لیست قیمت برای همه‌ی بازدیدکننده‌ها، بدون نیاز به ثبت‌نام یا ورود، نمایش داده می‌شود. ثبت‌نام عمومی وجود ندارد.
فقط مدیرها (از طریق دکمه‌ی ⚙ پایین صفحه یا آدرس `/login`) وارد پنل می‌شوند و محصولات را اضافه، ویرایش یا حذف می‌کنند.

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
| فونت و آیکون (محلی، بدون CDN) | `public/fonts/vazirmatn`، `public/vendor/fontawesome` |
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
- **مدیران:** ساخت مدیر جدید، ویرایش نام و ایمیل، تغییر رمز، و حذف حساب (حساب خودتان را نمی‌توانید حذف کنید).
- **خروجی:** دانلود نسخه‌ی پشتیبان JSON.
- **راهنما**
- با هر ذخیره یا حذف محصول، «آخرین بروزرسانی قیمت‌ها» به‌صورت خودکار با تاریخ شمسی ثبت می‌شود.

## امنیت

- هیچ ایمیل یا رمزی داخل کد یا صفحه نیست. رمزها با bcrypt هش می‌شوند.
- ثبت‌نام عمومی وجود ندارد. فقط حساب‌هایی که `is_admin` دارند می‌توانند وارد شوند، و این فیلد قابل Mass Assignment نیست.
- همه‌ی مسیرهای `/admin` پشت `auth` و `admin` هستند و برای کاربر عادی خطای 403 برمی‌گردانند.
- محدودیت تعداد تلاش برای ورود (۵ بار ناموفق ← قفل موقت)، برای ثبت‌نام و برای پنل.
- محافظت CSRF، بازسازی Session بعد از ورود و رمزنگاری Session.
- هدرهای امنیتی، از جمله CSP سخت‌گیرانه (اسکریپت، استایل و فونت فقط از خود سایت، بدون اسکریپت inline)، `X-Frame-Options`، `nosniff` و HSTS روی HTTPS.
- جلوگیری از XSS: خروجی Blade escape می‌شود، JSON صفحه با `JSON_HEX_*` درج می‌شود و JS همه‌ی داده‌ها را escape می‌کند.
- اعتبارسنجی آپلود: فقط jpg/png/webp و PDF با محدودیت حجم (SVG پذیرفته نمی‌شود). لینک‌ها فقط با `http` یا `https` پذیرفته می‌شوند. آیکون دسته فقط از لیست سفید انتخاب می‌شود.

## نصب روی لوکال‌هاست (MySQL)

پیش‌نیازها: PHP 8.2 یا بالاتر (با افزونه‌های `pdo_mysql`، `intl`، `mbstring`، `fileinfo` و `gd`)، Composer و MySQL 8 یا MariaDB 10.6 به بالا (مثلاً از طریق XAMPP یا Laragon).

```bash
composer install
cp .env.example .env
php artisan key:generate
```

یک دیتابیس با کدگذاری utf8mb4 بسازید:

```sql
CREATE DATABASE baloochi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

مقادیر `DB_DATABASE`، `DB_USERNAME` و `DB_PASSWORD` را در `.env` تنظیم کنید. سپس:

```bash
php artisan migrate --seed      # تنظیمات، ۱۵ دسته و ۷۳ محصول فایل اصلی
php artisan storage:link        # برای نمایش عکس، PDF و لوگوی آپلودی
php artisan admin:create        # ساخت اولین مدیر (ایمیل و رمز به‌صورت مخفی پرسیده می‌شود)
php artisan serve               # http://127.0.0.1:8000
```

ورود به پنل: دکمه‌ی ⚙ پایین صفحه یا آدرس `http://127.0.0.1:8000/login`. مدیرهای بعدی را از تب «مدیران» در پنل بسازید.

لوگوی پیش‌فرض یک لینک خارجی است. برای اینکه سایت کاملاً بدون منابع خارجی کار کند، لوگو را از «تنظیمات» آپلود کنید.

### در صورت انتشار روی سرور

`APP_ENV=production`، `APP_DEBUG=false`، `SESSION_SECURE_COOKIE=true` و استفاده از HTTPS.

## تست

```bash
php artisan test
```

تست‌ها به‌صورت پیش‌فرض روی SQLite در حافظه اجرا می‌شوند. برای اجرا روی MySQL، یک دیتابیس جداگانه بسازید و اجرا کنید:

```bash
DB_CONNECTION=mysql DB_DATABASE=baloochi_test php artisan test
```

هشدار: تست‌ها دیتابیس را پاک می‌کنند؛ هرگز از دیتابیس اصلی استفاده نکنید.
