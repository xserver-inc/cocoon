<?php //吹き出しリスト
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$_SB_SAMPLE_TEXTS =
  array(
    __( '吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当けんとうがつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。', THEME_NAME ),
    __( '雨ニモマケズ 風ニモマケズ 雪ニモ夏ノ暑サニモマケヌ 丈夫ナカラダヲモチ 慾ハナク 決シテ瞋ラズ ツモシヅカニワラッテヰル', THEME_NAME ),
    __( '私わたくしはその人を常に先生と呼んでいた。だからここでもただ先生と書くだけで本名は打ち明けない。', THEME_NAME ),
    __( 'メロスは激怒した。必ず、かの邪智暴虐の王を除かなければならぬと決意した。メロスには政治がわからぬ。', THEME_NAME ),
    __( '私は、その男の写真を三葉、見たことがある。', THEME_NAME ),
    __( 'ある日の暮方の事である。一人の下人が、羅生門の下で雨やみを待っていた。', THEME_NAME ),
    __( '親譲の無鉄砲むてっぽうで小供の時から損ばかりしている。', THEME_NAME ),
    __( '「天は人の上に人を造らず人の下に人を造らず」と言えり。', THEME_NAME ),
    __( '「おい地獄さ行えぐんだで！」　二人はデッキの手すりに寄りかかって、蝸牛が背のびをしたように延びて、海を抱かかえ込んでいる函館の街を見ていた。', THEME_NAME ),
    __( '私がウスウスと眼を覚ました時、こうした蜜蜂の唸るような音は、まだ、その弾力の深い余韻を、私の耳の穴の中にハッキリと引き残していた。', THEME_NAME ),
    __( 'うとうととして目がさめると女はいつのまにか、隣のじいさんと話を始めている。このじいさんはたしかに前の前の駅から乗ったいなか者である。', THEME_NAME ),
    __( 'ゴーシュは町の活動写真館でセロを弾く係りでした。けれどもあんまり上手でないという評判でした。', THEME_NAME ),
    __( 'これは、私わたしが小さいときに、村の茂平というおじいさんからきいたお話です。', THEME_NAME ),
  );

//var_dump($_POST);
$keyword = !empty($_POST['s']) ? $_POST['s'] : null;
$order_by = isset($_POST['order']) ? $_POST['order'] : 'date DESC, id DESC';
//var_dump($order_by);
$records = get_speech_balloons($keyword, $order_by);
//var_dump($records);
//並び替えオプション
generate_sort_options_tag($keyword, $order_by);
?>
<!-- メッセージ -->
<?php if ($records): ?>
  <p><?php _e( '設定を変更したり不要なものは削除してご利用ください。新しいものは「新規作成」ボタンから追加できます。', THEME_NAME );
  echo get_help_page_tag('https://wp-cocoon.com/how-to-use-speech-balloon/'); ?></p>
<?php else: ?>
  <p><?php _e( '「吹き出し」を作成するには「新規作成」リンクをクリックしてください。', THEME_NAME );
  echo get_help_page_tag('https://wp-cocoon.com/how-to-use-speech-balloon/'); ?></p>
<?php endif ?>

<div id="sb-list" class="postbox" style="max-width: 980px; margin-top: 20px;">
  <h2 class="hndle"><?php _e( '吹き出し一覧', THEME_NAME ) ?></h2>
  <div class="inside">

<!-- <table class="sb-list" style="width: 100%;"> -->
  <?php foreach ($records as $record):
  //var_dump($record);
  $edit_url   = add_query_arg(array('action' => 'edit',   'id' => $record->id));
  $delete_url = add_query_arg(array('action' => 'delete', 'id' => $record->id));
   ?>
  <div class="balloon-box">
    <div class="balloon-top">
      <?php if ($record->title): ?>
      <div><a href="<?php echo $edit_url; ?>"><?php echo $record->title; ?></a></div>
      <?php endif ?>
      <?php if (is_icon_irasutoya($record)): ?>
      <div><?php echo $record->credit; ?></div>
      <?php endif ?>
    </div>
    <div class="balloon-content">
      <div class="demo">
      <?php //吹き出しの表示
      generate_speech_balloon_tag(
        $record,
        $_SB_SAMPLE_TEXTS[rand(0, 11)]
      ); ?>
      </div>
      <div class="balloon-option">
        <div><a href="<?php echo $edit_url; ?>"><?php _e( '編集', THEME_NAME ) ?></a></div>
        <div><a href="<?php echo $delete_url; ?>"><?php _e( '削除', THEME_NAME ) ?></a></div>
        <?php if (!$record->visible): ?>
          <div>[<?php _e( '非表示', THEME_NAME ) ?>]</div>
        <?php endif ?>
      </div>
    </div>
  </div>
<!--   <tr style="margin-bottom: 20px">
    <td>


    </td>
    <td class="list-option" style="width: 50px;">

    </td>
  </tr> -->
  <?php endforeach ?>
<!-- </table> -->

  </div>
</div>
