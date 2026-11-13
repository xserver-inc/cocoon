<?php //本文のリンク設定
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//アンカーリンクはブログカードか
if ( !function_exists( 'is_anchor_link_tag_blogcard' ) ):
function is_anchor_link_tag_blogcard($anchor_link_tag){
  //_v($anchor_link_tag);
  if (strpos($anchor_link_tag, ' class="blogcard-wrap ') !== false) {
    return true;
  }
}
endif;

//本文の内部・外部リンクの置換
add_filter('the_content', 'replace_anchor_links', 12);
add_filter('the_author_box_name', 'replace_anchor_links', 12);
add_filter('get_the_author_description', 'replace_anchor_links', 12);
add_filter('widget_text', 'replace_anchor_links', 12);//テキストウィジェットをフック
add_filter('widget_text_pc_text', 'replace_anchor_links', 12);
//add_filter('widget_classic_text', 'replace_anchor_links', 12);
add_filter('widget_text_mobile_text', 'replace_anchor_links', 12);
add_filter('the_category_tag_content', 'replace_anchor_links', 12);
if ( !function_exists( 'replace_anchor_links' ) ):
function replace_anchor_links($the_content) {
  $res = preg_match_all('{<a [^>]+?>.+?</a>}is', $the_content, $m);
  //_v($m);
  //Aリンクがある場合
  if ($res && $m[0]) {
    // HTTP・HTTPSの違いに依存しない自サイトのホスト名
    $home_host = wp_parse_url(home_url(), PHP_URL_HOST);
    // 引用符内・連続文字・空白の一括読み飛ばしによるhref属性の抽出
    $href_pattern = '{^<a\b(?:[^\s\'">]++|"[^"]*"|\'[^\']*\'|\s++(?!href\s*=))*+\s+href\s*=\s*(?:"([^"]*)"|\'([^\']*)\')}is';

    foreach ($m[0] as $value) {
      //初期値の設定
      $old_a = $value;
      $new_a = $value;
      //aタグの均一化
      $value = strtolower(str_replace('&#8221;', '"', $value));

      //rel属性値の取得
      $rels = array();
      $res = preg_match('/ *rel="([^"]*?)"/i', $new_a, $m);
      //rel属性があれば値を取得する
      if ($res && $m[1]) {
        $rels = explode(' ', $m[1]);
      }

      // 元のHTMLを保持した、判定用URLの取得と正規化
      $link_url = '';
      $href_result = preg_match($href_pattern, $old_a, $href_match, PREG_UNMATCHED_AS_NULL);
      // URL抽出エラー時の元リンクの保持
      if ($href_result === false) {
        continue;
      }
      if ($href_result === 1) {
        $link_url = html_entity_decode($href_match[1] ?? $href_match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // URL内のタブ・改行と前後の制御文字・空白の除去
        $link_url = trim(str_replace(array("\t", "\n", "\r"), '', $link_url), "\x00..\x20");
      }

      // ページ内リンクの設定変更の除外
      if (strpos($link_url, '#') === 0) {
        continue;
      }

      //イメージリンクを除外
      /*
      if (preg_match('{<a[^>]+?href="[^"]+?"[^>]*?>\s*?<img .+?>\s*?</a>}is', $value)) {
        continue;
      }
      */
      $is_img_ilnk = preg_match('{<a[^>]+?href="[^"]+?"[^>]*?>\s*?<img .+?>\s*?</a>}is', $value);

      // 相対URLなどの既存動作を維持したWebリンクのホスト名の照合
      $is_internal_link = true;
      if (preg_match('{^(https?:)?//}i', $link_url)) {
        $link_host = wp_parse_url($link_url, PHP_URL_HOST);
        $is_internal_link = $home_host && $link_host && strcasecmp($home_host, $link_host) === 0;
      }

      if ($is_internal_link) {//内部リンクの場合
        //リンクの開き方を変更する
        $new_a = replace_target_attr_tag( get_internal_link_open_type(), $new_a );

        //フォロータイプの設定
        $rels = get_rel_follow_attr_values( get_internal_link_follow_type(), $rels );

        //noopenerの追加と削除
        $rels = get_noopener_rels( is_internal_link_noopener_enable(), $rels );
        //target="_blank"のnoopener
        if (!is_internal_link_noopener_enable() && includes_target_blalk($new_a)) {
          $rels = get_noopener_rels( is_internal_target_blank_link_noopener_enable(), $rels );
        }

        //noreferrerの追加と削除
        $rels = get_noreferrer_rels( is_internal_link_noreferrer_enable(), $rels );
        //target="_blank"のnoreferrer
        if (!is_internal_link_noreferrer_enable() && includes_target_blalk($new_a)) {
          $rels = get_noreferrer_rels( is_internal_target_blank_link_noreferrer_enable(), $rels );
        }

        if (!is_anchor_link_tag_blogcard($value) && !$is_img_ilnk) {
          //アイコンフォントの表示
          $new_a = replace_link_icon_font_tag( is_internal_link_icon_visible(), get_internal_link_icon(), 'internal-icon anchor-icon', $new_a );
        }

      } else { //外部リンクの場合
        //リンクの開き方を変更する
        $new_a = replace_target_attr_tag( get_external_link_open_type(), $new_a );

        //フォロータイプの設定
        $rels = get_rel_follow_attr_values( get_external_link_follow_type(), $rels );

        //noopenerの追加と削除
        $rels = get_noopener_rels( is_external_link_noopener_enable(), $rels );
        //target="_blank"のnoopener
        if (!is_external_link_noopener_enable() && includes_target_blalk($new_a)) {
          $rels = get_noopener_rels( is_external_target_blank_link_noopener_enable(), $rels );
        }

        //noreferrerの追加と削除
        $rels = get_noreferrer_rels( is_external_link_noreferrer_enable(), $rels );
        //target="_blank"のnoreferrer
        if (!is_external_link_noreferrer_enable() && includes_target_blalk($new_a)) {
          $rels = get_noreferrer_rels( is_external_target_blank_link_noreferrer_enable(), $rels );
        }

        //externalの追加と削除
        $rels = get_external_rels( is_external_link_external_enable(), $rels );

        if (!is_anchor_link_tag_blogcard($value) && !$is_img_ilnk) {
          //アイコンフォントの表示
          $new_a = replace_link_icon_font_tag( is_external_link_icon_visible(), get_external_link_icon(), 'external-icon anchor-icon', $new_a );
        }
      }//内部リンクか外部リンクか条件分岐の終わり

      //変更する場合はrel属性のクリアを行う
      $new_a = preg_replace('/ *rel="[^"]*?"/i', '', $new_a);
      $new_a = preg_replace('/<a/i', '<a rel="'.implode(' ', $rels).'"', $new_a);
      //rel属性が空の場合は削除
      $new_a = str_replace(' rel=""', '', $new_a);

      //何かしらの変更があった場合
      if ($old_a != $new_a) {
        $the_content = str_replace($old_a, $new_a, $the_content);
      }

    }//foreach

  }//Aリンクがある場合
  return $the_content;
}
endif;

//ビジュアルエディターでrel="noopener noreferrer"自動付加の解除
add_filter('tiny_mce_before_init','tinymce_allow_unsafe_link_target');
if ( !function_exists( 'tinymce_allow_unsafe_link_target' ) ):
function tinymce_allow_unsafe_link_target( $mce_init ) {
  $mce_init['allow_unsafe_link_target'] = true;
  return $mce_init;
}
endif;

//配列に文字列を追加
if ( !function_exists( 'add_string_to_array' ) ):
function add_string_to_array( $add_string, $rels ) {
  if (!in_array($add_string, $rels)) {
    $rels[] = $add_string;
  }
  return $rels;
}
endif;

//配列にnoopenerの追加と削除
if ( !function_exists( 'get_noopener_rels' ) ):
function get_noopener_rels( $noopener_enable, $rels ) {
  //noopenerの追加と削除
  if ($noopener_enable) {
    //noopenerの追加
    $rels = add_string_to_array( 'noopener', $rels );
  } else {
    //noopenerの削除
    $rels = delete_string_from_array( 'noopener', $rels );
  }
  return $rels;
}
endif;

//配列にnoreferrerの追加と削除
if ( !function_exists( 'get_noreferrer_rels' ) ):
function get_noreferrer_rels( $noreferrer_enable, $rels ) {
  //noreferrerの追加と削除
  if ($noreferrer_enable) {
    //noreferrerの追加
    $rels = add_string_to_array( 'noreferrer', $rels );
  } else {
    //noreferrerの削除
    $rels = delete_string_from_array( 'noreferrer', $rels );
  }
  return $rels;
}
endif;

//配列にexternalの追加と削除
if ( !function_exists( 'get_external_rels' ) ):
function get_external_rels( $external_enable, $rels ) {
  //externalの追加と削除
  if ($external_enable) {
    //externalの追加
    $rels = add_string_to_array( 'external', $rels );
  } else {
    //externalの削除
    $rels = delete_string_from_array( 'external', $rels );
  }
  return $rels;
}
endif;

//配列から文字列を削除
if ( !function_exists( 'delete_string_from_array' ) ):
function delete_string_from_array( $delete_string, $rels ) {
  if(($key = array_search($delete_string, $rels)) !== false) {
    unset($rels[$key]);
  }
  return $rels;
}
endif;

//target属性の置換
if ( !function_exists( 'replace_target_attr_tag' ) ):
function replace_target_attr_tag( $link_open_type, $the_a_tag ) {
  //リンクの開き方を変更する場合
  if ($link_open_type != 'default') {
    //外部リンクの開き方を変更する場合はtarget属性のクリアを行う
    $the_a_tag = preg_replace('/ *target="[^"]*?"/i', '', $the_a_tag);
    switch ($link_open_type) {
      case 'blank':
        $the_a_tag = str_replace('<a', '<a target="_blank"', $the_a_tag);
        break;
      case 'self':
        $the_a_tag = str_replace('<a', '<a target="_self"', $the_a_tag);
        break;
    }
  }
  return $the_a_tag;
}
endif;

//リンクアイコンフォントの置換
if ( !function_exists( 'replace_link_icon_font_tag' ) ):
function replace_link_icon_font_tag( $icon_font_visible, $icon_font, $class, $the_a_tag ) {
  //アイコンフォントの表示
  if ($icon_font_visible) {
    $the_a_tag = str_replace('</a>', '<span class="fa '.$icon_font.' '.$class.'"></span></a>', $the_a_tag);
  }
  return $the_a_tag;
}
endif;


//follow, nofollowのrel属性値取得
if ( !function_exists( 'get_rel_follow_attr_values' ) ):
function get_rel_follow_attr_values( $follow_type, $rels ) {
  //フォロータイプの設定
  if ($follow_type != 'default') {

    switch ($follow_type) {
      case 'nofollow':
        //nofollowの追加
        $rels = add_string_to_array( 'nofollow', $rels );
        //followがある場合は削除
        $rels = delete_string_from_array( 'follow', $rels );
        break;
      case 'follow':
        //followの追加
        $rels = add_string_to_array( 'follow', $rels );
        //nofollowがある場合は削除
        $rels = delete_string_from_array( 'nofollow', $rels );
        break;
    }

  }
  return $rels;
}
endif;
