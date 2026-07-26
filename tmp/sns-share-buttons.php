<?php //シェアボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
<?php if ( is_sns_share_buttons_visible($option) ): ?>
<div class="sns-share<?php echo esc_attr(get_additional_sns_share_button_classes($option)); ?>">
  <?php if ( get_sns_bottom_share_message() && $option == SS_BOTTOM ): //シェアボタン用のメッセージを取得?>
    <div class="sns-share-message"><?php echo get_sns_bottom_share_message(); ?></div>
  <?php endif; ?>

  <div class="sns-share-buttons sns-buttons">
    <?php
    //SNSの定義一覧（lib/sns-share.php の get_cocoon_sns_share_options()）をもとにシェアボタンを出力する
    //新しいSNSを追加したいときは cocoon_sns_share_options フィルターで定義を足すだけでよい
    //※ class・icon・count_class・title・caption は定義一覧を組み立てた時点でエスケープ済み
    foreach ( get_cocoon_sns_share_options() as $sns ) {
      //表示しないSNSはここで抜ける。URL取得やシェア数取得は表示するものだけで実行される
      if ( !is_cocoon_sns_share_button_visible($sns, $option) ) {
        continue;
      }

      $sns_url = '';
      if ( $sns['type'] === 'share' ) {
        //フィルターで追加されたSNSでURL取得関数が未定義の場合は出力しない
        if ( empty($sns['url_fn']) ) {
          continue;
        }
        $url_fn = $sns['url_fn'];
        $sns_url = esc_url($url_fn());
      } elseif ( $sns['type'] === 'comment' ) {
        $sns_url = esc_url(!empty($sns['url']) ? $sns['url'] : '#comments');
      } elseif ( $sns['type'] !== 'copy' ) {
        //フィルターで未知の type を指定された場合は、リンク先の無いボタンを出さずに飛ばす
        continue;
      }

      //シェア数を表示しないSNSは count_fn を持たない
      $sns_count = '';
      if ( !empty($sns['count_fn']) ) {
        $count_fn = $sns['count_fn'];
        $sns_count = esc_html($count_fn());
      }
      $sns_inner = '<span class="social-icon '.$sns['icon'].'"></span>'
                 . '<span class="button-caption">'.$sns['caption'].'</span>'
                 . '<span class="share-count '.$sns['count_class'].'">'.$sns_count.'</span>';

      //ボタン同士の間に空白を入れる（従来のHTMLと同じくインライン要素の区切りを保つため）
      echo "\n      ";

      if ( $sns['type'] === 'copy' ) {
        //コピーボタンを出力したことをモバイルシェアボタン側へ伝える
        global $_MOBILE_COPY_BUTTON;
        $_MOBILE_COPY_BUTTON = true;
        echo '<a role="button" tabindex="0" class="sns-button share-button '.$sns['class'].'"'
           . ' data-clipboard-text="'.esc_attr(get_share_page_title()).' '.get_share_page_url().'"'
           . ' title="'.$sns['title'].'" aria-label="'.$sns['title'].'">'.$sns_inner.'</a>';
      } elseif ( $sns['type'] === 'comment' ) {
        echo '<a href="'.$sns_url.'" class="sns-button share-button '.$sns['class'].'"'
           . ' title="'.$sns['title'].'" aria-label="'.$sns['title'].'">'.$sns_inner.'</a>';
      } else {
        //attrs_after_class / attrs_after_rel は、はてブやPinterestのように特定の位置へdata属性を差し込むためのもの
        echo '<a href="'.$sns_url.'" class="sns-button share-button '.$sns['class'].'"'.$sns['attrs_after_class']
           . ' target="_blank" title="'.$sns['title'].'" rel="nofollow noopener noreferrer"'.$sns['attrs_after_rel']
           . ' aria-label="'.$sns['title'].'">'.$sns_inner.'</a>';
      }
    }
    echo "\n    ";
    ?>
  </div><!-- /.sns-share-buttons -->

</div><!-- /.sns-share -->
<?php endif; ?>
