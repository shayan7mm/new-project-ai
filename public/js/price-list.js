/*
 * صفحه عمومی لیست قیمت — فقط نمایش، فیلتر و جستجو.
 * داده از سرور (کنترلر لاراول) در <script id="site-data"> قرار می‌گیرد.
 * هیچ اطلاعات ورود یا عملیات ادمین در این فایل وجود ندارد.
 * تمام داده‌ها قبل از درج در HTML با esc() امن می‌شوند (جلوگیری از XSS).
 */
(function () {
  'use strict';

  let DATA = { settings: {}, categories: [], products: [] };
  let activeSection = 'equipment', activeCats = [], activeBrands = [], searchTerm = '', sortBy = 'default';

  const $ = (id) => document.getElementById(id);

  function esc(v) {
    return String(v == null ? '' : v)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }
  function safeUrl(u) {
    u = String(u || '').trim();
    return /^(https?:\/\/|\/)/i.test(u) ? u : '';
  }
  function toFa(n) { return String(n == null ? '' : n).replace(/\d/g, (d) => '۰۱۲۳۴۵۶۷۸۹'[d]); }
  function formatPrice(n) { return toFa(Number(n || 0).toLocaleString('en-US')); }
  const faEsc = (v) => esc(toFa(v));

  function loadData() {
    try { DATA = JSON.parse($('site-data').textContent); } catch (e) { /* داده نامعتبر */ }
  }

  /* ---------- تنظیمات ---------- */
  function applySettings() {
    const s = DATA.settings || {};
    document.querySelectorAll('[data-s]').forEach((el) => {
      const k = el.getAttribute('data-s');
      if (s[k] !== undefined) el.textContent = s[k];
    });
    const logo = safeUrl(s.logo);
    document.querySelectorAll('img[data-logo]').forEach((img) => {
      if (logo) img.src = logo; else showLogoFallback(img);
    });
    const ig = safeUrl(s.instagramUrl);
    ['igTop', 'igFooter'].forEach((id) => { const el = $(id); if (el && ig) el.href = ig; });
    const lpu = $('lastPriceUpdateDisplay');
    if (lpu) lpu.textContent = s.lastPriceUpdate || '—';
  }
  function showLogoFallback(img) {
    img.style.display = 'none';
    const fb = img.dataset.fallback ? $(img.dataset.fallback) : img.parentElement.querySelector('.logo-mark');
    if (fb) fb.style.display = 'flex';
  }

  /* ---------- بخش و دسته ---------- */
  function setSection(s) {
    activeSection = s; activeCats = []; activeBrands = [];
    $('btnEquipment').classList.toggle('active', s === 'equipment');
    $('btnConsumable').classList.toggle('active', s === 'consumable');
    renderAll();
  }
  const getSectionCats = () => DATA.categories.filter((c) => (c.type || 'equipment') === activeSection);
  function getSectionProducts() {
    const ids = getSectionCats().map((c) => c.id);
    return DATA.products.filter((p) => ids.includes(p.category));
  }

  /* ---------- دراپ‌داون‌ها ---------- */
  function toggleDropdown(id) {
    const dd = $(id), p = $(id + 'Panel');
    const open = !p.classList.contains('hidden');
    closeAllDropdowns();
    if (!open) { p.classList.remove('hidden'); dd.classList.add('open'); }
  }
  function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-panel').forEach((p) => p.classList.add('hidden'));
    document.querySelectorAll('.dropdown').forEach((d) => d.classList.remove('open'));
  }
  function filterDDItems(id, q) {
    q = q.toLowerCase();
    document.querySelectorAll('#' + id + 'Items .dd-item').forEach((it) => {
      it.style.display = it.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  }

  function renderCatDropdown() {
    const cats = getSectionCats(), counts = {};
    cats.forEach((c) => (counts[c.id] = 0));
    getSectionProducts().forEach((p) => { if (counts[p.category] !== undefined) counts[p.category]++; });
    $('ddCatItems').innerHTML = cats.map((c) => {
      const checked = activeCats.includes(c.id);
      const dig = c.digital ? '<span class="digital-badge">دیجیتال</span>' : '';
      return `<label class="dd-item ${checked ? 'checked' : ''}" data-action="toggle-cat" data-id="${esc(c.id)}"><input type="checkbox" ${checked ? 'checked' : ''} tabindex="-1"><i class="fas ${esc(c.icon)}" style="font-size:10px;color:var(--gold);width:13px"></i><span class="dd-label">${esc(c.name)}</span> ${dig}<span class="dd-count">${toFa(counts[c.id] || 0)}</span></label>`;
    }).join('');
    updateBtn('catBtnCount', 'ddCat', activeCats.length);
  }
  function toggleCat(id) {
    const i = activeCats.indexOf(id);
    if (i >= 0) activeCats.splice(i, 1); else activeCats.push(id);
    updateAvailableBrands(); renderAll();
  }
  function getAvailBrandsMap() {
    let prods = getSectionProducts();
    if (activeCats.length > 0) prods = prods.filter((p) => activeCats.includes(p.category));
    const m = {};
    prods.forEach((p) => { const b = p.brand || 'سایر'; m[b] = (m[b] || 0) + 1; });
    return m;
  }
  function updateAvailableBrands() {
    const a = Object.keys(getAvailBrandsMap());
    activeBrands = activeBrands.filter((b) => a.includes(b));
  }
  function renderBrandDropdown() {
    const brands = getAvailBrandsMap();
    const sorted = Object.keys(brands).sort((a, b) => a.localeCompare(b, 'fa'));
    $('ddBrandItems').innerHTML = sorted.length === 0
      ? '<div style="padding:12px;text-align:center;color:var(--ink-muted);font-size:11px">برندی موجود نیست</div>'
      : sorted.map((b) => {
        const checked = activeBrands.includes(b);
        return `<label class="dd-item ${checked ? 'checked' : ''}" data-action="toggle-brand" data-brand="${esc(b)}"><input type="checkbox" ${checked ? 'checked' : ''} tabindex="-1"><i class="fas fa-tag" style="font-size:9px;color:var(--gold);width:13px"></i><span class="dd-label">${faEsc(b)}</span><span class="dd-count">${toFa(brands[b])}</span></label>`;
      }).join('');
    updateBtn('brandBtnCount', 'ddBrand', activeBrands.length);
  }
  function toggleBrand(n) {
    const i = activeBrands.indexOf(n);
    if (i >= 0) activeBrands.splice(i, 1); else activeBrands.push(n);
    renderBrandDropdown(); renderActiveFilters(); renderPublicProducts();
  }
  function updateBtn(countId, ddId, n) {
    const btn = $(countId), dd = $(ddId).querySelector('.dropdown-btn');
    btn.textContent = n === 0 ? 'همه' : toFa(n) + ' انتخابی';
    dd.classList.toggle('has-selection', n > 0);
  }

  /* ---------- فیلترهای فعال ---------- */
  function renderActiveFilters() {
    const wrap = $('activeFilters'), chips = [];
    activeCats.forEach((id) => {
      const c = DATA.categories.find((x) => x.id === id);
      if (c) chips.push({ type: 'دسته', value: esc(c.name), attrs: `data-action="toggle-cat" data-id="${esc(id)}"` });
    });
    activeBrands.forEach((b) => chips.push({ type: 'برند', value: faEsc(b), attrs: `data-action="toggle-brand" data-brand="${esc(b)}"` }));
    if (searchTerm.trim()) chips.push({ type: 'جستجو', value: esc(searchTerm.trim()), attrs: 'data-action="clear-search"' });
    if (chips.length === 0) { wrap.classList.add('hidden'); wrap.innerHTML = ''; return; }
    wrap.innerHTML = '<span class="label">فیلترها:</span>' +
      chips.map((c) => `<span class="filter-chip"><span class="chip-type">${c.type}</span> ${c.value} <span class="chip-x" ${c.attrs}><i class="fas fa-times"></i></span></span>`).join('') +
      '<button class="clear-all-btn" type="button" data-action="clear-all"><i class="fas fa-eraser"></i> پاک کردن</button>';
    wrap.classList.remove('hidden');
  }
  function clearSearch() { $('searchInput').value = ''; searchTerm = ''; renderActiveFilters(); renderPublicProducts(); }
  function clearAllFilters() { activeCats = []; activeBrands = []; $('searchInput').value = ''; searchTerm = ''; renderAll(); }

  /* ---------- سلول‌های قیمت ---------- */
  function priceCell(val, baloochi) {
    const cls = baloochi ? 'td-price baloochi' : 'td-price';
    if (val > 0) return `<td class="${cls}"><span class="p-num">${formatPrice(val)}</span><span class="p-unit">ت</span></td>`;
    return `<td class="${cls} no-price">استعلام</td>`;
  }
  function priceCellTerms(val, terms, baloochi) {
    const cls = baloochi ? 'td-price baloochi' : 'td-price';
    let html;
    if (val > 0) {
      html = `<span class="p-num">${formatPrice(val)}</span><span class="p-unit">ت</span>`;
      if (terms && terms.trim()) html += `<div class="p-terms">${esc(terms)}</div>`;
    } else html = '<span class="no-price">استعلام</span>';
    return `<td class="${cls}">${html}</td>`;
  }
  function cardPriceCell(val, terms, baloochi) {
    const cls = baloochi ? 'pc-price-cell baloochi' : 'pc-price-cell factory';
    let html = `<div class="${cls}"><div class="pc-label">${baloochi ? 'بلوچی ★' : 'کارخانه'}</div>`;
    if (val > 0) {
      html += `<div class="pc-val">${formatPrice(val)}<small> ت</small></div>`;
      if (terms && terms.trim()) html += `<div class="pc-terms">${esc(terms)}</div>`;
    } else html += '<div class="pc-val no">استعلام</div>';
    return html + '</div>';
  }

  /* ---------- رندر محصولات ---------- */
  const STATUS = { in: { cls: 'in', txt: 'موجود' }, low: { cls: 'low', txt: 'کم' }, out: { cls: 'out', txt: 'ناموجود' } };

  function renderAll() { renderCatDropdown(); renderBrandDropdown(); renderActiveFilters(); renderPublicProducts(); }

  function renderPublicProducts() {
    const wrap = $('productList');
    const t = searchTerm.trim(), tl = t.toLowerCase();
    const items = getSectionProducts().filter((p) => {
      const catOk = activeCats.length === 0 || activeCats.includes(p.category);
      const brandOk = activeBrands.length === 0 || activeBrands.includes(p.brand);
      const termOk = !t || (p.name || '').toLowerCase().includes(tl) || (p.code || '').toLowerCase().includes(tl) || (p.brand || '').toLowerCase().includes(tl);
      return catOk && brandOk && termOk;
    });
    const pv = (p) => p.priceBaloochiCash || p.priceFactoryCash || 0;
    if (sortBy === 'price-asc') items.sort((a, b) => pv(a) - pv(b));
    else if (sortBy === 'price-desc') items.sort((a, b) => pv(b) - pv(a));
    else if (sortBy === 'name') items.sort((a, b) => (a.name || '').localeCompare(b.name || '', 'fa'));

    $('resultCount').textContent = toFa(items.length);
    if (items.length === 0) {
      wrap.innerHTML = '<div class="empty"><i class="fas fa-search"></i><h4>موردی یافت نشد</h4><p>با فیلترهای انتخاب‌شده محصولی وجود ندارد.</p><span class="reset-link" data-action="clear-all">پاک کردن فیلترها</span></div>';
      return;
    }
    let html = '';
    const catsToShow = activeCats.length > 0 ? activeCats : getSectionCats().map((c) => c.id);
    catsToShow.forEach((cid) => {
      const cat = DATA.categories.find((c) => c.id === cid);
      if (!cat) return;
      const group = items.filter((i) => i.category === cid);
      if (group.length === 0) return;
      if (activeBrands.length > 0) {
        html += renderSection(cat, cat.name + ' — ' + activeBrands.map(toFa).join('، '), group);
      } else if (activeCats.length === 1) {
        const byBrand = {};
        group.forEach((p) => { const b = p.brand || 'سایر'; (byBrand[b] = byBrand[b] || []).push(p); });
        Object.keys(byBrand).sort().forEach((b) => { html += renderSection(cat, cat.name + ' — ' + toFa(b), byBrand[b]); });
      } else {
        html += renderSection(cat, cat.name, group);
      }
    });
    wrap.innerHTML = html;
  }

  function renderSection(cat, title, items) {
    const digTag = cat.digital ? '<span class="digital-tag">DIGITAL</span>' : '';
    const iconCls = cat.digital ? 'icon-box digital' : 'icon-box';
    const rows = items.map((p) => {
      const st = STATUS[p.status] || STATUS.in;
      return `<tr><td><span class="p-code">${faEsc(p.code)}</span></td><td><div class="p-name">${esc(p.name)}</div>${p.desc ? `<div class="p-desc">${esc(p.desc)}</div>` : ''}</td><td class="p-brand">${faEsc(p.brand)}<br><small>${faEsc(p.origin)}</small></td>${priceCell(p.priceFactoryCash, false)}${priceCellTerms(p.priceFactoryInstallment, p.factoryInstallmentTerms, false)}${priceCell(p.priceBaloochiCash, true)}${priceCellTerms(p.priceBaloochiInstallment, p.baloochiInstallmentTerms, true)}<td><span class="p-status ${st.cls}">${st.txt}</span></td><td><button type="button" class="btn-detail" data-action="open-detail" data-id="${esc(p.id)}"><i class="fas fa-circle-info"></i> اطلاعات</button></td></tr>`;
    }).join('');
    const cards = items.map((p) => {
      const st = STATUS[p.status] || STATUS.in;
      return `<div class="p-card"><div class="pc-head"><div class="pc-name">${esc(p.name)}</div><span class="pc-code">${faEsc(p.code)}</span></div><div class="pc-meta">برند: <span class="brand">${faEsc(p.brand)}</span> | ${faEsc(p.origin)}</div><div class="pc-prices"><div class="pc-prices-title">قیمت نقد</div><div class="pc-price-grid">${cardPriceCell(p.priceFactoryCash, '', false)}${cardPriceCell(p.priceBaloochiCash, '', true)}</div></div><div class="pc-prices"><div class="pc-prices-title">قیمت اقساطی</div><div class="pc-price-grid">${cardPriceCell(p.priceFactoryInstallment, p.factoryInstallmentTerms, false)}${cardPriceCell(p.priceBaloochiInstallment, p.baloochiInstallmentTerms, true)}</div></div><div class="pc-status-row"><span class="p-status ${st.cls}">${st.txt}</span></div><div class="pc-footer"><button type="button" class="btn-detail" data-action="open-detail" data-id="${esc(p.id)}"><i class="fas fa-circle-info"></i> اطلاعات بیشتر</button></div></div>`;
    }).join('');
    return `<div class="price-section"><div class="section-head"><h3><span class="${iconCls}"><i class="fas ${esc(cat.icon)}"></i></span>${esc(title)} ${digTag}</h3><span class="cat-meta">${toFa(items.length)} محصول</span></div><div class="table-wrap"><table class="price-table"><thead><tr><th rowspan="2">کد</th><th rowspan="2">نام محصول</th><th rowspan="2">برند</th><th colspan="2" class="group-factory">قیمت کارخانه</th><th colspan="2" class="group-baloochi">قیمت بلوچی</th><th rowspan="2">وضعیت</th><th rowspan="2">اطلاعات</th></tr><tr><th class="sub">نقد</th><th class="sub">اقساطی</th><th class="sub baloochi">نقد</th><th class="sub baloochi">اقساطی</th></tr></thead><tbody>${rows}</tbody></table></div><div class="card-view">${cards}</div></div>`;
  }

  /* ---------- مودال جزئیات ---------- */
  function openDetail(id) {
    const p = DATA.products.find((x) => String(x.id) === String(id));
    if (!p) return;
    const sm = { in: { cls: 'in', txt: 'موجود' }, low: { cls: 'low', txt: 'موجودی کم' }, out: { cls: 'out', txt: 'ناموجود' } };
    const st = sm[p.status] || sm.in;
    const cat = DATA.categories.find((c) => c.id === p.category);
    const img = safeUrl(p.imageUrl), pdf = safeUrl(p.pdfUrl), extra = (p.extraInfo || '').trim();
    const hasPrice = p.priceFactoryCash > 0 || p.priceBaloochiCash > 0 || p.priceFactoryInstallment > 0 || p.priceBaloochiInstallment > 0;
    const pc = (v) => (v > 0 ? formatPrice(v) + ' <span style="font-size:9px;color:var(--ink-muted)">ت</span>' : '<span class="no-price">استعلام</span>');
    const tc = (v) => (v ? esc(v) : '<span class="no-price">—</span>');

    let html = `<div class="detail-hero">${img ? `<img src="${esc(img)}" class="detail-img" alt="" data-detail-img>` : '<div class="detail-img-placeholder"><i class="fas fa-tooth"></i></div>'}<div class="detail-info"><div class="code-row"><span class="p-code">${faEsc(p.code)}</span>${cat ? `<span style="font-size:10px;color:var(--ink-muted)">${esc(cat.name)}</span>` : ''}</div><h2>${esc(p.name)}</h2>${p.desc ? `<p style="font-size:11.5px;color:var(--ink-muted)">${esc(p.desc)}</p>` : ''}<div class="brand-row"><span>برند: <strong>${faEsc(p.brand)}</strong></span><span>کشور: <strong>${faEsc(p.origin)}</strong></span></div><div><span class="p-status ${st.cls}">${st.txt}</span></div></div></div>`;
    if (hasPrice) {
      html += `<div class="price-comp-wrap"><div class="price-comp-title"><i class="fas fa-coins"></i> جدول قیمت‌ها</div><table class="price-comp-table"><thead><tr><th></th><th class="factory-col">کارخانه</th><th class="baloochi-col">بلوچی</th></tr></thead><tbody><tr><td>قیمت نقد</td><td>${pc(p.priceFactoryCash)}</td><td class="baloochi-col">${pc(p.priceBaloochiCash)}</td></tr><tr><td>قیمت اقساطی</td><td>${pc(p.priceFactoryInstallment)}</td><td class="baloochi-col">${pc(p.priceBaloochiInstallment)}</td></tr><tr><td>شرایط پرداخت</td><td style="font-size:10.5px">${tc(p.factoryInstallmentTerms)}</td><td class="baloochi-col" style="font-size:10.5px">${tc(p.baloochiInstallmentTerms)}</td></tr></tbody></table></div>`;
      html += '<div class="detail-negotiable"><i class="fas fa-handshake"></i> شرایط اقساط بسته به میزان خرید و بودجه مشتری قابل مذاکره است</div>';
    } else {
      html += '<div class="price-comp-wrap" style="text-align:center;padding:14px"><div class="price-comp-title" style="justify-content:center"><i class="fas fa-phone"></i> برای استعلام قیمت تماس بگیرید</div></div>';
    }
    if (extra) html += `<div class="detail-extra"><h4><i class="fas fa-file-lines"></i> توضیحات</h4><p>${esc(extra)}</p></div>`;
    if (pdf) html += `<a href="${esc(pdf)}" target="_blank" rel="noopener noreferrer" class="detail-pdf"><i class="fas fa-file-pdf"></i> دانلود کاتالوگ PDF</a>`;
    html += `<div class="detail-contact"><div class="label">برای مشاوره و استعلام تماس بگیرید:</div><div class="phone">${esc(DATA.settings.mobile)}</div><div style="font-size:9px;color:var(--ink-muted)">${esc(DATA.settings.manager)} — ارسال به سراسر کشور</div></div>`;

    $('detailBody').innerHTML = html;
    const im = $('detailBody').querySelector('[data-detail-img]');
    if (im) im.addEventListener('error', () => { im.outerHTML = '<div class="detail-img-placeholder"><i class="fas fa-image"></i></div>'; }, { once: true });
    $('detailModal').classList.remove('hidden');
  }
  function closeDetail() { $('detailModal').classList.add('hidden'); }

  /* ---------- رویدادها (بدون onclick داخل HTML؛ سازگار با CSP) ---------- */
  function bindEvents() {
    document.addEventListener('click', (e) => {
      const el = e.target.closest('[data-action]');
      if (!e.target.closest('.dropdown')) closeAllDropdowns();
      if (e.target.id === 'detailModal') closeDetail();
      if (!el) return;
      const a = el.dataset.action;
      if (el.tagName === 'LABEL') e.preventDefault();
      switch (a) {
        case 'set-section': setSection(el.dataset.section); break;
        case 'toggle-dropdown': toggleDropdown(el.dataset.target); break;
        case 'toggle-cat': toggleCat(Number(el.dataset.id)); break;
        case 'toggle-brand': toggleBrand(el.dataset.brand); break;
        case 'select-all-cats': activeCats = getSectionCats().map((c) => c.id); updateAvailableBrands(); renderAll(); break;
        case 'clear-cats': activeCats = []; activeBrands = []; renderAll(); break;
        case 'select-all-brands': activeBrands = Object.keys(getAvailBrandsMap()); renderBrandDropdown(); renderActiveFilters(); renderPublicProducts(); break;
        case 'clear-brands': activeBrands = []; renderBrandDropdown(); renderActiveFilters(); renderPublicProducts(); break;
        case 'clear-search': clearSearch(); break;
        case 'clear-all': clearAllFilters(); break;
        case 'open-detail': openDetail(el.dataset.id); break;
        case 'close-detail': closeDetail(); break;
        case 'nav-toggle': $('mainNav').classList.toggle('open'); break;
      }
    });
    document.querySelectorAll('[data-dd-filter]').forEach((inp) => {
      inp.addEventListener('input', () => filterDDItems(inp.dataset.ddFilter, inp.value));
    });
    $('searchInput').addEventListener('input', (e) => { searchTerm = e.target.value; renderActiveFilters(); renderPublicProducts(); });
    $('sortSelect').addEventListener('change', (e) => { sortBy = e.target.value; renderPublicProducts(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeAllDropdowns(); closeDetail(); } });
    document.querySelectorAll('img[data-logo]').forEach((img) => img.addEventListener('error', () => showLogoFallback(img)));
  }

  document.addEventListener('DOMContentLoaded', () => {
    loadData();
    bindEvents();
    applySettings();
    renderAll();
  });
})();
