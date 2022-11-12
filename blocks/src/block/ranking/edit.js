import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { SelectControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/editor';
import classnames from 'classnames';

export default function edit(props) {
  const { attributes, setAttributes, className } = props;
  const { id } = attributes;
  const classes = classnames('ranking-box', 'block-box',
    {
      [ 'ranking-' + id ]: !! (id !== '-1'),
      [ className ]: !! className,
    }
  );
  setAttributes({ classNames: classes });

  // attributesのidが存在するかしないかを判断するフラグ
  let isRankingIdExist = false;

  // ドロップダウンリストに表示される有効なランキングアイテムの数
  let abledDropdownListItemCount = 0;

  function createOptions() {
    var options = [];
    options.push({ value: '-1', label: __('未選択', THEME_NAME)})
    if (typeof gbItemRankings !== 'undefined') {
      gbItemRankings.forEach((rank) => {
        if ((isRankingIdExist === false) && (rank.id === id)) {
          isRankingIdExist = true;
        }
        if (rank.visible == '1') {
          options.push({ value: rank.id, label: rank.title, disabled: false });
          abledDropdownListItemCount += 1;
        }
        else {
          options.push({ value: rank.id, label: rank.title + __('（リスト非表示）', THEME_NAME), disabled: true });
        }
      });
    }

    return options;
  }


  const getRankingMessage = () => {
    let msg = '';
    const setmsg = __('ダッシュボードメニューの「Cocoon設定」→「ランキング作成」からランキングを作成してください。', THEME_NAME);
    if (id == '-1' && typeof gbItemRankings !== 'undefined' && abledDropdownListItemCount === 0) {
      //ランキング非表示などで行こに選択できるランキングが存在しない場合
      msg = __('有効なランキングが登録されていません。', THEME_NAME) + setmsg;
    }
    else if (id == '-1' && typeof gbItemRankings !== 'undefined') {
      msg = __('ランキングを選択してください。', THEME_NAME);
    }
    else if (id == '-1' && typeof gbItemRankings === 'undefined') {
      msg = __('ランキングが登録されていません。', THEME_NAME) + setmsg;
    }
    return (
      <div class='editor-ranking-message'>
        {msg}
      </div>
    );
  }


  const getRankingContent = () => {
    if (id == '-1') {
      return getRankingMessage();
    }
    else {
      return (
        <ServerSideRender
          block={props.name}
          attributes={attributes}
        />
      );
    }
  }

  var options = createOptions();

  // ランキングを消したりして存在しないランキングIDだった場合は-1をセットする
  // これをすることによりブロックエディターリロード時でも「ランキングを選択してください。」などのエラーメッセージが出力される
  if (!isRankingIdExist) {
    setAttributes({ id: '-1' });
  }
  
  return (
    <Fragment>
      <div {...useBlockProps()}>
        <SelectControl
            label={__('ランキング', THEME_NAME)}
            labelPosition="side"
            className="editor-ranking-dropdown"
            value={id}
            onChange={(value) => setAttributes({ id: value, classNames: classes })}
            options={options}
        />
        {getRankingContent()}
      </div>
    </Fragment>
  );
}
