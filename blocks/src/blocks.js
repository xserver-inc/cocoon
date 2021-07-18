/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
 import {
  registerBlockType,
  unstable__bootstrapServerSideBlockDefinitions, // eslint-disable-line camelcase
} from '@wordpress/blocks';
import compareVersions from 'compare-versions';
const cocoonBlocksPro = [];

//構造化したブロック
import * as balloon from './block/balloon';
import * as blankBox from './block/blank-box';
import * as blogCard from './block/blogcard';
import * as button from './block/button';
import * as buttonWrap from './block/button-wrap';
import * as iconBox from './block/icon-box';
import * as iconList from './block/icon-list';
import * as infoBox from './block/info-box';
import * as searchBox from './block/search-box';
import * as stickyBox from './block/sticky-box';
import * as tabBox from './block/tab-box';
import * as timeline from './block/timeline';
import * as timelineItem from './block/timeline-item';
import * as toggleBox from './block/toggle-box';
import * as faq from './block/faq';

import * as captionBox from './block-universal/caption-box';
import * as labelBox from './block-universal/label-box';
import * as tabCaptionBox from './block-universal/tab-caption-box';

import * as microBalloon from './micro/micro-balloon';
import * as microText from './micro/micro-text';

const cocoonBlocks = [
  iconBox,
  infoBox,
  blankBox,
  stickyBox,
  tabBox,
  balloon,
  blogCard,
  button,
  buttonWrap,
  toggleBox,
  searchBox,
  timeline,
  timelineItem,
  iconList,
  faq,

  captionBox,
  tabCaptionBox,
  labelBox,

  microBalloon,
  microText,
];


export const __getCocoonBlocks = () => cocoonBlocks.concat(cocoonBlocksPro);

const registerBlock = (block) => {
  if (!block) {
    return;
  }

  let { metadata, settings, name } = block;

  // WP5.5未満の場合
  let wpVersion = 0;
  if (gbSettings['wpVersion']) {
    wpVersion = gbSettings['wpVersion'];
    // console.log(wpVersion);
  }
  //-RC版などの文字列が組まれる場合は取り除く
  wpVersion = wpVersion.replace(/-.+$/, '');
  if (compareVersions(wpVersion, '5.5') < 0) {
    //nameを削除
     delete metadata.name;
     //カテゴリ等を追加
     settings = {
       ...settings,
       ...metadata,
     };
   } else
   if (metadata) {
    unstable__bootstrapServerSideBlockDefinitions({ [name]: metadata });
  }
  registerBlockType(name, settings);
};

export const registerCocoonBlocks = (blocks = __getCocoonBlocks()) => {
  blocks.forEach(registerBlock);
};

registerCocoonBlocks();




//ブロック
// import './block/icon-box';
//import './block/info-box/block.js';
//import './block/blank-box/block.js';
//import './block/sticky-box/block.js';
//import './block/tab-box/block.js';
//import './block/balloon/block.js';
//import './block/balloon-ex/block.js';
//import './block/blogcard/block.js';
//import './block/button/block.js';
//import './block/button-wrap/block.js';
//import './block/toggle-box/block.js';
//import './block/search-box/block.js';
//import './block/timeline/block.js';
//import './block/icon-list/block.js';

//デフォルトブロックの拡張
import './custom/code/block.js';
// import './block/hoc-color-palette-demo/block.js';

//汎用ブロック
//import './block-universal/caption-box/block.js';
//import './block-universal/tab-caption-box/block.js';
//import './block-universal/label-box/block.js';

//マイクロコピー
//import './micro/micro-text/block.js';
//import './micro/micro-balloon/block.js';

//レイアウト
import './layout/column-children/block.js';
import './layout/column-2/block.js';
import './layout/column-3/block.js';

//ショートコード
// import './shortcode/affi/block.js';

//文字色変更など
import './toolbutton/bold.js';
import './toolbutton/red.js';
import './toolbutton/bold-red.js';
import './toolbutton/blue.js';
import './toolbutton/bold-blue.js';
import './toolbutton/green.js';
import './toolbutton/bold-green.js';
import './toolbutton/keyboard-key.js';
import './toolbutton/ruby.js';
import './toolbutton/clear-format.js';
import './toolbutton/html.js';

//マーカー
import './toolbutton/marker-yellow.js';
import './toolbutton/marker-under-yellow.js';
import './toolbutton/marker-red.js';
import './toolbutton/marker-under-red.js';
import './toolbutton/marker-blue.js';
import './toolbutton/marker-under-blue.js';

//バッジ
import './toolbutton/badge-orange.js';
import './toolbutton/badge-red.js';
import './toolbutton/badge-pink.js';
import './toolbutton/badge-purple.js';
import './toolbutton/badge-blue.js';
import './toolbutton/badge-green.js';
import './toolbutton/badge-yellow.js';
import './toolbutton/badge-brown.js';
import './toolbutton/badge-grey.js';

//ドロップダウン
import './toolbutton/dropdown-letters.js';
import './toolbutton/dropdown-markers.js';
import './toolbutton/dropdown-badges.js';
import './toolbutton/dropdown-font-sizes.js';
import './toolbutton/dropdown-shortcodes.js';
import './toolbutton/dropdown-templates.js';
import './toolbutton/dropdown-affiliates.js';
import './toolbutton/dropdown-rankings.js';



//旧バージョン（現在は非表示）
//ブロックエディター出現時の情報のないときに誤って作成したもの
//出来れば同一ブロックに統一して統合したいけど、やり方がよくわかっていない。
import './old/micro-balloon/block.js';
import './old/micro-balloon-1/block.js';
import './old/balloon/block.js';
import './old/balloon-1/block.js';
import './old/balloon-2/block.js';
import './old/balloon-ex/block.js';
import './old/blank-box/block.js';
import './old/tab-box/block.js';
import './old/toggle-box/block.js';
import './old/caption-box/block.js';
import './old/tab-caption-box/block.js';
import './old/label-box/block.js';
import './old/button/block.js';
// import './old/button-1/block.js';
import './old/button-wrap/block.js';
// import './old/button-wrap-1/block.js';

// import './demo/test/block.js';
// import './demo/info-box-drop/block.js';
// import './demo/blank-box-demo/block.js';
// import './demo/test-as-shortcode-text/block.js';
// import './demo/test-severside-as-shortcode-input/block.js';
// import './demo/test-get-id/block.js';
