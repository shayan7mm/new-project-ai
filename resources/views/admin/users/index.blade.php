@extends('layouts.admin')
@use('App\Support\Fa')

@section('title', 'مدیران')

@section('content')
<div class="admin-section">
  <h2><i class="fas fa-user-shield"></i> مدیران سایت</h2>

  <div class="admin-toolbar">
    <span style="flex:1;font-size:11.5px;color:var(--ink-muted)">فقط این حساب‌ها می‌توانند وارد پنل شوند و محصولات را ویرایش کنند.</span>
    <a href="{{ route('admin.users.create') }}" class="btn-add"><i class="fas fa-plus"></i> افزودن مدیر</a>
  </div>

  <div style="overflow-x:auto">
    <table class="admin-table">
      <thead><tr><th>نام</th><th>ایمیل</th><th>تاریخ ساخت</th><th>اقدامات</th></tr></thead>
      <tbody>
        @foreach ($users as $u)
          <tr>
            <td style="font-weight:600">{{ $u->name }} @if ($u->is(auth()->user()))<small style="color:var(--ink-muted)">(شما)</small>@endif</td>
            <td dir="ltr" style="text-align:right">{{ $u->email }}</td>
            <td>{{ \App\Support\Jalali::dateTime($u->created_at) }}</td>
            <td>
              <div class="actions-cell">
                <a class="btn-edit" href="{{ route('admin.users.edit', $u) }}" title="ویرایش / تغییر رمز"><i class="fas fa-pen"></i></a>
                @unless ($u->is(auth()->user()))
                  <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline-form" data-confirm="حساب «{{ $u->name }}» حذف شود؟">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del" title="حذف"><i class="fas fa-trash"></i></button>
                  </form>
                @endunless
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $users->links('partials.pagination') }}
</div>
@endsection
