@extends('layouts.auth')

@section('title', 'ثبت‌نام')

@section('content')
  @if ($errors->any())
    <div class="login-error"><i class="fas fa-circle-exclamation"></i> لطفاً خطاهای فرم را بررسی کنید.</div>
  @endif
  <form method="POST" action="{{ route('register.store') }}" novalidate>
    @csrf
    <div class="field">
      <label for="name">نام و نام خانوادگی</label>
      <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus maxlength="100" autocomplete="name" class="@error('name') is-invalid @enderror">
      @error('name')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="field">
      <label for="email">ایمیل</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" dir="ltr" class="@error('email') is-invalid @enderror">
      @error('email')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="field">
      <label for="password">رمز عبور</label>
      <input type="password" id="password" name="password" required autocomplete="new-password" dir="ltr" class="@error('password') is-invalid @enderror">
      <div class="hint">حداقل ۸ کاراکتر، شامل حروف بزرگ و کوچک انگلیسی و عدد</div>
      @error('password')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="field">
      <label for="password_confirmation">تکرار رمز عبور</label>
      <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" dir="ltr">
    </div>
    <button type="submit" class="btn-login"><i class="fas fa-user-plus"></i> ثبت‌نام</button>
  </form>
@endsection

@section('foot')
  <div><span class="muted">حساب دارید؟</span> <a href="{{ route('login') }}">ورود</a></div>
  <div style="margin-top:4px"><a href="{{ route('home') }}" class="muted">بازگشت به سایت</a></div>
@endsection
