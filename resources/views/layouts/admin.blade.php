<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
@include('partials.head')
<meta name="robots" content="noindex, nofollow">
<title>@yield('title', 'پنل مدیریت') | {{ $siteSettings['companyName'] }}</title>
</head>
<body>
<div class="admin-view">
  <div class="admin-topbar"><div class="container">
    <div class="brand">
      @if ($siteLogo)
        <div class="admin-logo-box"><img src="{{ $siteLogo }}" alt="" data-hide-on-error></div>
      @endif
      <div><strong>پنل مدیریت</strong><br><small>{{ $siteSettings['companyName'] }}</small></div>
    </div>
    <div class="actions">
      <a class="admin-btn admin-btn-view" href="{{ route('home') }}" target="_blank" rel="noopener"><i class="fas fa-eye"></i> سایت</a>
      <a class="admin-btn admin-btn-export" href="{{ route('admin.export') }}"><i class="fas fa-download"></i> خروجی</a>
      <span class="admin-btn admin-btn-reset"><i class="fas fa-user-shield"></i> {{ auth()->user()->name }}</span>
      <form method="POST" action="{{ route('logout') }}" class="inline-form">
        @csrf
        <button type="submit" class="admin-btn admin-btn-logout"><i class="fas fa-right-from-bracket"></i> خروج</button>
      </form>
    </div>
  </div></div>

  <div class="admin-tabs"><div class="container">
    <a href="{{ route('admin.products.index') }}" @class(['active' => request()->routeIs('admin.products.*')])><i class="fas fa-box"></i> محصولات</a>
    <a href="{{ route('admin.categories.index') }}" @class(['active' => request()->routeIs('admin.categories.*')])><i class="fas fa-layer-group"></i> دسته‌ها</a>
    <a href="{{ route('admin.settings.edit') }}" @class(['active' => request()->routeIs('admin.settings.*')])><i class="fas fa-gear"></i> تنظیمات</a>
    <a href="{{ route('admin.users.index') }}" @class(['active' => request()->routeIs('admin.users.*')])><i class="fas fa-users"></i> کاربران</a>
    <a href="{{ route('admin.help') }}" @class(['active' => request()->routeIs('admin.help')])><i class="fas fa-circle-question"></i> راهنما</a>
  </div></div>

  <div class="container admin-content">
    @include('partials.flash')
    @yield('content')
  </div>
</div>
<script src="{{ asset('js/admin.js') }}?v={{ filemtime(public_path('js/admin.js')) }}"></script>
</body>
</html>
