<?php //アップデート関係の処理

//アップデートチェックの初期化
require 'theme-update-checker.php'; //ライブラリのパス
$example_update_checker = new ThemeUpdateChecker(
  strtolower(THEME_NAME), //テーマフォルダ名
  'https://wp-cocoon.com/wp-content/themes/cocoon-master/update-info.json' //JSONファイルのURL
);


//テーマアップデート時の処理
add_action( 'upgrader_process_complete', 'upgrader_process_complete_custom', 10, 2 );
if ( !function_exists( 'upgrader_process_complete_custom' ) ):
function upgrader_process_complete_custom( $upgrader_object, $options ) {
  if ( function_exists( '_v' ) ):
    _v($upgrader_object);
    _v($options);
  endif;

  var_dump($upgrader_object);
  var_dump($options);

  if ($options['action'] == 'update' && $options['type'] == 'theme' ){
    foreach($options['themes'] as $theme){
      //アップデートされたのが当テーマだった場合
      if ( strpos($theme, THEME_NAME) !== false ){
        if ( function_exists( '_v' ) ):
          _v(THEME_NAME.'がアップデートされました(upgrader_process_complete_custom)');
        endif;
      }
    }
  }
}
endif;


