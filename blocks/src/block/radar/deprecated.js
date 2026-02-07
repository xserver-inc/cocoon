/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, hexToRgba, arrayValueTotal, getChartJsFontHeight } from '../../helpers';
import classnames from 'classnames';
const DEFAULT_COLOR = 'rgba(255, 99, 132, 0.2)';
const DEFAULT_BORDER_COLOR = 'rgba(255, 99, 132, 0.9)';
import { __ } from '@wordpress/i18n';

// API version 2で保存されたブロック（wp-block-*クラスなし）
const v1 = {
  save( props ) {
    const { attributes } = props;
    const {
      chartId,
      chartColor,
      backgroundColor,
      fontColor,
      gridColor,
      canvasSize,
      fontSize,
      fontWeight,
      maximum,
      displayTitle,
      title,
      displayLegend,
      legendText,
      labels,
      data,
      displayTotal,
      displayLabelValue,
      displayAngleLines,
    } = attributes;

    const classes = classnames( 'radar-block', 'block-box', {
      'is-style-small': canvasSize === 'is-style-small',
      'is-style-large': canvasSize === 'is-style-large',
    } );

    const total = arrayValueTotal( data );

    const dataStr = data.join( ',' );

    const styles = {
      '--chart-color': hexToRgba( chartColor, 0.2 ),
      '--chart-border-color': hexToRgba( chartColor, 0.9 ),
    };

    const fontHeight = getChartJsFontHeight( fontSize, fontWeight );

    return (
      <div className={ classes } style={ styles }>
        <canvas
          id={ chartId }
          className="radar-chart"
          data-labels={ labels.join( ',' ) }
          data-data={ dataStr }
          data-background-color={ backgroundColor }
          data-font-color={ fontColor }
          data-grid-color={ gridColor }
          data-chart-color={ hexToRgba( chartColor, 0.2 ) }
          data-chart-border-color={ hexToRgba( chartColor, 0.9 ) }
          data-font-size={ fontSize }
          data-font-weight={ fontWeight }
          data-font-height={ fontHeight }
          data-maximum={ maximum }
          data-display-title={ displayTitle }
          data-title={ title }
          data-display-legend={ displayLegend }
          data-legend-text={ legendText }
          data-display-label-value={ displayLabelValue }
          data-display-angle-lines={ displayAngleLines }
        ></canvas>
        { displayTotal ? (
          <div className="radar-total">
            { __( '合計', THEME_NAME ) }：<span className="radar-total-number">{ total }</span>
          </div>
        ) : (
          ''
        ) }
      </div>
    );
  },
};

export default [ v1 ];
