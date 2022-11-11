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

  function createOptions() {
    var options = [];
    options.push({ value: '-1', label: __('未選択', THEME_NAME)})
    if (typeof gbItemRankings !== 'undefined') {
      gbItemRankings.forEach((rank) => {
        if (rank.visible == '1') {
          options.push({ value: rank.id, label: rank.title, disabled: false });
        }
        else {
          options.push({ value: rank.id, label: rank.title, disabled: true });
        }
      });
    }

    return options;
  }


  const getRankingMessage = () => {
    if (id == '-1' && typeof gbItemRankings !== 'undefined') {
      return (
        <div class='editor-ranking-message'>
          {__('ランキングを選択してください。', THEME_NAME)}
        </div>
      );
    }
    else if (id == '-1' && typeof gbItemRankings === 'undefined') {
      return (
        <div class='editor-ranking-message'>
          {__('ランキングが登録されていません。ダッシュボードメニューの「Cocoon設定」→「ランキング作成」からランキングを作成してください。', THEME_NAME)}
        </div>
      );
    }
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
