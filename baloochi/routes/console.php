<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/*
| ساخت یا ارتقای یک کاربر به ادمین به‌صورت امن از ترمینال سرور:
|   php artisan admin:create
*/
Artisan::command('admin:create', function () {
    $email = mb_strtolower(trim((string) $this->ask('ایمیل ادمین')));
    $user = User::firstWhere('email', $email);

    $name = $user?->name ?? (string) $this->ask('نام', 'مدیر سایت');
    $password = (string) $this->secret('رمز عبور (حداقل ۸ کاراکتر، حروف بزرگ و کوچک و عدد)');
    $confirm = (string) $this->secret('تکرار رمز عبور');

    $validator = Validator::make(
        ['email' => $email, 'name' => $name, 'password' => $password, 'password_confirmation' => $confirm],
        [
            'email' => ['required', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ],
    );

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return 1;
    }

    $user ??= new User(['email' => $email]);
    $user->name = $name;
    $user->password = $password;
    $user->is_admin = true;
    $user->email_verified_at ??= now();
    $user->save();

    $this->info("ادمین «{$user->email}» با موفقیت ذخیره شد.");

    return 0;
})->purpose('ساخت ادمین جدید یا ارتقای کاربر موجود به ادمین');
