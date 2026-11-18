@extends('layouts.admin')

@php($editing = $product->exists)

@section('title', $editing ? 'ویرایش محصول' : 'افزودن محصول')

@section('content')
<div class="admin-section" style="max-width:820px;margin-inline:auto">
  <h2><i class="fas fa-box"></i> {{ $editing ? 'ویرایش: '.$product->name : 'افزودن محصول' }}</h2>

  <form method="POST" enctype="multipart/form-data"
        action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="form-section-header"><i class="fas fa-info-circle"></i> اطلاعات پایه</div>
    <div class="form-grid">
      <div class="form-group">
        <label for="code">کد <span class="req">*</span></label>
        <input type="text" id="code" name="code" value="{{ old('code', $product->code) }}" required maxlength="50" dir="ltr" class="@error('code') is-invalid @enderror">
        @error('code')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
        <label for="category_id">دسته <span class="req">*</span></label>
        <select id="category_id" name="category_id" required class="@error('category_id') is-invalid @enderror">
          <option value="">انتخاب کنید</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" @selected((int) old('category_id', $product->category_id) === $cat->id)>{{ $cat->name }} ({{ $cat->type->label() }})</option>
          @endforeach
        </select>
        @error('category_id')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group full">
        <label for="name">نام <span class="req">*</span></label>
        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="200" class="@error('name') is-invalid @enderror">
        @error('name')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group full">
        <label for="description">توضیح</label>
        <textarea id="description" name="description" maxlength="500">{{ old('description', $product->description) }}</textarea>
        @error('description')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
        <label for="brand">برند <span class="req">*</span></label>
        <input type="text" id="brand" name="brand" value="{{ old('brand', $product->brand) }}" required maxlength="100" class="@error('brand') is-invalid @enderror">
        @error('brand')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
        <label for="origin">کشور</label>
        <input type="text" id="origin" name="origin" value="{{ old('origin', $product->origin) }}" maxlength="100">
        @error('origin')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
        <label for="status">وضعیت</label>
        <select id="status" name="status">
          @foreach (\App\Enums\ProductStatus::options() as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $product->status?->value) === $value)>{{ $label }}</option>
          @endforeach
        </select>
        @error('status')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
        <label for="sort_order">ترتیب نمایش</label>
        <input type="number" id="sort_order" name="sort_order" min="0" value="{{ old('sort_order', $product->sort_order ?? 0) }}">
        @error('sort_order')<div class="field-error">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="form-section-header"><i class="fas fa-coins"></i> قیمت‌ها</div>
    <div class="price-form-grid">
      <div class="price-form-col factory">
        <div class="col-title"><i class="fas fa-industry"></i> کارخانه</div>
        @foreach (['price_factory_cash' => 'نقد', 'price_factory_installment' => 'اقساطی'] as $field => $label)
          <div class="form-group">
            <label for="{{ $field }}">{{ $label }}</label>
            <div class="price-input-wrap"><input type="number" id="{{ $field }}" name="{{ $field }}" min="0" value="{{ old($field, $product->{$field} ?? 0) }}" dir="ltr"><span class="suffix">ت</span></div>
            @error($field)<div class="field-error">{{ $message }}</div>@enderror
          </div>
        @endforeach
        <div class="form-group">
          <label for="factory_installment_terms">شرایط</label>
          <input type="text" id="factory_installment_terms" name="factory_installment_terms" value="{{ old('factory_installment_terms', $product->factory_installment_terms) }}" maxlength="255" placeholder="۳۰٪ پیش + ۳ چک">
          @error('factory_installment_terms')<div class="field-error">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="price-form-col baloochi">
        <div class="col-title"><i class="fas fa-store"></i> بلوچی ★</div>
        @foreach (['price_baloochi_cash' => 'نقد', 'price_baloochi_installment' => 'اقساطی'] as $field => $label)
          <div class="form-group">
            <label for="{{ $field }}">{{ $label }}</label>
            <div class="price-input-wrap"><input type="number" id="{{ $field }}" name="{{ $field }}" min="0" value="{{ old($field, $product->{$field} ?? 0) }}" dir="ltr"><span class="suffix">ت</span></div>
            @error($field)<div class="field-error">{{ $message }}</div>@enderror
          </div>
        @endforeach
        <div class="form-group">
          <label for="baloochi_installment_terms">شرایط</label>
          <input type="text" id="baloochi_installment_terms" name="baloochi_installment_terms" value="{{ old('baloochi_installment_terms', $product->baloochi_installment_terms) }}" maxlength="255" placeholder="۲۰٪ پیش + ۶ چک">
          @error('baloochi_installment_terms')<div class="field-error">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>

    <div class="form-section-header"><i class="fas fa-photo-film"></i> رسانه</div>
    <div class="form-grid">
      <div class="form-group">
        <label for="image">آپلود عکس <small style="color:var(--ink-muted)">(jpg, png, webp — حداکثر ۲ مگابایت)</small></label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
        @error('image')<div class="field-error">{{ $message }}</div>@enderror
        @if ($product->image_src)
          <div class="media-preview">
            <img src="{{ $product->image_src }}" alt="" data-hide-on-error>
            @if ($product->image_path)
              <label class="check-row"><input type="checkbox" name="remove_image" value="1"> حذف عکس آپلودی</label>
            @endif
          </div>
        @endif
      </div>
      <div class="form-group">
        <label for="image_url">یا لینک عکس (URL)</label>
        <input type="url" id="image_url" name="image_url" value="{{ old('image_url', $product->image_url) }}" placeholder="https://..." dir="ltr">
        @error('image_url')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
        <label for="pdf">آپلود کاتالوگ PDF <small style="color:var(--ink-muted)">(حداکثر ۱۰ مگابایت)</small></label>
        <input type="file" id="pdf" name="pdf" accept="application/pdf">
        @error('pdf')<div class="field-error">{{ $message }}</div>@enderror
        @if ($product->pdf_path)
          <div class="media-preview">
            <a href="{{ $product->pdf_src }}" target="_blank" rel="noopener"><i class="fas fa-file-pdf" style="color:var(--danger)"></i> مشاهده PDF</a>
            <label class="check-row"><input type="checkbox" name="remove_pdf" value="1"> حذف PDF آپلودی</label>
          </div>
        @endif
      </div>
      <div class="form-group">
        <label for="pdf_url">یا لینک PDF (URL)</label>
        <input type="url" id="pdf_url" name="pdf_url" value="{{ old('pdf_url', $product->pdf_url) }}" placeholder="https://..." dir="ltr">
        @error('pdf_url')<div class="field-error">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="form-section-header"><i class="fas fa-file-lines"></i> توضیحات</div>
    <div class="form-group full">
      <textarea id="extra_info" name="extra_info" style="min-height:100px" maxlength="5000">{{ old('extra_info', $product->extra_info) }}</textarea>
      @error('extra_info')<div class="field-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-actions">
      <a href="{{ route('admin.products.index') }}" class="btn-cancel">انصراف</a>
      <button type="submit" class="btn-save"><i class="fas fa-check"></i> ذخیره</button>
    </div>
  </form>
</div>
@endsection
