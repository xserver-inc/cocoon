<div class="metabox-holder">

<!-- スキン -->
<div id="skin" class="postbox">
  <h2 class="hndle"><?php _e( 'スキン設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'スキンを変更することで、サイトのデザインを手軽に変更できます。。', THEME_NAME ) ?></p>

    <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
    <div class="demo iframe-standard-demo skin-demo">
      <iframe id="skin-demo" class="iframe-demo" src="<?php echo home_url(); ?>" width="1000" height="400"></iframe>
    </div>

    <table class="form-table">
      <tbody>

        <!-- スキン -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SKIN_URL, __('スキン', THEME_NAME) ); ?>
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
                $author_text = '[<span class="fa fa-user"></span>
<a href="'.$author_uri.'" target="_blank">'.$author.'</a>] ';
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

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->