const { createBlock } = wp.blocks;

const transforms = {
  to: [
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/caption-box-1' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock(
          'cocoon-blocks/caption-box-1',
          attributes,
          innerBlocks
        );
      },
    },
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/tab-caption-box-1' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock(
          'cocoon-blocks/tab-caption-box-1',
          attributes,
          innerBlocks
        );
      },
    },
  ],
};

export default transforms;
