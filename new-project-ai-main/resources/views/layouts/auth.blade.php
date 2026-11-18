<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
@include('partials.head')
<title>@yield('title') | {{ $siteSettings['companyName'] ?? config('app.name') }}</title>
</head>
<body>
<div class="auth-page">
  <div class="login-card">
    <div class="head">
      @if (!empty($siteLogo))
        <div class="login-logo-box"><img src="{{ $siteLogo }}" alt="" data-hide-on-error></div>
      @endif
      <h3>@yield('title')</h3>
      <p>{{ $siteSettings['companyName'] ?? '' }}</p>
    </div>
    <div class="body">
      @yield('content')
    </div>
    <div class="foot">@yield('foot')</div>
  </div>
</div>
<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
