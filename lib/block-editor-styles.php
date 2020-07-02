<?php //ブロックエディタースタイル関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'information-box',
    'label' => __( '補足情報(i)', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'question-box',
    'label' => __( '補足情報(?)', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'alert-box',
    'label' => __( '補足情報(!)', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'memo-box',
    'label' => __( 'メモ', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'comment-box',
    'label' => __( 'コメント', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'ok-box',
    'label' => __( 'OK', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'ng-box',
    'label' => __( 'NG', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'good-box',
    'label' => __( 'GOOD', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'bad-box',
    'label' => __( 'BAD', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'profile-box',
    'label' => __( 'プロフィール', THEME_NAME ),
  )
);
