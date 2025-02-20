<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  文字
//******************************************************************************
$text = ['red', 'blue' , 'green'];

for ($i=0; $i<count($text); $i++) {
  $color = get_theme_mod("hvn_rich_text_color{$i}_setting");
  if ($color) {
    echo <<< EOF
.{$text[$i]},.bold-{$text[$i]} {color: {$color};}

EOF;
  }
}


//******************************************************************************
//  マーカー
//******************************************************************************
$marker = [
  ['',      '#ffff99'],
  ['-red',  '#ffd0d1'],
  ['-blue', '#a8dafb'],
];

for ($i=0; $i<count($marker); $i++) {
  $color = get_theme_mod("hvn_marker_color{$i}_setting", $marker[$i][1]);
  if ($color) {
    if (get_theme_mod('hvn_marker_color_set1_setting')) {
      echo <<< EOF
.marker{$marker[$i][0]} {
  background: repeating-linear-gradient(-45deg, {$color} 0, {$color} 2px, transparent 2px, transparent 4px) no-repeat left bottom / 100%;
}

.marker-under{$marker[$i][0]} {
  background: repeating-linear-gradient(-45deg, {$color} 0, {$color} 2px, transparent 2px, transparent 4px) no-repeat left bottom / 100% 0.5em;
}

EOF;
    } else {
      echo <<< EOF
.marker{$marker[$i][0]} {
  background: {$color};
}

.marker-under{$marker[$i][0]} {
  background: linear-gradient(transparent 60%, {$color} 60%);
}

EOF;
    }
  }
}


//******************************************************************************
//  バッジ
//******************************************************************************
$b_class = ['orange', 'red', 'pink', 'purple', 'blue', 'green', 'yellow', 'brown', 'grey'];

$css = null;
for ($i=0; $i<count($b_class); $i++) {
  $color = get_theme_mod("hvn_badge_color{$i}_setting");
  if ($color) {
    $css .= "--cocoon-{$b_class[$i]}-color:{$color};";
  }
}
if ($css) {
  echo '[class*="badge"] {' . $css . '}';
}


//******************************************************************************
//  インラインボタン
//******************************************************************************
$i_class = ['black', 'red', 'blue', 'teal'];

$css = null;
for ($i=0; $i<count($i_class); $i++) {
  $color = get_theme_mod("hvn_inline_button_color{$i}_setting");
  if ($color) {
    $css .= "--cocoon-{$i_class[$i]}-color:{$color};";
  }
}
if ($css) {
  echo '[class*="inline-button"] {' . $css . '}';
}


if (get_theme_mod('hvn_inline_button_set3_setting')) {
  echo <<< EOF

[class*="inline-button"]:not([class*="inline-button-white-"]):after{
  --shadow: 3px;
  border-radius: inherit;
  box-shadow: 0 var(--shadow) 0 rgb(0 0 0 / 20%);
  content: '';
  display: block;
  height: 100%;
  left: -2px;
  margin: 0;
  pointer-events: none;
  position: absolute;
  top: -1px;
  width: calc(100% + 4px);
}

EOF;
}


//******************************************************************************
//  リスト丸数字
//******************************************************************************
$css = null;
$color = get_theme_mod('hvn_numeric_list_set1_setting');
if ($color) {
  $css = "background-color: {$color};";
}

$no = get_theme_mod('hvn_numeric_list_set2_setting');
if ($no) {
  $css .= 'border-radius: 0;';
}

if ($css) {
  echo <<< EOF
.editor-styles-wrapper .is-style-numeric-list-enclosed > li:before,
.is-style-numeric-list-enclosed > li:before {
  {$css}
}

EOF;
}


//******************************************************************************
//  アイコンボックス
//******************************************************************************
$icon_box_class = [
  ['blockquote'               ,'#cccccc'],
  ['.is-style-information-box','#87cefa'],
  ['.is-style-question-box'   ,'#ffe766'],
  ['.is-style-alert-box'      ,'#f6b9b9'],
  ['.is-style-memo-box'       ,'#8dd7c1'],
  ['.is-style-comment-box'    ,'#cccccc'],
  ['.is-style-ok-box'         ,'#3cb2cc'],
  ['.is-style-ng-box'         ,'#dd5454'],
  ['.is-style-good-box'       ,'#98e093'],
  ['.is-style-bad-box'        ,'#eb6980'],
  ['.is-style-profile-box'    ,'#cccccc'],
  ['.information-box'         ,'#87cefa'],
  ['.question-box'            ,'#ffe766'],
  ['.alert-box'               ,'#f6b9b9'],
  ['.memo-box'                ,'#8dd7c1'],
  ['.comment-box'             ,'#cccccc'],
  ['.ok-box'                  ,'#3cb2cc'],
  ['.ng-box'                  ,'#dd5454'],
  ['.good-box'                ,'#98e093'],
  ['.bad-box'                 ,'#eb6980'],
  ['.profile-box'             ,'#cccccc'],
  ['.information'             ,'#87cefa'],
  ['.question'                ,'#ffe766'],
  ['.alert'                   ,'#f6b9b9']
];

