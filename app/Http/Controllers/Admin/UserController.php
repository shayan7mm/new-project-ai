<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->limit(100, '')->toString();

        $users = User::query()
            ->when($search !== '', function ($q) use ($search) {
                $like = '%'.addcslashes($search, '\\%_').'%';
                $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('email', 'like', $like));
            })
            ->orderByDesc('is_admin')
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'نمی‌توانید نقش حساب خودتان را تغییر دهید.');
        }

        $user->is_admin = $request->boolean('is_admin');
        $user->save();

        return back()->with('status', $user->is_admin
            ? "«{$user->name}» به مدیر تبدیل شد."
            : "دسترسی مدیر از «{$user->name}» گرفته شد.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'نمی‌توانید حساب خودتان را حذف کنید.');
        }

        $user->delete();

        return back()->with('status', "کاربر «{$user->name}» حذف شد.");
    }
}
