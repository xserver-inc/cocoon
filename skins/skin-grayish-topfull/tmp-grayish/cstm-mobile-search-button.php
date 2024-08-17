<?php //モバイル用の検索ボタン -> skin-grayish-topfullでPC版ヘッダーに流用
/**
 * Cocoon WordPress Theme
 * @author: yhira modify Na2factory
 * @link: https://cocoon-grayish.na2-factory.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if (!defined('ABSPATH')) exit;
global $_MENU_CAPTION;
global $_MENU_ICON;
$icon_class = $_MENU_ICON ? $_MENU_ICON : 'fa fa-search'; ?>

<!-- 検索ボタン -->
<?php if (!is_amp() || (is_amp() && is_ssl())) : ?>
  <!-- 検索ボタン -->
  <li class="search-menu-button menu-button">
    <dialog class="searchMenuDialog js-searchMenuDialog-content" style="display: none;">
      <div class="searchMenuDialog_wrapper js-searchMenuDialog-back">
        <div id="gnavi-search-menu-content" class="gnavi-search-menu-content">
          <?php //検索フォーム
          get_template_part('searchform') ?>
        </div>
        <button type="button" class="searchMenuDialog_close js-searchMenuDialog-close grayish-btn"><span class="searchMenuDialog_closemark" aria-label="<?php _e('検索フォームを閉じる', THEME_NAME); ?>"><?php _e('閉じる', THEME_NAME); ?></span></button>
      </div>
    </dialog>
    <button type="button" class="gnavi-search-menu-open grayish-btn js-searchMenuDialog-open" aria-label="<?php _e('検索フォームを開く', THEME_NAME); ?>">
      <span class="search-menu-icon menu-icon">
        <span class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></span>
      </span>
      <span class="search-menu-caption menu-caption"><?php echo $_MENU_CAPTION ? $_MENU_CAPTION : __('', THEME_NAME); ?></span>
    </button>
    <div class="gnavi-search-menu-content sponly-search-menu">
      <?php //検索フォーム
      get_template_part('searchform') ?>
    </div>
  </li>
<?php endif ?>