const { createBlock } = wp.blocks;

export const transforms = {
  to: [
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/sticky-box' ],
      transform: ( { content } ) => {
        return createBlock( 'cocoon-blocks/sticky-box', { content } );
      },
    },
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/tab-box-1' ],
      transform: ( { content } ) => {
        return createBlock( 'cocoon-blocks/tab-box-1', { content } );
      },
    },
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/icon-box' ],
      transform: ( { content } ) => {
        return createBlock( 'cocoon-blocks/icon-box', { content } );
      },
    },
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/info-box' ],
      transform: ( { content } ) => {
        return createBlock( 'cocoon-blocks/info-box', { content } );
      },
    },
  ],
};