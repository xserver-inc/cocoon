const { createBlock } = wp.blocks;

const transforms = {
  to: [
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/micro-text' ],
      transform: ( attributes ) => {
        return createBlock( 'cocoon-blocks/micro-text', attributes );
      },
    },
  ],
};

export default transforms;
