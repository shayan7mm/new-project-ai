<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * ساخت ادمین اولیه از متغیرهای ADMIN_EMAIL و ADMIN_PASSWORD در فایل .env
 * (هیچ رمزی داخل کد نوشته نمی‌شود). روش جایگزین: php artisan admin:create
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD تنظیم نشده؛ ادمین ساخته نشد. از دستور php artisan admin:create استفاده کنید.');

            return;
        }

        $user = User::firstOrNew(['email' => mb_strtolower($email)]);
        $user->name = env('ADMIN_NAME', 'مدیر سایت');
        $user->password = $password;
        $user->is_admin = true;
        $user->email_verified_at ??= now();
        $user->save();

        $this->command?->info("ادمین {$user->email} آماده است. اکنون ADMIN_PASSWORD را از .env پاک کنید.");
    }
}
