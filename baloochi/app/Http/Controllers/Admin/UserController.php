<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * مدیریت حساب‌های ادمین. ثبت‌نام عمومی وجود ندارد؛
 * هر حسابی که این‌جا ساخته شود، ادمین است.
 */
class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->where('is_admin', true)->orderBy('id')->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = new User($request->safe()->only(['name', 'email', 'password']));
        $user->is_admin = true;
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('admin.users.index')->with('status', "ادمین «{$user->name}» ساخته شد.");
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email']);
        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('status', "اطلاعات «{$user->name}» ذخیره شد.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'نمی‌توانید حساب خودتان را حذف کنید.');
        }

        $user->delete();

        return back()->with('status', "حساب «{$user->name}» حذف شد.");
    }
}
