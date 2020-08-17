const { createBlock } = wp.blocks;

export const transforms = {
  to: [
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/caption-box-1' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock( 'cocoon-blocks/caption-box-1', attributes, innerBlocks );
      },
    },
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/label-box-1' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock( 'cocoon-blocks/label-box-1', attributes, innerBlocks );
      },
    },
  ],
};