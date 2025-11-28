import { THEME_NAME } from '../helpers';
import {
  PanelBody,
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
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
      <ToggleGroupControl
        label={ __( '幅', THEME_NAME ) }
        isBlock
        value={ selectedWidth }
        onChange={ ( newValue ) => handleChange( newValue ) }
        __nextHasNoMarginBottom={ true }
        __next40pxDefaultSize={ true }
      >
        { [ '25', '50', '75', '100' ].map( ( widthValue ) => (
          <ToggleGroupControlOption
            key={ widthValue }
            value={ widthValue }
            label={ `${ widthValue }%` }
          />
        ) ) }
      </ToggleGroupControl>
    </PanelBody>
  );
}