$id_array = [];
for ($i=0; $i<count($icon_box_class); $i++) {
  $id_array[$i] = $icon_box_class[$i][0];
}

$no = get_theme_mod('hvn_icon_box_set1_setting');
switch($no) {
  case 1:
    $id = implode(',', $id_array) ;
    echo <<< EOF
{$id} {
  background-color: transparent;
  border-width: 1px;
  color: var(--cocoon-text-color);
}

EOF;

    break;

  case 2:
    $id =  implode(':before,', $id_array);
    echo <<< EOF
{$id}:before{
  border: 0;
  color: #fff;
  margin: 0;
}
EOF;

    for ($i=0; $i<count($icon_box_class); $i++) {
      echo <<< EOF
{$icon_box_class[$i][0]}:before {
  background-color: {$icon_box_class[$i][1]};
}

EOF;
    }
    break;
}


//******************************************************************************
//  タブボックス
//******************************************************************************
$no = get_theme_mod('hvn_tab_box_set1_setting');
switch($no) {
  case 1:
    echo <<< EOF
.blank-box.bb-tab {
  margin-top: calc(var(--gap30) + 12.5px);
  padding-top: calc(var(--padding15) + 12.5px);
}

.tab-caption-box {
  padding-top: 12.5px;
}

.tab-caption-box-content {
  padding-top: calc(var(--padding15) + 12.5px);
}

.tab-caption-box-label {
  left: 15px;
  position: absolute;
  top: 0;
}

.editor-styles-wrapper .blank-box.bb-tab:before,
.blank-box.bb-tab .bb-label {
  left: 14px;
  position: absolute;
  top: -12.5px;
}

EOF;
    break;

  case 2:
    echo <<< EOF
.blank-box.bb-tab {
  margin: 0 0 var(--gap30) 0;
  padding-top: calc(var(--padding15) + 1.8em);
}

.editor-styles-wrapper .blank-box.bb-tab::before,
.blank-box.bb-tab .bb-label {
  top: -1px;
}


.tab-caption-box-label {
  position: absolute;
  top: 0;
}

.tab-caption-box-content {
  padding-top: calc(var(--padding15) + 1.8em);
}

EOF;
    break;
}


//******************************************************************************
//  FAQ
//******************************************************************************
$no = get_theme_mod('hvn_faq_set1_setting');
switch($no) {
  case 1:
    echo <<< EOF
.faq .faq-item-label {
  background-color: var(--cocoon-custom-question-color);
  color: #fff;
}
.faq .faq-answer-label {
  background-color: var(--cocoon-custom-answer-color);
}

EOF;
    break;

  case 2:
    echo <<< EOF
.faq .faq-item-label {
  background-color: var(--cocoon-custom-question-color);
  border-radius: 100%;
  color: #fff;
}
.faq .faq-answer-label {
  background-color: var(--cocoon-custom-answer-color);
}

EOF;
    break;
}


//******************************************************************************
//  パレット反映
//******************************************************************************
$colors = get_cocoon_editor_color_palette_colors();

$default_colors = [];
if (class_exists('WP_Theme_JSON_Resolver')) {
  $settings = WP_Theme_JSON_Resolver::get_core_data()->get_settings();
  if (isset($settings['color']['palette']['default'])) {
    $default_colors = $settings['color']['palette']['default'];
  }
}

$colors = array_merge($colors, $default_colors);
foreach ($colors as $color) {
  $slug  = $color['slug'];
  $color = $color['color'];
  echo <<< EOF
html .body .is-style-hvn-timeline-mini.has-{$slug}-point-color:not(.not-nested-style) .timeline-item:before{
  border-color: {$color};
}

html .body .is-style-hvn-timeline-line.has-{$slug}-point-color:not(.not-nested-style) .timeline-item-content:before{
  background-color: {$color};
}

html .body .hvn-timeline.has-{$slug}-point-color:not(.not-nested-style) .timeline-item:before{
  border-color: {$color};
}

EOF;
}
