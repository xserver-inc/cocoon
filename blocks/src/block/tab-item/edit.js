import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { InnerBlocks } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import classnames from 'classnames';

const TEMPLATE = [
  [ 'core/paragraph', {} ],
];

export default function edit( props ) {
  const {attributes, className} = props;
  const {isActive} = attributes;

  const classes = classnames( className, {
    'tab-content': true,
    'is-active': (isActive === true) // この処理はエディタでだけ実行したいのでsaveには書かない
  });

  return (
    <div class={classes}>
      <InnerBlocks
        template={TEMPLATE}
        templateLock={false}
      />
    </div>
  );
}