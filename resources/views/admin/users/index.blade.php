@extends('layouts.admin')
@use('App\Support\Fa')

@section('title', 'کاربران')

@section('content')
<div class="admin-section">
  <h2><i class="fas fa-users"></i> کاربران <small style="font-size:11px;color:var(--ink-muted);font-weight:500">({{ Fa::digits($users->total()) }} نفر)</small></h2>

  <form method="GET" action="{{ route('admin.users.index') }}" class="admin-toolbar">
    <div class="admin-search"><input type="text" name="q" value="{{ $search }}" placeholder="جستجوی نام یا ایمیل..." maxlength="100"><i class="fas fa-search"></i></div>
    <button type="submit" class="btn-search"><i class="fas fa-search"></i> جستجو</button>
  </form>

  <div style="overflow-x:auto">
    <table class="admin-table">
      <thead><tr><th>نام</th><th>ایمیل</th><th>نقش</th><th>تاریخ عضویت</th><th>اقدامات</th></tr></thead>
      <tbody>
        @forelse ($users as $u)
          <tr>
            <td style="font-weight:600">{{ $u->name }} @if ($u->is(auth()->user()))<small style="color:var(--ink-muted)">(شما)</small>@endif</td>
            <td dir="ltr" style="text-align:right">{{ $u->email }}</td>
            <td><span class="role-tag {{ $u->is_admin ? 'admin' : 'user' }}">{{ $u->is_admin ? 'مدیر' : 'کاربر' }}</span></td>
            <td>{{ Fa::digits(\App\Support\Jalali::dateTime($u->created_at)) }}</td>
            <td>
              @unless ($u->is(auth()->user()))
                <div class="actions-cell">
                  <form method="POST" action="{{ route('admin.users.role', $u) }}" class="inline-form"
                        data-confirm="{{ $u->is_admin ? 'دسترسی مدیر از «'.$u->name.'» گرفته شود؟' : '«'.$u->name.'» مدیر شود؟ (دسترسی کامل به پنل)' }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="is_admin" value="{{ $u->is_admin ? 0 : 1 }}">
                    <button type="submit" class="btn-edit">{{ $u->is_admin ? 'لغو مدیر' : 'مدیر کن' }}</button>
                  </form>
                  <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline-form" data-confirm="کاربر «{{ $u->name }}» حذف شود؟">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del" title="حذف"><i class="fas fa-trash"></i></button>
                  </form>
                </div>
              @endunless
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;padding:18px;color:var(--ink-muted)">خالی</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $users->links('partials.pagination') }}
</div>
@endsection
