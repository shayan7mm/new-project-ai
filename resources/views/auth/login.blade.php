@extends('layouts.auth')

@section('title', 'ورود مدیر')

@section('content')
  @if ($errors->any())
    <div class="login-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first() }}</div>
  @endif
  <form method="POST" action="{{ route('login.store') }}" novalidate>
    @csrf
    <div class="field">
      <label for="email">ایمیل</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" dir="ltr" class="@error('email') is-invalid @enderror">
    </div>
    <div class="field">
      <label for="password">رمز عبور</label>
      <input type="password" id="password" name="password" required autocomplete="current-password" dir="ltr">
    </div>
    <label class="remember"><input type="checkbox" name="remember" value="1"> مرا به خاطر بسپار</label>
    <button type="submit" class="btn-login"><i class="fas fa-right-to-bracket"></i> ورود</button>
  </form>
@endsection

@section('foot')
  <div><a href="{{ route('home') }}" class="muted">بازگشت به سایت</a></div>
@endsection
