const { createBlock } = wp.blocks;

export const transforms = {
  to: [
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/micro-text' ],
      transform: ( attributes ) => {
        return createBlock( 'cocoon-blocks/micro-text', attributes );
      },
    }
  ]
};