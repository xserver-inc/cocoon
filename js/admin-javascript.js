/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

// 管理タブの何番目がクリックされたか
document.querySelectorAll("#tabs > ul > li").forEach((tab, index) => {
  tab.addEventListener("click", () => {
    document.getElementById("select_index").value = index;
  });
});

// トグルスイッチ
document.querySelectorAll('.toggle-link').forEach(toggleLink => {
  toggleLink.addEventListener('click', () => {
    const toggleContent = toggleLink.nextElementSibling;
    if (toggleContent && toggleContent.classList.contains('toggle-content')) {
      toggleContent.style.display = toggleContent.style.display === 'none' ? 'block' : 'none';
    }
  });
});

// WordPressの管理バー削除処理
function deleteWpAdminBar(selector) {
  document.querySelectorAll(selector).forEach(iframe => {
    iframe.addEventListener('load', () => {
      const iframeContent = iframe.contentDocument;
      if (iframeContent) {
        const wpAdminBar = iframeContent.getElementById('wpadminbar');
        if (wpAdminBar) wpAdminBar.style.display = 'none';
        const adminPanel = iframeContent.querySelector('.admin-panel');
        if (adminPanel) adminPanel.style.display = 'none';
        iframeContent.documentElement.style.cssText = 'margin-top: 0px !important';
      }
    });
  });
}

deleteWpAdminBar('.iframe-demo');
