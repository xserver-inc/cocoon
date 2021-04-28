import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { RichText, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import classnames from 'classnames';


export default function edit({ attributes, setAttributes, className }) {
  const { content, style } = attributes;

  function onChangeContent(newContent){
    setAttributes( { content: newContent } );
  }

  const classes = classnames(style, className);
  const blockProps = useBlockProps({
    className: classes,
  });

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

          <SelectControl
            label={ __( 'ラベル', THEME_NAME ) }
            value={ style }
            onChange={ ( value ) => setAttributes( { style: value } ) }
            options={ [
              {
                value: 'blogcard-type bct-none',
                label: __( 'なし', THEME_NAME ),
              },
              {
                value: 'blogcard-type bct-related',
                label: __( '関連記事', THEME_NAME ),
              },
              {
                value: 'blogcard-type bct-reference',
                label: __( '参考記事', THEME_NAME ),
              },
              {
                value: 'blogcard-type bct-reference-link',
                label: __( '参考リンク', THEME_NAME ),
              },
              {
                value: 'blogcard-type bct-popular',
                label: __( '人気記事', THEME_NAME ),
              },
              {
                value: 'blogcard-type bct-together',
                label: __( 'あわせて読みたい', THEME_NAME ),
              },
              {
                value: 'blogcard-type bct-detail',
                label: __( '詳細はこちら', THEME_NAME ),
              },
              {
                value: 'blogcard-type bct-check',
                label: __( 'チェック', THEME_NAME ),
              },
              {
                value: 'blogcard-type bct-pickup',
                label: __( 'ピックアップ', THEME_NAME ),
              },
              {
                value: 'blogcard-type bct-official',
                label: __( '公式サイト', THEME_NAME ),
              },
              {
                value: 'blogcard-type bct-dl',
                label: __( 'ダウンロード', THEME_NAME ),
              },
            ] }
          />

        </PanelBody>
      </InspectorControls>

      <div { ...blockProps }>
        <RichText
          onChange={ onChangeContent }
          value={ content }
          multiline="p"
        />
      </div>
    </Fragment>
  );
}