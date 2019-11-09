<?php //ランキング関係の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//関数テキストテーブルのバージョン
define('ITEM_RANKINGS_TABLE_VERSION', DEBUG_MODE ? rand(0, 99) : '0.0');
define('ITEM_RANKINGS_TABLE_NAME',  $wpdb->prefix . THEME_NAME . '_item_rankings');

//関数テキスト移動用URL
define('IR_LIST_URL',   add_query_arg(array('action' => false,   'id' => false)));
define('IR_NEW_URL',    add_query_arg(array('action' => 'new',   'id' => false)));

//テーブルのバージョン取得
define('OP_ITEM_RANKINGS_TABLE_VERSION', 'item_rankings_table_version');
if ( !function_exists( 'get_item_rankings_table_version' ) ):
function get_item_rankings_table_version(){
  return get_theme_option(OP_ITEM_RANKINGS_TABLE_VERSION);
}
endif;

//テーブルが存在するか
if ( !function_exists( 'is_item_rankings_table_exist' ) ):
function is_item_rankings_table_exist(){
  return is_db_table_exist(ITEM_RANKINGS_TABLE_NAME);
}
endif;

//レコードを追加
if ( !function_exists( 'insert_item_ranking_record' ) ):
function insert_item_ranking_record($posts){
  $table = ITEM_RANKINGS_TABLE_NAME;
  $now = current_time('mysql');
  //_v($posts);
  $data = array(
    'date' => $now,
    'modified' => $now,
    'title' => $posts['title'],
    'item_ranking' => serialize($posts['item_ranking']),
    'count' => $posts['count'],
    'visible' => !empty($posts['visible']) ? $posts['visible'] : 0,
  );
  //_v($data);
  $format = array(
    '%s',
    '%s',
    '%s',
    '%s',
    '%d',
    '%d',
  );
  return insert_db_table_record($table, $data, $format);
}
endif;

//レコードの編集
if ( !function_exists( 'update_item_ranking_record' ) ):
function update_item_ranking_record($id, $posts){
  $table = ITEM_RANKINGS_TABLE_NAME;
  $now = current_time('mysql');

  $data = array(
    'modified' => $now,
    'title' => $posts['title'],
    'item_ranking' => serialize($posts['item_ranking']),
    'count' => $posts['count'],
    'visible' => !empty($posts['visible']) ? $posts['visible'] : 0,
  );
  $where = array('id' => $id);
  $format = array(
    '%s',
    '%s',
    '%s',
    '%d',
    '%d',
  );
  $where_format = array('%d');
  return update_db_table_record($table, $data, $where, $format, $where_format);
}
endif;

//初期データの入力
if ( !function_exists( 'add_default_item_ranking_records' ) ):
function add_default_item_ranking_records(){
  // $posts = array();
  // $posts['title'] = __( '[SAMPLE 01] 男性（左）', THEME_NAME );
  // $posts['name']  = __( '太郎', THEME_NAME );
  // $posts['icon']  = SB_DEFAULT_MAN_ICON;
  // $posts['style'] = SBS_STANDARD;
  // $posts['position'] = SBP_LEFT;
  // $posts['iconstyle'] = SBIS_CIRCLE_BORDER;
  // $posts['visible'] = 1;
  // insert_item_ranking_record($posts);
}
endif;

//テーブルの作成
if ( !function_exists( 'create_item_rankings_table' ) ):
function create_item_rankings_table() {
  $add_default_records = false;
  //テーブルが存在しない場合初期データを挿入（テーブル作成時のみ挿入）
  if (!is_item_rankings_table_exist()) {
    $add_default_records = true;
  }
  // SQL文でテーブルを作る
  $sql = "CREATE TABLE ".ITEM_RANKINGS_TABLE_NAME." (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    title varchar(126),
    item_ranking text NOT NULL,
    count bigint(20) DEFAULT 1,
    visible bit(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
  )";
  $res = create_db_table($sql);

  //初期データの挿入
  if ($res && $add_default_records) {
    //データ挿入処理
    add_default_item_ranking_records();
  }

  set_theme_mod( OP_ITEM_RANKINGS_TABLE_VERSION, ITEM_RANKINGS_TABLE_VERSION );
  return $res;
}
endif;


