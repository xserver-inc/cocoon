<?php //目次関数

if ( !function_exists( 'get_toc_filter_priority' ) ):
function get_toc_filter_priority(){
  //優先順位の設定
  if (is_toc_before_ads()) {
    $priority = BEFORE_1ST_H2_TOC_PRIORITY_HIGH;
  } else {
    $priority = BEFORE_1ST_H2_TOC_PRIORITY_STANDARD;
  }
  return $priority;
}
endif;

//最初のH2タグの前に目次を挿入する
//ref:https://qiita.com/wkwkrnht/items/c2ee485ff1bbd81325f9
if (is_toc_visible()) {
  add_filter('the_content', 'add_toc_before_1st_h2', get_toc_filter_priority());
}
if ( !function_exists( 'add_toc_before_1st_h2' ) ):
function add_toc_before_1st_h2($the_content){

  //投稿ページだと表示しない
  if (!is_single_toc_visible() && is_single()) {
    return $the_content;
  }
  //固定ページだと表示しない
  if (!is_page_toc_visible() && is_page()) {
    return $the_content;
  }

  //投稿ページで非表示になっていると表示しない
  if (!is_the_page_toc_visible()) {
    return $the_content;
  }

  $content     = $the_content;
  $headers     = array();
  $html        = '';
  $toc_list    = '';
  $id          = '';
  $toggle      = '';
  $counter     = 0;
  $counters    = array(0,0,0,0,0,0);
  $harray      = array();

  $class       = 'toc';
  $title       = get_toc_title(); //目次タイトル
  $showcount   = 0;
  $depth       = intval(get_toc_depth()); //2-6 0で全て
  $top_level   = 2; //h2がトップレベル
  $targetclass = 'entry-content'; //目次対象となるHTML要素

  $set_depth = $depth;
  if (intval($set_depth) == 0) {
    $set_depth = 6;
  }
  $number_visible   = is_toc_number_visible(); //見出しの数字を表示するか
  if ($number_visible) {
    $list_tag = 'ol';
  } else {
    $list_tag = 'ul';
  }


  if($targetclass===''){$targetclass = get_post_type();}
  for($h = $top_level; $h <= 6; $h++){$harray[] = 'h' . $h . '';}
  //$harray = implode(',',$harray);

  preg_match_all('/<([hH][1-6]).*?>(.*?)<\/[hH][1-6].*?>/u', $content, $headers);
  $header_count = count($headers[0]);
  if($header_count > 0){
    $level = strtolower($headers[1][0]);
    if($top_level < $level){$top_level = $level;}
  }
  if($top_level < 1){$top_level = 1;}
  if($top_level > 6){$top_level = 6;}
  $top_level = $top_level;
  $current_depth          = $top_level - 1;
  $prev_depth             = $top_level - 1;
  $max_depth              = (($depth == 0) ? 6 : intval($depth)) - $top_level + 1;


  if($header_count > 0){
    $toc_list .= '<' . $list_tag . (($current_depth == $top_level - 1) ? ' class="toc-list open"' : '') . '>';
  }
  for($i=0;$i < $header_count;$i++){
    $depth = 0;
    switch(strtolower($headers[1][$i])){
      case 'h1': $depth = 1 - $top_level + 1; break;
      case 'h2': $depth = 2 - $top_level + 1; break;
      case 'h3': $depth = 3 - $top_level + 1; break;
      case 'h4': $depth = 4 - $top_level + 1; break;
      case 'h5': $depth = 5 - $top_level + 1; break;
      case 'h6': $depth = 6 - $top_level + 1; break;
    }
    //var_dump($depth);
    if($depth >= 1 && $depth <= $max_depth){
      if($current_depth == $depth && $i != 0){
        $toc_list .= '</li>';
      }
      while($current_depth > $depth){
        $toc_list .= '</li></'.$list_tag.'>';
        $current_depth--;
        $counters[$current_depth] = 0;
      }
      if($current_depth != $prev_depth){$toc_list .= '</li>';}
      if($current_depth < $depth){
        $toc_list .= '<'.$list_tag.'>';
        $current_depth++;
      }
      $counters[$current_depth - 1] ++;
      $counter++;
      $toc_list .= '<li><a href="#toc' . $counter . '" tabindex="0">' . strip_tags($headers[2][$i]) . '</a>';
      $prev_depth = $depth;
    }
  }
  while($current_depth >= 1 ){
    $toc_list .= '</li></'.$list_tag.'>';
    $current_depth--;
  }
  if($counter >= $showcount){
    if($id!==''){$id = ' id="' . $id . '"';}else{$id = '';}
    $html .= '
    <div' . $id . ' class="' . $class . get_additional_toc_classes() . '">
      <div class="toc-title">' . $title . '</div>
      ' . $toc_list .'
    </div>';

    //_v($counter);
    $display_count = intval(get_toc_display_count());
    if (is_int($display_count) && ($counter < $display_count)) {
      return $the_content;
    }

    ///////////////////////////////////////
    // jQueryの見出し処理（PHPの置換処理と比べてこちらの方が信頼度高い）
    ///////////////////////////////////////
    // $script = '
    // (function($){
    //   $(document).ready(function(){
    //     var hxs = $(".'.$targetclass.'").find("' . implode(',', $harray) . '");
    //     //console.log(hxs);
    //     hxs.each(function(i, e) {
    //       //console.log(e);
    //       //console.log(i+1);
    //       $(e).attr("id", "toc"+(i+1));
    //     });
    //   });
    // })(jQuery);';
    // //JavaScriptの縮小化
    // $script_min = minify_js($script);
    // //javascript.jsの後に読み込む
    // wp_add_inline_script( THEME_JS, $script_min, 'after' ) ;

    ///////////////////////////////////////
    // PHPの見出し処理（条件によっては失敗するかも）
    ///////////////////////////////////////
    $res = preg_match_all('/(<('.implode('|', $harray).')[^>]*?>)(.*?)(<\/h[2-6]>)/i', $the_content, $m);
    // var_dump($harray);
    // var_dump($res);
    //_v($m);
    $tag_all_index = 0;
    $tag_index = 1;
    $h_index = 2;
    $h_content_index = 3;
    $tag_end_index = 4;
    if ($res && $m[0]) {
      $i = 0;
      $count = 1;
      foreach ($m[$tag_all_index] as $value) {
        //var_dump($m[0][$i]);
        $tag_all = $m[$tag_all_index][$i];
        $tag = $m[$tag_index][$i];
        $h = $m[$h_index][$i];
        $h_content = $m[$h_content_index][$i];
        $tag_end = $m[$tag_end_index][$i];

        $now_depth = intval(str_replace('h', '', $h));
        //_v('$set_depth='.$set_depth.', '.'$now_depth='.$now_depth);
        //_v($tag_all);

        //設定より見出しが深い場合はスキップ
        if ($set_depth < $now_depth) {
          $i++;
          continue;
        }

        $new = $tag.'<span id="toc'.strval($count).'">'.$h_content.'</span>'.$tag_end;
        //$new = str_replace('<'.$h, '<'.$h.' id="toc'.strval($i+1).'"', $value);

        // var_dump($value);
        // var_dump($new);

        //_v($value);
        // _v($new);

        // $count = 1;
        // $the_content = str_replace($value, $new, $the_content, $count);
        $the_content = preg_replace('/'.preg_quote($value, '/').'/', $new, $the_content, 1);

        $i++;
        $count++;
      }
    }

  }
  $h2result = get_h2_included_in_body( $the_content );//本文にH2タグが含まれていれば取得
  $the_content = preg_replace(H2_REG, $html.PHP_EOL.PHP_EOL.$h2result, $the_content, 1);
  //var_dump($the_content);
  return $the_content;
}
endif;
