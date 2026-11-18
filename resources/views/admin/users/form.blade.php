@extends('layouts.admin')

@php($editing = $user->exists)

@section('title', $editing ? 'ویرایش مدیر' : 'افزودن مدیر')

@section('content')
<div class="admin-section" style="max-width:480px;margin-inline:auto">
  <h2><i class="fas fa-user-shield"></i> {{ $editing ? 'ویرایش: '.$user->name : 'افزودن مدیر' }}</h2>

  <form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" autocomplete="off">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="form-group" style="margin-bottom:10px">
      <label for="name">نام <span class="req">*</span></label>
      <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="100" class="@error('name') is-invalid @enderror">
      @error('name')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group" style="margin-bottom:10px">
      <label for="email">ایمیل (برای ورود) <span class="req">*</span></label>
      <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required dir="ltr" class="@error('email') is-invalid @enderror">
      @error('email')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group" style="margin-bottom:10px">
      <label for="password">رمز عبور @unless ($editing)<span class="req">*</span>@endunless</label>
      <input type="password" id="password" name="password" autocomplete="new-password" dir="ltr" class="@error('password') is-invalid @enderror" @required(! $editing)>
      <div style="font-size:10px;color:var(--ink-muted)">حداقل ۸ کاراکتر، شامل حروف بزرگ و کوچک انگلیسی و عدد{{ $editing ? ' — برای تغییر ندادن رمز، خالی بگذارید' : '' }}</div>
      @error('password')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group" style="margin-bottom:10px">
      <label for="password_confirmation">تکرار رمز عبور</label>
      <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" dir="ltr">
    </div>

    <div class="form-actions">
      <a href="{{ route('admin.users.index') }}" class="btn-cancel">انصراف</a>
      <button type="submit" class="btn-save"><i class="fas fa-check"></i> ذخیره</button>
    </div>
  </form>
</div>
@endsection
