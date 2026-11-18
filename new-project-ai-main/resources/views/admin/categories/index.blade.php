@extends('layouts.admin')
@use('App\Support\Fa')

@section('title', 'دسته‌ها')

@section('content')
<div class="admin-section">
  <h2><i class="fas fa-layer-group"></i> دسته‌ها</h2>

  <form method="GET" action="{{ route('admin.categories.index') }}" class="admin-toolbar">
    <div class="admin-search"><input type="text" name="q" value="{{ $search }}" placeholder="جستجو..." maxlength="100"><i class="fas fa-search"></i></div>
    <button type="submit" class="btn-search"><i class="fas fa-search"></i> جستجو</button>
    <a href="{{ route('admin.categories.create') }}" class="btn-add"><i class="fas fa-plus"></i> افزودن</a>
  </form>

  <div class="cat-grid">
    @forelse ($categories as $c)
      <div class="cat-card">
        <div class="info">
          <div class="icon"><i class="fas {{ $c->icon }}"></i></div>
          <div>
            <div class="name">
              {{ $c->name }}
              @if ($c->is_digital)
                <span style="font-size:7px;background:var(--gold);color:var(--dark);padding:1px 3px;border-radius:2px;font-weight:700">دیجیتال</span>
              @endif
            </div>
            <div class="count">{{ Fa::digits($c->products_count) }} محصول <span class="type-tag {{ $c->type->value }}">{{ $c->type === \App\Enums\CategoryType::Equipment ? 'تجهیزات' : 'مصرفی' }}</span></div>
          </div>
        </div>
        <div class="actions-cell">
          <a class="btn-edit" href="{{ route('admin.categories.edit', $c) }}" title="ویرایش"><i class="fas fa-pen"></i></a>
          <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" class="inline-form" data-confirm="دسته «{{ $c->name }}» حذف شود؟">
            @csrf @method('DELETE')
            <button type="submit" class="btn-del" title="حذف" @disabled($c->products_count > 0)><i class="fas fa-trash"></i></button>
          </form>
        </div>
      </div>
    @empty
      <p style="text-align:center;padding:18px;color:var(--ink-muted)">خالی</p>
    @endforelse
  </div>
  <p style="font-size:10.5px;color:var(--ink-muted);margin-top:10px"><i class="fas fa-info-circle"></i> دسته‌ای که محصول دارد قابل حذف نیست؛ ابتدا محصولات آن را حذف یا منتقل کنید.</p>
</div>
@endsection
