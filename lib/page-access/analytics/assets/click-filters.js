// クリック解析のカスタム期間入力の表示切り替え
(function () {
  'use strict';
  const form = document.getElementById('cocoon-click-filters');
  if (!form) {return;}
  const period = form.elements.namedItem('period');
  const range = form.querySelector('.cocoon-analytics-custom-range');
  if (!period || !range) {return;}
  function updateRange() {
    const custom = period.value === 'custom';
    range.hidden = !custom;
    // 固定期間での不要な日付送信と、非表示入力による検証エラーの防止
    range.querySelectorAll('input').forEach((input) => {
      input.disabled = !custom;
      input.required = custom;
    });
  }
  period.addEventListener('change', updateRange);
  // 戻る操作でブラウザーに復元された期間と入力欄の同期
  window.addEventListener('pageshow', updateRange);
  updateRange();
})();
