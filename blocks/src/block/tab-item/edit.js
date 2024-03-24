import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import classnames from 'classnames';

const TEMPLATE = [
  [ 'core/paragraph', {} ],
];

export default function edit( props ) {
  const {clientId, attributes, setAttributes, className } = props;

  const classes = classnames( className, {
    'tab-content': true,
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