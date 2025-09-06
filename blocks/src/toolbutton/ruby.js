/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../helpers.js';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
  toggleFormat,
  registerFormatType,
  insert,
  applyFormat,
  removeFormat,
} from '@wordpress/rich-text';
import {
  RichTextToolbarButton,
  RichTextShortcut,
} from '@wordpress/block-editor';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faEllipsisH } from '@fortawesome/free-solid-svg-icons';

const isRubyVisible = Number(
  gbSettings.isRubyVisible ? gbSettings.isRubyVisible : 0
);

if (isRubyVisible) {
  // 下位フォーマット: rt
  registerFormatType('cocoon-blocks/rt', {
    title: __('ふりがな（ルビ）キャラクター', THEME_NAME),
    tagName: 'rt',
    className: null,
    edit() {
      return <Fragment></Fragment>;
    },
  });

  // 上位フォーマット: ruby
  registerFormatType('cocoon-blocks/ruby', {
    title: __('ふりがな（ルビ）', THEME_NAME),
    tagName: 'ruby',
    className: null,

    edit({ isActive, value, onChange }) {
      // ---- ユーティリティ ----
      const hasTypeAt = (formatsArr, idx, type) => {
        const f = formatsArr?.[idx];
        return Array.isArray(f) && f.some((fmt) => fmt && fmt.type === type);
      };

      const findContiguousSpanForTypeAround = (formatsArr, idx, type) => {
        // idx を含む連続領域（[start, end)）
        let start = idx;
        let end = idx;

        // 前方向
        while (start > 0 && hasTypeAt(formatsArr, start - 1, type)) start--;
        // 後方向
        while (end < (formatsArr?.length ?? 0) && hasTypeAt(formatsArr, end, type)) end++;

        return [start, end];
      };

      const deleteRangeFromValue = (val, start, end) => {
        // text と formats を同時に削除
        if (end <= start) return val;
        val.text = val.text.slice(0, start) + val.text.slice(end);
        if (Array.isArray(val.formats)) {
          val.formats.splice(start, end - start);
        }
        // 選択位置補正（削除開始点にキャレットを戻す）
        val.start = val.end = start;
        return val;
      };

      const removeAllRtInsideRubySpan = (val, rubyStart, rubyEnd) => {
        // ruby 範囲 [rubyStart, rubyEnd) にある rt 連続塊をすべて削除
        const fmts = val.formats || [];
        let i = rubyStart;

        // 連続塊ごとに後ろから削除するとインデックスずれが起きない
        const ranges = [];
        while (i < rubyEnd) {
          if (hasTypeAt(fmts, i, 'cocoon-blocks/rt')) {
            let s = i;
            let e = i + 1;
            while (e < rubyEnd && hasTypeAt(fmts, e, 'cocoon-blocks/rt')) e++;
            ranges.push([s, e]);
            i = e;
          } else {
            i++;
          }
        }

        // 後ろから削除
        for (let r = ranges.length - 1; r >= 0; r--) {
          const [s, e] = ranges[r];
          val = deleteRangeFromValue(val, s, e);
          // rubyEnd を短縮
          rubyEnd -= (e - s);
        }

        return { val, rubyStart, rubyEnd };
      };

      const onToggle = () => {
        let v = value;

        if (!isActive) {
          // 付与
          const ruby =
            window.prompt(
              __('ふりがな（ルビ）を入力してください。', THEME_NAME)
            ) || v.text.substr(v.start, v.end - v.start);

          const rubyEnd = v.end;
          const rubyStart = v.start;

          // ルビ文字列を直後に挿入
          v = insert(v, ruby, rubyEnd);

          // ベース + rt 部分までを ruby で囲う
          v.start = rubyStart;
          v.end = rubyEnd + ruby.length;
          v = applyFormat(v, { type: 'cocoon-blocks/ruby' }, rubyStart, rubyEnd + ruby.length);

          // 追加されたルビ文字列部分に <rt> を適用
          v = applyFormat(v, { type: 'cocoon-blocks/rt' }, rubyEnd, rubyEnd + ruby.length);

          return onChange(v);
        }

        // 解除（選択でもカーソルでも同じ挙動）
        const fmts = v.formats || [];
        const anchor = v.start; // 開始位置を基準に ruby 範囲を特定
        if (!hasTypeAt(fmts, anchor, 'cocoon-blocks/ruby') && anchor > 0 && hasTypeAt(fmts, anchor - 1, 'cocoon-blocks/ruby')) {
          // ちょうど ruby の境目にいる場合は 1 つ戻って判定
          v.start = v.end = anchor - 1;
        }

        if (hasTypeAt(v.formats || [], v.start, 'cocoon-blocks/ruby')) {
          // カーソル/選択開始が ruby 内にある → ruby の連続範囲を取得
          let [rubyStart, rubyEnd] = findContiguousSpanForTypeAround(v.formats || [], v.start, 'cocoon-blocks/ruby');

          // ruby 範囲内の rt テキストを全削除
          const removed = removeAllRtInsideRubySpan(v, rubyStart, rubyEnd);
          v = removed.val;
          rubyStart = removed.rubyStart;
          rubyEnd = removed.rubyEnd;

          // 残ったベース文字から ruby フォーマットを外す
          // （rt を全削除した後なので、ruby 範囲 = ベース文字列だけ、もしくは空）
          if (rubyEnd > rubyStart) {
            v = removeFormat(v, 'cocoon-blocks/ruby', rubyStart, rubyEnd);
            v.start = v.end = rubyStart;
          }

          return onChange(v);
        }

        // ここに来るのは、フォーカス位置が ruby 外で「ボタンがアクティブ扱い」だったような例外的状況。
        // その場合は既存のトグルで外すのみ（rt 残存を避けるため、選択範囲内の rt も念のため除去）。
        if (v.start !== v.end) {
          v = removeFormat(v, 'cocoon-blocks/rt', v.start, v.end);
        }
        v = toggleFormat(v, { type: 'cocoon-blocks/ruby' });
        return onChange(v);
      };

      // @see keycodes/src/index.js
      const shortcutType = 'primaryAlt';
      const shortcutCharacter = 'r';
      const icon = <FontAwesomeIcon icon={ faEllipsisH } />;

      return (
        <Fragment>
          <RichTextShortcut
            type={ shortcutType }
            character={ shortcutCharacter }
            onUse={ onToggle }
          />
          <RichTextToolbarButton
            icon={ icon }
            title={ __('ふりがな（ルビ）', THEME_NAME) }
            onClick={ onToggle }
            isActive={ isActive }
            shortcutType={ shortcutType }
            shortcutCharacter={ shortcutCharacter }
          />
        </Fragment>
      );
    },
  });
}