//テーブルのアップデート
if ( !function_exists( 'update_item_rankings_table' ) ):
function update_item_rankings_table() {
  // オプションに登録されたデータベースのバージョンを取得
  $installed_ver = get_item_rankings_table_version();
  $now_ver = ITEM_RANKINGS_TABLE_VERSION;
  if ( is_update_db_table($installed_ver, $now_ver) ) {
    create_item_rankings_table();
  }
}
endif;


//テーブルのアンインストール
if ( !function_exists( 'uninstall_item_rankings_table' ) ):
function uninstall_item_rankings_table() {
  uninstall_db_table(ITEM_RANKINGS_TABLE_NAME);
  remove_theme_mod(OP_ITEM_RANKINGS_TABLE_VERSION);
}
endif;


//テーブルレコードの取得
if ( !function_exists( 'get_item_rankings' ) ):
function get_item_rankings( $keyword = null, $order_by = null ) {

  $table_name = ITEM_RANKINGS_TABLE_NAME;
  return get_db_table_records($table_name, 'title', $keyword, $order_by);
}
endif;


//テーブルレコードの取得
if ( !function_exists( 'get_item_ranking' ) ):
function get_item_ranking( $id ) {
  $table_name = ITEM_RANKINGS_TABLE_NAME;
  $record = get_db_table_record( $table_name, $id );
  if ($record) {
    $record->title = !empty($record->title) ? stripslashes_deep($record->title) : '';
    $record->item_ranking = !empty($record->item_ranking) ? stripslashes_deep(unserialize($record->item_ranking)) : array();
    $record->count = !empty($record->count) ? $record->count : 1;
    $record->visible = !empty($record->visible) ? $record->visible : 0;
  }

  //var_dump($record);

  return $record;
}
endif;

//テーブルのレコードが空か
if ( !function_exists( 'is_item_rankings_record_empty' ) ):
function is_item_rankings_record_empty(){
  $table_name = ITEM_RANKINGS_TABLE_NAME;
  return is_db_table_record_empty($table_name);
}
endif;

//関数テキストレコードの削除
if ( !function_exists( 'delete_item_ranking' ) ):
function delete_item_ranking( $id ) {
  $table_name = ITEM_RANKINGS_TABLE_NAME;
  return delete_db_table_record( $table_name, $id );
}
endif;

//レーティングスタータグの取得
if ( !function_exists( 'get_rating_star_tag' ) ):
function get_rating_star_tag($rate, $max = 5, $number = false){
  $rate = floatval($rate);
  $rate = floor($rate * 10) / 10;
  $max = intval($max);
  //数字じゃない場合
  if (!is_numeric($rate) || !is_numeric($max)) {
    return $rate;
  }
  //レーティングが100より多い場合は多すぎるので処理しない
  if ($rate > 100 && $max > 100) {
    return $rate;
  }

  $tag = '<span class="rating-star">';

  //小数点で分割
  $rates = explode('.', $rate);
  if (!isset($rates[0])) {
    return $rate;
  }
  //小数点以下が5以上かどうか
  if (isset($rates[1])) {
    $has_herf = intval($rates[1]) >= 5;
  } else {
    $has_herf = false;
  }
  if ($has_herf) {
    $before = intval($rates[0]);
    $middle = 1;
    $after = $max - 1 - $before;
  } else {
    $before = intval($rate);
    $middle = 0;
    $after = $max - $before;
    //3.2とかの場合は小数点以下を切り捨てる
    //$rate = floor(floatval($rate));
  }
  //スターの出力
  for ($j=1; $j <= $before; $j++) {
    $tag .= '<span class="fa fa-star" aria-hidden="true"></span>';
  }
  //半分スターの出力
  for ($j=1; $j <= $middle; $j++) {
    $tag .= '<span class="fa fa-star-half-o" aria-hidden="true"></span>';
  }
  //空スターの出力
  for ($j=1; $j <= $after; $j++) {
    $tag .= '<span class="fa fa-star-o" aria-hidden="true"></span>';
  }

  if ($number) {
    $tag .= '<span class="rating-number">'.sprintf('%.1f', $rate).'</span>';
  }

  $tag .= '</span>';
  return $tag;
}
endif;

