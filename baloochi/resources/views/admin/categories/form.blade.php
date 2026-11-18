@extends('layouts.admin')

@php($editing = $category->exists)

@section('title', $editing ? 'ویرایش دسته' : 'افزودن دسته')

@section('content')
<div class="admin-section" style="max-width:480px;margin-inline:auto">
  <h2><i class="fas fa-layer-group"></i> {{ $editing ? 'ویرایش: '.$category->name : 'افزودن دسته' }}</h2>

  <form method="POST" action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="form-group" style="margin-bottom:10px">
      <label for="name">نام <span class="req">*</span></label>
      <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required maxlength="150" class="@error('name') is-invalid @enderror">
      @error('name')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-grid">
      <div class="form-group" style="margin-bottom:10px">
        <label for="type">نوع <span class="req">*</span></label>
        <select id="type" name="type">
          @foreach (\App\Enums\CategoryType::options() as $value => $label)
            <option value="{{ $value }}" @selected(old('type', $category->type?->value) === $value)>{{ $label }}</option>
          @endforeach
        </select>
        @error('type')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group" style="margin-bottom:10px">
        <label for="is_digital">دیجیتال؟</label>
        <select id="is_digital" name="is_digital">
          <option value="0" @selected(! old('is_digital', $category->is_digital))>خیر</option>
          <option value="1" @selected(old('is_digital', $category->is_digital))>بله</option>
        </select>
      </div>
      <div class="form-group" style="margin-bottom:10px">
        <label for="cat-icon">آیکون <i id="cat-icon-preview" class="fas {{ old('icon', $category->icon) }}" style="color:var(--maroon-700)"></i></label>
        <select id="cat-icon" name="icon">
          @foreach (\App\Support\CategoryIcons::ALL as $value => $label)
            <option value="{{ $value }}" @selected(old('icon', $category->icon) === $value)>{{ $label }}</option>
          @endforeach
        </select>
        @error('icon')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group" style="margin-bottom:10px">
        <label for="sort_order">ترتیب نمایش</label>
        <input type="number" id="sort_order" name="sort_order" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
        @error('sort_order')<div class="field-error">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="form-actions">
      <a href="{{ route('admin.categories.index') }}" class="btn-cancel">انصراف</a>
      <button type="submit" class="btn-save"><i class="fas fa-check"></i> ذخیره</button>
    </div>
  </form>
</div>
@endsection
