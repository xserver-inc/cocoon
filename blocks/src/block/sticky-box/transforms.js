import { createBlock } from '@wordpress/blocks';

export default {
  to: [
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/blank-box-1' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock( 'cocoon-blocks/blank-box-1', {}, innerBlocks );
      },
    },
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/tab-box-1' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock( 'cocoon-blocks/tab-box-1', {}, innerBlocks );
      },
    },
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/icon-box' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock( 'cocoon-blocks/icon-box', {}, innerBlocks );
      },
    },
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/info-box' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock( 'cocoon-blocks/info-box', {}, innerBlocks );
      },
    },
  ],
};