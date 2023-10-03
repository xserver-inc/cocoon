import { THEME_NAME } from '../helpers';
import { PanelBody, ButtonGroup, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function WidthPanel( { selectedWidth, setAttributes } ) {
  function handleChange( newWidth ) {
    // Check if we are toggling the width off
    const width = selectedWidth === newWidth ? undefined : newWidth;

    // Update attributes
    setAttributes( { width } );
  }

  return (
    <PanelBody title={ __( '幅設定', THEME_NAME ) }>
      <ButtonGroup aria-label={ __( '幅', THEME_NAME ) }>
        { [ '25', '50', '75', '100' ].map( ( widthValue ) => {
          return (
            <Button
              key={ widthValue }
              isSmall
              isPrimary={ widthValue === selectedWidth }
              onClick={ () => handleChange( widthValue ) }
            >
              { widthValue }%
            </Button>
          );
        } ) }
      </ButtonGroup>
    </PanelBody>
  );
}
