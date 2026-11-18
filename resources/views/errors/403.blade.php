<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
@include('partials.head')
<title>دسترسی غیرمجاز</title>
</head>
<body>
<div class="auth-page">
  <div class="login-card">
    <div class="head"><h3><i class="fas fa-lock"></i> دسترسی غیرمجاز</h3><p>۴۰۳</p></div>
    <div class="body" style="text-align:center;font-size:13px;color:var(--ink-soft)">
      {{ $exception->getMessage() ?: 'شما اجازه دسترسی به این صفحه را ندارید.' }}
    </div>
    <div class="foot"><a href="{{ route('home') }}" style="color:var(--maroon-700);font-weight:600;font-size:12px">بازگشت به سایت</a></div>
  </div>
</div>
</body>
</html>
