const { createBlock } = wp.blocks;

export const transforms = {
  to: [
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/sticky-box' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock( 'cocoon-blocks/sticky-box', {}, innerBlocks );
      },
    },
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/tab-box-1' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock( 'cocoon-blocks/tab-box-1', attributes, innerBlocks );
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