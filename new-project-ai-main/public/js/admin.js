/* اسکریپت‌های کوچک پنل مدیریت — بدون اسکریپت inline (سازگار با CSP) */
(function () {
  'use strict';
  // تأیید قبل از حذف
  document.addEventListener('submit', function (e) {
    var msg = e.target.getAttribute('data-confirm');
    if (msg && !window.confirm(msg)) e.preventDefault();
  });
  // نمایش آیکون جایگزین وقتی لوگو بارگذاری نشود
  document.querySelectorAll('img[data-hide-on-error]').forEach(function (img) {
    img.addEventListener('error', function () { img.style.display = 'none'; });
  });
  // نمایش پیش‌نمایش آیکون دسته
  var iconSel = document.getElementById('cat-icon'), iconPrev = document.getElementById('cat-icon-preview');
  if (iconSel && iconPrev) {
    iconSel.addEventListener('change', function () { iconPrev.className = 'fas ' + iconSel.value; });
  }
})();
