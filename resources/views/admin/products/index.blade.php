@extends('layouts.admin')
@use('App\Support\Fa')

@section('title', 'محصولات')

@section('content')
<div class="admin-section">
  <h2><i class="fas fa-box"></i> محصولات <small style="font-size:11px;color:var(--ink-muted);font-weight:500">({{ Fa::digits($products->total()) }} مورد)</small></h2>

  <form method="GET" action="{{ route('admin.products.index') }}" class="admin-toolbar admin-filter">
    <div class="admin-search"><input type="text" name="q" value="{{ $search }}" placeholder="جستجوی نام، کد یا برند..." maxlength="100"><i class="fas fa-search"></i></div>
    <select name="category">
      <option value="">همه دسته‌ها</option>
      @foreach ($categories as $cat)
        <option value="{{ $cat->id }}" @selected($categoryId === $cat->id)>{{ $cat->name }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn-search"><i class="fas fa-filter"></i> فیلتر</button>
    <a href="{{ route('admin.products.create') }}" class="btn-add"><i class="fas fa-plus"></i> افزودن</a>
  </form>

  <div style="overflow-x:auto">
    <table class="admin-table">
      <thead><tr><th>کد</th><th>نام</th><th>دسته</th><th>برند</th><th>نقد بلوچی</th><th>وضعیت</th><th>اقدامات</th></tr></thead>
      <tbody>
        @forelse ($products as $p)
          <tr>
            <td><span class="p-code">{{ Fa::digits($p->code) }}</span></td>
            <td style="font-weight:600">
              {{ $p->name }}
              @if ($p->image_src)<i class="fas fa-image" style="color:var(--success);font-size:8px" title="عکس دارد"></i>@endif
              @if ($p->pdf_src)<i class="fas fa-file-pdf" style="color:var(--danger);font-size:8px" title="PDF دارد"></i>@endif
            </td>
            <td>{{ $p->category?->name ?? '—' }}</td>
            <td>{{ Fa::digits($p->brand) }}</td>
            <td style="font-weight:600;color:var(--maroon-700)">{{ $p->display_price > 0 ? Fa::price($p->display_price) : 'استعلام' }}</td>
            <td><span style="color:{{ $p->status->color() }};font-weight:600;font-size:9px">{{ $p->status->label() }}</span></td>
            <td>
              <div class="actions-cell">
                <a class="btn-edit" href="{{ route('admin.products.edit', $p) }}" title="ویرایش"><i class="fas fa-pen"></i></a>
                <form method="POST" action="{{ route('admin.products.destroy', $p) }}" class="inline-form" data-confirm="محصول «{{ $p->name }}» حذف شود؟">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-del" title="حذف"><i class="fas fa-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="text-align:center;padding:18px;color:var(--ink-muted)">خالی</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $products->links('partials.pagination') }}
</div>
@endsection