//HTMLを生成
if ( !function_exists( 'generate_item_ranking_tag' ) ):
function generate_item_ranking_tag($id, $is_first_only = false){
  $record = get_item_ranking($id);
  $items = isset($record->item_ranking) ? $record->item_ranking : array();
  $count = isset($record->count) ? intval($record->count) : 1;
  //$demo_class = $is_first_only ? ' demo' : '';
  ?>
  <?php //アイテムが存在している場合
  if (!empty($items)): ?>
  <div class="ranking-items no-icon">
  <?php
  for ($i = 1; $i <= $count; $i++):
    // if ($i == 2) {
    //   break;
    // }
    // var_dump($items);
    //var_dump($count);


    if ($is_first_only && $i > 1) {
      break;
    }

    $name = isset($items[$i]['name']) ? $items[$i]['name'] : '';
    $rating = isset($items[$i]['rating']) ? $items[$i]['rating'] : 'none';
    $image_tag = isset($items[$i]['image_tag']) ? $items[$i]['image_tag'] : '';
    $description = isset($items[$i]['description']) ? $items[$i]['description'] : '';
    $detail_url = isset($items[$i]['detail_url']) ? $items[$i]['detail_url'] : '';
    $link_url = isset($items[$i]['link_url']) ? $items[$i]['link_url'] : '';
    $link_tag = isset($items[$i]['link_tag']) ? $items[$i]['link_tag'] : '';
    //改行を取り除く
    $name      = preg_replace('/\n/', '', $name);
    $image_tag = preg_replace('/\n/', '', $image_tag);
    $link_tag  = preg_replace('/\n/', '', $link_tag);
    //ショートコード実行用フィルター
    $name        = apply_filters( 'ranking_item_name',        $name                 );
    $image_tag   = apply_filters( 'ranking_item_image_tag',   $image_tag            );
    $description = apply_filters( 'ranking_item_description', wpautop($description) );
    $link_tag    = apply_filters( 'ranking_item_link_tag',    $link_tag             );

   ?>

    <div class="ranking-item border-element">

      <div class="ranking-item-name">
        <div class="ranking-item-name-crown">
          <?php generate_ranking_crown_tag($i); ?>
        </div>
        <div class="ranking-item-name-text">
          <?php echo $name; ?>
        </div>
      </div>

      <?php //評価が設定されている場合
      if ($rating != 'none'){
        echo '<div class="ranking-item-rating">';
        echo get_rating_star_tag($rating);
        echo '</div>';
      } ?>

      <?php //continue; ?>

      <div class="ranking-item-img-desc">

        <?php //画像タグ情報があるとき
        if ($image_tag): ?>
        <div class="ranking-item-image-tag">
          <?php echo $image_tag; ?>
        </div>
        <?php endif ?>

        <div class="ranking-item-description">
          <?php echo $description; ?>
        </div>

      </div><!-- ./ranking-item-img-desc -->

      <?php //ボタン情報があるとき
      if ($detail_url || $link_url || $link_tag): ?>
      <div class="ranking-item-link-buttons">

        <?php //詳細ページURLがあるとき
        if ($detail_url):
          $target = apply_filters('cocoon_ranking_detail_page_target', '_self' );
        ?>
        <div class="ranking-item-detail">
          <a href="<?php echo $detail_url; ?>" target="<?php echo $target; ?>"><?php echo apply_filters('cocoon_ranking_detail_page_caption', __( '詳細ページ', THEME_NAME ) )  ?></a>
        </div>
        <?php endif ?>


        <?php //リンク情報があるとき
        if ($link_url || $link_tag): ?>
        <div class="ranking-item-link">
          <?php if ($link_url):
              $target = apply_filters('cocoon_ranking_official_page_target', '_blank' );
            ?>
            <a href="<?php echo $link_url; ?>" target="<?php echo $target; ?>"><?php echo apply_filters('cocoon_ranking_official_page_caption', __( '公式ページ', THEME_NAME )) ?></a>
          <?php else: ?>
            <?php echo $link_tag; ?>
          <?php endif ?>
        </div>
        <?php endif ?>

      </div><!-- /.ranking-item-link-buttons -->
      <?php endif ?>

    </div><!-- /.ranking-item -->

  <?php endfor ?>

  </div><!-- /.ranking-items -->
  <?php endif ?>

<?php
}
endif;

