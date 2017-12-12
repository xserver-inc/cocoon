<?php //
define('SB_SAMPLE_TEXTS',
  array(
    __( '吾輩わがはいは猫である。名前はまだ無い。どこで生れたかとんと見当けんとうがつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。', THEME_NAME ),
    __( '雨ニモマケズ 風ニモマケズ 雪ニモ夏ノ暑サニモマケヌ 丈夫ナカラダヲモチ 慾ハナク 決シテ瞋ラズ ツモシヅカニワラッテヰル', THEME_NAME ),
    __( '私わたくしはその人を常に先生と呼んでいた。だからここでもただ先生と書くだけで本名は打ち明けない。', THEME_NAME ),
    __( 'メロスは激怒した。必ず、かの邪智暴虐じゃちぼうぎゃくの王を除かなければならぬと決意した。メロスには政治がわからぬ。', THEME_NAME ),
    __( '私は、その男の写真を三葉、見たことがある。', THEME_NAME ),
    __( 'ある日の暮方の事である。一人の下人げにんが、羅生門らしょうもんの下で雨やみを待っていた。', THEME_NAME ),
    __( '親譲おやゆずりの無鉄砲むてっぽうで小供の時から損ばかりしている。', THEME_NAME ),
    __( 'ある日の事でございます。御釈迦様おしゃかさまは極楽の蓮池はすいけのふちを、独りでぶらぶら御歩きになっていらっしゃいました。', THEME_NAME ),
    __( '「天は人の上に人を造らず人の下に人を造らず」と言えり。', THEME_NAME ),
    __( '「おい地獄さ行えぐんだで！」　二人はデッキの手すりに寄りかかって、蝸牛かたつむりが背のびをしたように延びて、海を抱かかえ込んでいる函館はこだての街を見ていた。', THEME_NAME ),
    __( '私がウスウスと眼を覚ました時、こうした蜜蜂みつばちの唸うなるような音は、まだ、その弾力の深い余韻を、私の耳の穴の中にハッキリと引き残していた。', THEME_NAME ),
    __( 'うとうととして目がさめると女はいつのまにか、隣のじいさんと話を始めている。このじいさんはたしかに前の前の駅から乗ったいなか者である。', THEME_NAME ),
    __( 'ゴーシュは町の活動写真館でセロを弾く係りでした。けれどもあんまり上手でないという評判でした。', THEME_NAME ),
    __( 'これは、私わたしが小さいときに、村の茂平もへいというおじいさんからきいたお話です。', THEME_NAME ),
  )
);
//var_dump($_POST);
$keyword = !empty($_POST['s']) ? $_POST['s'] : null;
$order_by = isset($_POST['order']) ? $_POST['order'] : 'date DESC';
//var_dump($order_by);
$records = get_speech_balloons($keyword, $order_by);
//var_dump($records);
//並び替えオプション
generate_sort_options_tag($keyword, $order_by);
?>
<!-- メッセージ -->
<?php if ($records): ?>
  <p class="op-message"><?php _e( 'ショートコードをコピーして本文の表示したい部分に貼り付けてください。', THEME_NAME ) ?></p>
<?php else: ?>
  <p class="op-message"><?php _e( '「吹き出し」を作成するには「新規作成」リンクをクリックしてください。', THEME_NAME ) ?></p>
<?php endif ?>

<div id="sb-list" class="postbox" style="max-width: 980px; margin-top: 20px;">
  <h2 class="hndle"><?php _e( '吹き出し一覧', THEME_NAME ) ?></h2>
  <div class="inside">

<table class="sb-list" style="width: 100%;">
  <?php foreach ($records as $record):
  //var_dump($record);
  $edit_url   = add_query_arg(array('action' => 'edit',   'id' => $record->id));
  $delete_url = add_query_arg(array('action' => 'delete', 'id' => $record->id));
   ?>
  <tr style="margin-bottom: 20px">
    <td>
      <div class="demo">
      <?php //吹き出しの表示
      generate_speech_balloon_tag(
        $record->name,
        $record->icon,
        $record->style,
        $record->position,
        SB_SAMPLE_TEXTS[rand(0, 12)]
      ); ?>
      </div>
      <div class="shortcode"><?php _e( 'ショートコード：', THEME_NAME ) ?><input type="text" name="" value='[speech name="<?php echo esc_html($record->name); ?>" icon="<?php echo esc_html($record->icon); ?>" style="<?php echo esc_html($record->style); ?>" position="<?php echo esc_html($record->position); ?>"]VOICE[/speech]' style="width: 80%;"></div>
    </td>
    <td class="sb-list-option" style="width: 50px;">
      <p><a href="<?php echo $edit_url; ?>"><?php _e( '編集', THEME_NAME ) ?></a></p>
      <p><a href="<?php echo $delete_url; ?>"><?php _e( '削除', THEME_NAME ) ?></a></p>
    </td>
  </tr>
  <?php endforeach ?>
</table>

  </div>
</div>