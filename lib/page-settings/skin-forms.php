<div class="metabox-holder">

<!-- スキン -->
<div id="skin" class="postbox">
  <h2 class="hndle"><?php _e( 'スキン設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'スキンを変更することで、サイトのデザインを手軽に変更できます。', THEME_NAME ) ?></p>

    <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
    <div class="demo iframe-standard-demo skin-demo">
      <iframe id="skin-demo" class="iframe-demo" src="<?php echo home_url(); ?>" width="1000" height="400"></iframe>
    </div>

    <table class="form-table">
      <tbody>

        <!-- スキン一覧 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SKIN_URL, __('スキン一覧', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              '' => __( 'なし', THEME_NAME ),
            );
            $skin_infos = get_skin_infos();
            foreach ($skin_infos as $info) {
              $file_url = $info['file_url'];
              $skin_name = $info['skin_name'];
              $skin_page_uri = $info['skin_page_uri'];
              $description = $info['description'];
              $author = $info['author'];
              $author_uri = $info['author_uri'];
              $version = $info['version'];

              $skin_text = $skin_name.' ';
              if ($skin_page_uri) {
                $skin_text = '<a href="'.$skin_page_uri.'" target="_blank">'.$skin_name.'</a> ';
              }

              $author_text = $author.' ';
              if ($author_uri) {
                $author_text = '&nbsp;&nbsp;<span style="font-style: italic;font-size: 0.9em;">['.__( '作者', THEME_NAME ).': <a href="'.$author_uri.'" target="_blank">'.$author.'</a>]</span>';
              }

              if ($version) {
                $version = 'v.'.strip_tags($version).' ';
              }

              if ($description) {
                $description = '<br><span class="indent">'.strip_tags($description).'</span>';
              }

              $caption = $skin_text.$author_text;//.$description;
              //var_dump($caption);
              $options += array($file_url => $caption);
            }
            //var_dump($options);
            generate_radiobox_tag(OP_SKIN_URL, $options, get_skin_url());
            generate_tips_tag(__( 'スキンを選択してください。', THEME_NAME ));

            ?>
          </td>
        </tr>

        <!-- 表示スキン -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_INCLUDE_SKIN_TYPE, __('表示スキン', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'all' => __( '全てのスキンを表示', THEME_NAME ),
              'parent_only' => __( '親テーマのスキンのみ表示', THEME_NAME ),
              'child_only' => __( '子テーマのスキンのみ表示', THEME_NAME ),
            );
            generate_radiobox_tag(OP_INCLUDE_SKIN_TYPE, $options, get_include_skin_type());
            generate_tips_tag(__( 'スキン一覧に含めて表示するスキンを選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 同梱スキン募集中 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __('同梱スキン募集中', THEME_NAME) ); ?>
          </th>
          <td>
            <p><?php _e( 'もし「良いスキンができたからCocoonに同梱してもいいよ」という方がおられましたらご連絡ください。', THEME_NAME ) ?></p>
            <p><?php _e( '詳しくはこちら', THEME_NAME ) ?> <span class="fa fa-arrow-right"></span> <a href="https://wp-cocoon.com/skin-make/" target="_blanl"><?php _e( 'スキンの同梱配布について', THEME_NAME ) ?></a></p>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->