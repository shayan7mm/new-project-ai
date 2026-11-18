@extends('layouts.admin')

@section('title', 'تنظیمات')

@php
  $text = ['companyName', 'companyNameEn', 'manager', 'phone1', 'phone2', 'mobile', 'email', 'instagram', 'instagramUrl', 'workingHours', 'warranty', 'experience', 'lastUpdate', 'lastPriceUpdate', 'logoUrl'];
  $labels = \App\Services\SettingService::EDITABLE;
  $ltr = ['email', 'instagramUrl', 'logoUrl'];
@endphp

@section('content')
<div class="admin-section">
  <h2><i class="fas fa-gear"></i> تنظیمات</h2>
  <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="form-grid">
      @foreach ($text as $key)
        <div class="form-group">
          <label for="set-{{ $key }}">{{ $labels[$key] }} @if ($key === 'companyName')<span class="req">*</span>@endif</label>
          <input type="text" id="set-{{ $key }}" name="{{ $key }}" value="{{ old($key, $settings[$key]) }}" maxlength="{{ in_array($key, ['instagramUrl', 'logoUrl']) ? 2048 : 255 }}" @if (in_array($key, $ltr)) dir="ltr" @endif class="@error($key) is-invalid @enderror" @required($key === 'companyName')>
          @if ($key === 'lastPriceUpdate')<div style="font-size:10px;color:var(--ink-muted)">با هر ذخیره/حذف محصول خودکار به‌روز می‌شود.</div>@endif
          @error($key)<div class="field-error">{{ $message }}</div>@enderror
        </div>
      @endforeach
      <div class="form-group">
        <label for="logo">آپلود لوگو <small style="color:var(--ink-muted)">(اولویت با فایل آپلودی است)</small></label>
        <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/webp">
        @error('logo')<div class="field-error">{{ $message }}</div>@enderror
        @if ($logo)
          <div class="media-preview">
            <img src="{{ $logo }}" alt="" data-hide-on-error style="object-fit:contain;background:#fff">
            @if ($settings['logoPath'])
              <label class="check-row"><input type="checkbox" name="remove_logo" value="1"> حذف لوگوی آپلودی</label>
            @endif
          </div>
        @endif
      </div>
      <div class="form-group full">
        <label for="set-address">{{ $labels['address'] }}</label>
        <textarea id="set-address" name="address" maxlength="500">{{ old('address', $settings['address']) }}</textarea>
        @error('address')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group full">
        <label for="set-notice">{{ $labels['notice'] }}</label>
        <textarea id="set-notice" name="notice" style="min-height:60px" maxlength="2000">{{ old('notice', $settings['notice']) }}</textarea>
        @error('notice')<div class="field-error">{{ $message }}</div>@enderror
      </div>
    </div>
    <div style="margin-top:12px"><button type="submit" class="btn-save"><i class="fas fa-check"></i> ذخیره</button></div>
  </form>
</div>
@endsection
