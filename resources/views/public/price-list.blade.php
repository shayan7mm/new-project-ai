<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
@include('partials.head')
<title>لیست قیمت | {{ $data['settings']['companyName'] }}</title>
</head>
<body>

{{-- داده‌ی صفحه: JSON_HEX_* مانع خروج از تگ script و تزریق HTML می‌شود --}}
<script id="site-data" type="application/json">{!! json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

<div id="publicView">
  <div class="topbar"><div class="container"><div class="info"><span><i class="fas fa-phone"></i> <span data-s="phone1"></span></span><span><i class="fas fa-mobile-screen"></i> <span data-s="mobile"></span></span></div><div class="info"><a id="igTop" href="https://www.instagram.com/baloochi_dental" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i> <span data-s="instagram"></span></a><span><i class="fas fa-clock"></i> <span data-s="workingHours"></span></span></div></div></div>

  <header><div class="container" style="position:relative">
    <div class="logo"><div class="header-logo-box"><img data-logo class="logo-img" alt=""><div class="logo-mark" style="display:none"><i class="fas fa-tooth"></i></div></div><div class="logo-text"><span class="en" data-s="companyNameEn"></span><span class="fa" data-s="companyName"></span></div></div>
    <nav><ul id="mainNav"><li><a href="{{ route('home') }}">خانه</a></li><li><a href="{{ route('home') }}" class="active">لیست قیمت</a></li><li><a href="#productList">محصولات</a></li><li><a href="#productList">دیجیتال</a></li><li><a href="#services">خدمات</a></li><li><a href="#contact">تماس</a></li></ul></nav>
    <div class="user-menu">
      @auth
        @if (auth()->user()->isAdmin())
          <a class="user-btn" href="{{ route('admin.products.index') }}"><i class="fas fa-gear"></i> <span>پنل مدیریت</span></a>
        @else
          <span class="user-btn"><i class="fas fa-user"></i> <span>{{ auth()->user()->name }}</span></span>
        @endif
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="user-btn" title="خروج"><i class="fas fa-right-from-bracket"></i></button></form>
      @else
        <a class="user-btn" href="{{ route('login') }}"><i class="fas fa-right-to-bracket"></i> <span>ورود</span></a>
        <a class="user-btn" href="{{ route('register') }}"><i class="fas fa-user-plus"></i> <span>ثبت‌نام</span></a>
      @endauth
      <button class="nav-toggle" type="button" data-action="nav-toggle" aria-label="منو"><i class="fas fa-bars"></i></button>
    </div>
  </div></header>

  <section class="hero"><div class="hero-logo-wrap"><div class="hero-logo-container"><img data-logo data-fallback="heroLf" class="hero-logo" alt="Baloochi"><div id="heroLf" class="hero-logo-fallback"><i class="fas fa-tooth"></i></div></div></div><div class="container"><div class="slogan-en">From Dream To Reality</div><div class="slogan-fa">از رؤیا تا واقعیت</div><div class="badge"><i class="fas fa-circle-check"></i> <span data-s="lastUpdate"></span> | <span data-s="experience"></span></div><h1>لیست قیمت <span class="brand" data-s="companyName"></span></h1><p>تأمین‌کننده تجهیزات و راهکارهای دندانپزشکی در شیراز — فروش نقدی و اقساطی، مشاوره، نصب، آموزش و خدمات پس از فروش.</p><div class="hero-meta"><div class="item"><i class="fas fa-shield-halved"></i> <span data-s="warranty"></span></div><div class="item"><i class="fas fa-credit-card"></i> نقدی و اقساطی</div><div class="item"><i class="fas fa-truck-fast"></i> ارسال به سراسر کشور</div><div class="item"><i class="fas fa-headset"></i> پشتیبانی</div></div></div></section>

  <div class="services-strip" id="services"><div class="container"><div class="svc-item"><i class="fas fa-comments"></i><div class="title">مشاوره</div></div><div class="svc-item"><i class="fas fa-credit-card"></i><div class="title">نقدی و اقساطی</div></div><div class="svc-item"><i class="fas fa-screwdriver-wrench"></i><div class="title">نصب</div></div><div class="svc-item"><i class="fas fa-chalkboard-user"></i><div class="title">آموزش</div></div><div class="svc-item"><i class="fas fa-headset"></i><div class="title">پس از فروش</div></div><div class="svc-item"><i class="fas fa-wrench"></i><div class="title">تعمیرات</div></div></div></div>

  <div class="nationwide-banner"><div class="container"><i class="fas fa-truck-fast"></i> ارائه خدمات به سراسر کشور — ارسال به تمامی استان‌ها و شهرستان‌ها <span class="pulse"></span></div></div>
  <div class="contact-strip"><div class="container"><i class="fas fa-headset"></i> مشاوره رایگان: <strong data-s="mobile"></strong><span class="sep">|</span> <span data-s="manager"></span></div></div>

  <div class="container"><div class="main-content">
    @include('partials.flash')
    <div class="content-head"><h2><i class="fas fa-list-check"></i> جدول قیمت‌ها</h2></div>
    <div class="section-toggle"><button type="button" class="toggle-btn active" id="btnEquipment" data-action="set-section" data-section="equipment"><i class="fas fa-tooth"></i> تجهیزات دندانپزشکی</button><button type="button" class="toggle-btn" id="btnConsumable" data-action="set-section" data-section="consumable"><i class="fas fa-box"></i> مواد مصرفی</button></div>

    <div class="filter-toolbar">
      <div class="dropdown" id="ddCat"><button class="dropdown-btn" type="button" data-action="toggle-dropdown" data-target="ddCat"><i class="fas fa-layer-group" style="font-size:10px;color:var(--gold)"></i><span>دسته‌بندی</span><span class="btn-count" id="catBtnCount">همه</span><i class="fas fa-chevron-down"></i></button><div class="dropdown-panel hidden" id="ddCatPanel"><div class="dropdown-search"><input type="text" placeholder="جستجوی دسته..." data-dd-filter="ddCat"></div><div class="dropdown-items" id="ddCatItems"></div><div class="dropdown-actions"><button type="button" data-action="select-all-cats">انتخاب همه</button><button type="button" data-action="clear-cats">پاک کردن</button></div></div></div>
      <div class="dropdown" id="ddBrand"><button class="dropdown-btn" type="button" data-action="toggle-dropdown" data-target="ddBrand"><i class="fas fa-tag" style="font-size:10px;color:var(--gold)"></i><span>برند</span><span class="btn-count" id="brandBtnCount">همه</span><i class="fas fa-chevron-down"></i></button><div class="dropdown-panel hidden" id="ddBrandPanel"><div class="dropdown-search"><input type="text" placeholder="جستجوی برند..." data-dd-filter="ddBrand"></div><div class="dropdown-items" id="ddBrandItems"></div><div class="dropdown-actions"><button type="button" data-action="select-all-brands">انتخاب همه</button><button type="button" data-action="clear-brands">پاک کردن</button></div></div></div>
      <div class="search-box"><input type="text" id="searchInput" placeholder="جستجوی نام، کد یا برند..." maxlength="100"><i class="fas fa-search"></i></div>
      <select class="sort-select" id="sortSelect"><option value="default">مرتب‌سازی</option><option value="price-asc">قیمت ↑</option><option value="price-desc">قیمت ↓</option><option value="name">نام</option></select>
    </div>

    <div id="activeFilters" class="active-filters hidden"></div>
    <div class="last-price-update"><span class="live-dot"></span><i class="fas fa-clock-rotate-left"></i> آخرین بروزرسانی قیمت‌ها: <strong id="lastPriceUpdateDisplay">—</strong><span class="sep-dot"></span><span style="font-size:11px;color:var(--ink-muted)">توسط <span data-s="manager"></span></span></div>
    <div class="toolbar-meta"><div class="result-count">نتایج: <strong id="resultCount">۰</strong> محصول</div></div>
    <div class="installment-note"><i class="fas fa-handshake"></i><span>شرایط پرداخت اقساطی بسته به میزان خرید و بودجه مشتری قابل مذاکره است</span><span class="neg-badge">قابل مذاکره</span></div>
    <div class="notice"><i class="fas fa-exclamation-circle"></i><div data-s="notice"></div></div>
    <div id="productList"></div>
  </div></div>

  <footer id="contact"><div class="container"><div class="grid">
    <div><div class="brand-line"><div class="footer-logo-box"><img data-logo class="logo-img" alt=""><div class="logo-mark" style="display:none"><i class="fas fa-tooth"></i></div></div><div><strong data-s="companyName"></strong></div></div><div class="slogan-foot">From Dream To Reality</div><p>تأمین‌کننده تجهیزات و مواد مصرفی دندانپزشکی در شیراز، با بیش از ۲۰ سال سابقه.</p></div>
    <div><h4>دسترسی</h4><ul><li><a href="{{ route('home') }}">خانه</a></li><li><a href="#productList">لیست قیمت</a></li><li><a href="#productList">تجهیزات</a></li><li><a href="#productList">مواد مصرفی</a></li><li><a href="#services">خدمات</a></li></ul></div>
    <div><h4>خدمات</h4><ul><li><a href="#services">مشاوره</a></li><li><a href="#services">نصب و راه‌اندازی</a></li><li><a href="#services">آموزش</a></li><li><a href="#services">تعمیرات</a></li><li><a href="#services">ارسال سراسری</a></li></ul></div>
    <div><h4>تماس</h4><div class="contact-item"><i class="fas fa-map-marker-alt"></i><span data-s="address"></span></div><div class="contact-item"><i class="fas fa-phone"></i><span data-s="phone1"></span></div><div class="contact-item"><i class="fas fa-mobile-screen"></i><span data-s="mobile"></span></div><div class="contact-item"><a id="igFooter" href="https://www.instagram.com/baloochi_dental" target="_blank" rel="noopener noreferrer" style="color:inherit"><i class="fab fa-instagram"></i><span data-s="instagram"></span></a></div><div class="manager-box"><div style="font-size:9px;color:#D4BFC6">مدیریت:</div><div style="font-size:11px;color:var(--gold-soft);font-weight:600" data-s="manager"></div></div></div>
  </div><div class="bottom">© {{ \App\Support\Fa::digits(\App\Support\Jalali::year(now())) }} <span data-s="companyName"></span> | شیراز</div></div></footer>
</div>

<div id="detailModal" class="modal-overlay hidden"><div class="modal detail-modal"><div class="modal-head"><h3><i class="fas fa-circle-info"></i> اطلاعات محصول</h3><button type="button" class="close" data-action="close-detail"><i class="fas fa-times"></i></button></div><div class="modal-body" id="detailBody"></div></div></div>

@if (auth()->user()?->isAdmin())
<div class="admin-access"><a href="{{ route('admin.products.index') }}" title="پنل مدیریت"><button type="button" tabindex="-1"><i class="fas fa-gear"></i></button></a></div>
@endif

<script src="{{ asset('js/price-list.js') }}?v={{ filemtime(public_path('js/price-list.js')) }}"></script>
</body>
</html>
