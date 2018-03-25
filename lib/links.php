<?php //本文のリンク設定

//本文の外部リンクの置換
add_filter('the_content', 'replace_anchor_links');
add_filter('get_the_author_description', 'replace_anchor_links');
if ( !function_exists( 'replace_anchor_links' ) ):
function replace_anchor_links($the_content) {
  $res = preg_match_all('{<a[^>]+?>[^<]+?</a>}i', $the_content, $m);
  //Aリンクがある場合
  if ($res && $m[0]) {

    foreach ($m[0] as $value) {
      //初期値の設定
      $old_a = $value;
      $new_a = $value;

      //rel属性値の取得
      $rels = array();
      $res = preg_match('/ *rel="([^"]*?)"/i', $new_a, $m);
      //rel属性があれば値を取得する
      if ($res && $m[1]) {
        $rels = explode(' ', $m[1]);
      }

      //目次リンクの場合
      //var_dump(strpos($value, '#'));
      if ((strpos($value, 'href="#toc') > 0)) {
        continue;
      }

      //ボタンを除外
      if ((strpos($value, 'class="btn ') > 0)) {
        continue;
      }

      //ブログカード用のリンクを除外
      if (preg_match('{<a[^>]+?href="'.URL_REG_STR.'"[^>]*?>'.URL_REG_STR.'</a>}i', $value)) {
        continue;
      }

      if ((strpos($value, '//'.get_the_site_domain()) !== false) ) {//内部リンクの場合
        //リンクの開き方を変更する
        $new_a = replace_target_attr_tag( get_internal_link_open_type(), $new_a );

        //フォロータイプの設定
        $rels = get_rel_follow_attr_values( get_internal_link_follow_type(), $rels );

        //noopenerの追加と削除
        $rels = get_noopener_rels( is_internal_link_noopener_enable(), $rels );

        //noreferrerの追加と削除
        $rels = get_noreferrer_rels( is_internal_link_noreferrer_enable(), $rels );

        //アイコフォントの表示
        $new_a = replace_link_icon_font_tag( is_internal_link_icon_visible(), get_internal_link_icon(), 'internal-icon', $new_a );

      } else { //外部リンクの場合

        // //リンクの開き方を変更する場合
        // if (!get_external_link_open_type_default()) {
        //   //外部リンクの開き方を変更する場合はtarget属性のクリアを行う
        //   $new_a = preg_replace('/ *target="[^"]*?"/i', '', $new_a);
        //   switch (get_external_link_open_type()) {
        //     case 'blank':
        //       $new_a = str_replace('<a', '<a target="_blank"', $new_a);
        //       break;
        //     case 'self':
        //       $new_a = str_replace('<a', '<a target="_self"', $new_a);
        //       break;
        //   }
        // }
        //リンクの開き方を変更する
        $new_a = replace_target_attr_tag( get_external_link_open_type(), $new_a );

        // //rel属性値の取得
        // $rels = array();
        // $res = preg_match('/ *rel="([^"]*?)"/i', $new_a, $m);
        // //rel属性があれば値を取得する
        // if ($res && $m[1]) {
        //   $rels = explode(' ', $m[1]);
        // }

        // //フォロータイプの設定
        // if (!get_external_link_follow_type_default()) {

        //   //var_dump($rels);
        //   switch (get_external_link_follow_type()) {
        //     case 'nofollow':
        //       //nofollowの追加
        //       $rels = add_string_to_array( 'nofollow', $rels );
        //       // if (!in_array('nofollow', $rels)) {
        //       //   $rels[] = 'nofollow';
        //       // }
        //       //followがある場合は削除
        //       $rels = delete_string_from_array( 'follow', $rels );
        //       // if(($key = array_search('follow', $rels)) !== false) {
        //       //   unset($rels[$key]);
        //       // }
        //       break;
        //     case 'follow':
        //       //followの追加
        //       $rels = add_string_to_array( 'follow', $rels );
        //       // if (!in_array('follow', $rels)) {
        //       //   $rels[] = 'follow';
        //       // }
        //       //nofollowがある場合は削除
        //       $rels = delete_string_from_array( 'nofollow', $rels );
        //       // if(($key = array_search('nofollow', $rels)) !== false) {
        //       //   unset($rels[$key]);
        //       // }
        //       break;
        //   }

        // }//!get_external_link_follow_type_default

        //フォロータイプの設定
        $rels = get_rel_follow_attr_values( get_external_link_follow_type(), $rels );

        // //noopenerの追加と削除
        // if (is_external_link_noopener_enable()) {
        //   //noopenerの追加
        //   $rels = add_string_to_array( 'noopener', $rels );
        //   // if (!in_array('noopener', $rels)) {
        //   //   $rels[] = 'noopener';
        //   // }
        // } else {
        //   //noopenerの削除
        //   $rels = delete_string_from_array( 'noopener', $rels );
        //   // if(($key = array_search('noopener', $rels)) !== false) {
        //   //   unset($rels[$key]);
        //   // }
        // }
        //noopenerの追加と削除
        $rels = get_noopener_rels( is_external_link_noopener_enable(), $rels );

        // //noreferrerの追加と削除
        // if (is_external_link_noreferrer_enable()) {
        //   //noreferrerの追加
        //   $rels = add_string_to_array( 'noreferrer', $rels );
        //   // if (!in_array('noreferrer', $rels)) {
        //   //   $rels[] = 'noreferrer';
        //   // }
        // } else {
        //   //noreferrerの削除
        //   $rels = delete_string_from_array( 'noreferrer', $rels );
        //   // if(($key = array_search('noreferrer', $rels)) !== false) {
        //   //   unset($rels[$key]);
        //   // }
        // }
        //noreferrerの追加と削除
        $rels = get_noreferrer_rels( is_external_link_noreferrer_enable(), $rels );

        // //externalの追加と削除
        // if (is_external_link_external_enable()) {
        //   //externalの追加
        //   if (!in_array('external', $rels)) {
        //     $rels[] = 'external';
        //   }
        // } else {
        //   //externalの削除
        //   if(($key = array_search('external', $rels)) !== false) {
        //     unset($rels[$key]);
        //   }
        // }
        //externalの追加と削除
        $rels = get_external_rels( is_external_link_external_enable(), $rels );

        // //アイコフォントの表示
        // if (is_external_link_icon_visible()) {
        //   $new_a = str_replace('</a>', '<span class="fa '.get_external_link_icon().' external-icon"></span></a>', $new_a);
        // }
        //アイコフォントの表示
        $new_a = replace_link_icon_font_tag( is_external_link_icon_visible(), get_external_link_icon(), 'external-icon', $new_a );
      }//内部リンクか外部リンクか条件分岐の終わり

      //変更する場合はrel属性のクリアを行う
      $new_a = preg_replace('/ *rel="[^"]*?"/i', '', $new_a);
      $new_a = str_replace('<a', '<a rel="'.implode(' ', $rels).'"', $new_a);
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
  //アイコフォントの表示
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