//ランキングアイテム入力項目が全て空か
if ( !function_exists( 'is_ranking_item_all_empty' ) ):
function is_ranking_item_all_empty($item){
  return empty($item['name']) && empty($item['image_tag']) && empty($item['description']) && empty($item['detail_url']) && empty($item['link_tag']);
}
endif;

//ランキングアイテム入力項目が有効か
if ( !function_exists( 'is_ranking_item_available' ) ):
function is_ranking_item_available($item){
  return !empty($item['name']) && !empty($item['description']);
}
endif;

if ( !function_exists( 'generate_ranking_crown_tag' ) ):
function generate_ranking_crown_tag($ranking_number){
  switch ($ranking_number) {
    case 1:
      echo '<div class="g-crown"><div class="g-crown-circle"></div></div>';
      break;
    case 2:
      echo '<div class="s-crown"><div class="s-crown-circle"></div></div>';
      break;
    case 3:
      echo '<div class="c-crown"><div class="c-crown-circle"></div></div>';
      break;
    default:

      break;
  }
}
endif;

if ( !function_exists( 'redirect_to_ranking_edit_page' ) ):
function redirect_to_ranking_edit_page($id){
  $url = add_query_arg(
    array(
      'action' => 'edit',
      'id' => $id,
      'from' => null,
      'to' => null,
      'del_no' => null,
      'conf_no' => null,
    )
  );
  redirect_to_url($url);
        exit;
}
endif;

//ランキングアイテムの移動
if ( !function_exists( 'move_ranking_item' ) ):
function move_ranking_item($id, $from, $to){
  //管理者以外が操作しようとした場合は何もしない
  if (!is_user_administrator()) {
    return;
  }
  $record = get_item_ranking($id);
  if ($record) {
    $items = isset($record->item_ranking) ? $record->item_ranking : array();
    if (!empty($items)) {
      //相手の入れ替え
      $tmp_item = $items[$to];
      $items[$to] = $items[$from];
      $items[$from] = $tmp_item;
      //オブジェクトを開いても配列に変換
      $posts = object_to_array($record);
      //アイテムランキングの更新
      $posts['item_ranking'] = $items;
      $res = update_item_ranking_record($id, $posts);

      if ($res) {
         redirect_to_ranking_edit_page($id);
      }

    }
  }
}
endif;

//ランキングアイテムの削除
if ( !function_exists( 'delete_ranking_item' ) ):
function delete_ranking_item($id, $del_no){
  //管理者以外が操作しようとした場合は何もしない
  if (!is_user_administrator()) {
    return;
  }
  $record = get_item_ranking($id);
  if ($record) {
    $items = isset($record->item_ranking) ? $record->item_ranking : array();
    if (!empty($items)) {
      //配列アイテムの削除
      unset($items[$del_no]);
      //_v($items);
      //順番を入れ替えた新しい連想配列を作り直す
      $i = 1;
      $new_items = array();
      foreach ($items as $key => $value) {
        $new_items[$i] = $value;
        $i++;
      }
      //_v($new_items);

      //削除配列をデータベースに登録

      //オブジェクトを開いても配列に変換
      $posts = object_to_array($record);
      //アイテムランキングの更新
      $posts['item_ranking'] = $new_items;
      $posts['count'] = count($new_items);
      $res = update_item_ranking_record($id, $posts);

      if ($res) {
        redirect_to_ranking_edit_page($id);
      }

    }
  }
}
endif;
