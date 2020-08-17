const { createBlock } = wp.blocks;

export const transforms = {
  to: [
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/micro-balloon-2' ],
      transform: ( attributes ) => {
        return createBlock( 'cocoon-blocks/micro-balloon-2', attributes );
      },
    }
  ]
};