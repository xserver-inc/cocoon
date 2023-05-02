import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InnerBlocks,
  useBlockProps,
  __experimentalUseInnerBlocksProps as useInnerBlocksProps,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import classnames from 'classnames';

const ALLOWED_BLOCKS = [
  'cocoon-blocks/comparison-left',
  'cocoon-blocks/comparison-right',
];
const TEMPLATE = [
  [ 'cocoon-blocks/comparison-left' ],
  [ 'cocoon-blocks/comparison-right' ],
];

export default function edit( { attributes, setAttributes, className } ) {
  const classes = classnames( 'comparison-box', 'block-box', {
    [ className ]: !! className,
  } );
  const blockProps = useBlockProps( {
    className: classes,
  } );

  const innerBlocksProps = useInnerBlocksProps( blockProps, {
    allowedBlocks: ALLOWED_BLOCKS,
    template: TEMPLATE,
    // orientation: 'horizontal',
    __experimentalLayout: {
      type: 'default',
      style: 'comparison',
      alignments: [],
    },
  } );

  return (
    <Fragment>
      <div { ...blockProps }>
        <div { ...innerBlocksProps } />
      </div>
    </Fragment>
  );
}
