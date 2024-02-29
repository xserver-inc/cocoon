import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InnerBlocks,
  RichText,
  InspectorControls,
  useBlockProps,
  withColors,
  withFontSizes,
  PanelColorSettings,
} from '@wordpress/block-editor';
import { PanelBody, RangeControl, Tabs, RadioControl } from '@wordpress/components';
import { Fragment, useEffect, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import classnames from 'classnames';
import memoize from 'memize';
import { times } from 'lodash';

const ALLOWED_BLOCKS = [ 'cocoon-blocks/tab-item' ];

function createRadioControlOptions(tabLabelsArray) {
  var options = [];

  for (let i = 0; i < tabLabelsArray.length; i++) {
    options.push({label: tabLabelsArray[i], value: i});
  }

  return options;
}

const getTabsTemplate = memoize( ( tabLabelsArray ) => {
  return times( tabLabelsArray.length, (index) => [ 'core/paragraph', {placeholder: __('タブのコンテンツ', THEME_NAME) + index}] );
} );


export function TabEdit( props ) {
  const {
    clientId,
    attributes,
    setAttributes,
    className,
    backgroundColor,
    setBackgroundColor,
    textColor,
    setTextColor,
    borderColor,
    setBorderColor,
    fontSize,
  } = props;

  const {
    title,
    tabLabelsArray,
    needUpdate,
    customBackgroundColor,
    customTextColor,
    customBorderColor
  } = attributes;

  // InnerBlocksからラベル配列を作る処理
  const CreateTabLabelsArray = () => {
    // 現在のブロックのclientIDをキーにInnerBlockの数を取得
    const {tabItemCount} = useSelect(select=> ({
      tabItemCount: select('core/block-editor').getBlockCount(clientId)
    }));

    // InnerBlockの数だけループを回してInnerBlockのtitle attributeを取得して配列に格納する
    var tabLabels = [];
    for (let i = 0; i < tabItemCount; i++) {
      tabLabels.push(wp.data.select('core/block-editor').getBlocks(clientId)[i].attributes.title);
    }

    return tabLabels;
  }

  // attributesのtabLabelsArrayとInnerBlocksから生成したlabelsArrayのサイズに差異があればattributeを更新
  // needUpdateフラグが上がっている場合も更新
  var labelsArray = CreateTabLabelsArray();
  if (labelsArray.length !== tabLabelsArray.length || needUpdate === true) {
    setAttributes({tabLabelsArray: labelsArray});

    // needUpdateフラグを下げる
    setAttributes({needUpdate: false});
  }

  // 選択中のタブ番号を保持する
  //const [ selectedTabId, setSelectedTabId ] = useState(0);

  useEffect( () => {
    setAttributes( { backgroundColorValue: backgroundColor.color } );
    setAttributes( { textColorValue: textColor.color } );
    setAttributes( { borderColorValue: borderColor.color } );
  }, [ backgroundColor, textColor, borderColor ] );

  const classes = classnames( className, {
    'tab-block': true,
    'block-box': true,
    'cocoon-block-tab': true,
    'has-text-color': textColor.color,
    'has-background': backgroundColor.color,
    'has-border-color': borderColor.color,
    [ backgroundColor.class ]: backgroundColor.class,
    [ textColor.class ]: textColor.class,
    [ borderColor.class ]: borderColor.class,
    [ fontSize.class ]: fontSize.class,
  });

  const styles = {
    '--cocoon-custom-border-color': customBorderColor || undefined,
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
  };

  const blockProps = useBlockProps( {
    className: classes,
    style: styles,
  });

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
          {/* <RangeControl
            label={ __( 'タブ数' ) }
            value={ tabLabelsArray.length }
            onChange={ ( value ) => {
              var valueInt = parseInt(value);
              if (valueInt > tabLabelsArray.length) {
                for (let i = 0; i < valueInt - tabLabelsArray.length; i++) {
                  tabLabels.push(undefined);
                }
              }
              else if (valueInt < tabLabelsArray.length) {
                for (let i = 0; i < tabLabelsArray.length - valueInt; i++) {
                  tabLabels.pop();
                }
              }
              setAttributes( { tabLabelsArray: tabLabels } ) }
            }
            min={ 0 }
            max={ 10 }
          /> */}
          <PanelColorSettings
            title={ __( '色設定', THEME_NAME ) }
            colorSettings={ [
              {
                label: __( '背景色', THEME_NAME ),
                onChange: setBackgroundColor,
                value: backgroundColor.color,
              },
              {
                label: __( '文字色', THEME_NAME ),
                onChange: setTextColor,
                value: textColor.color,
              },
              {
                label: __( 'ボーダー色', THEME_NAME ),
                onChange: setBorderColor,
                value: borderColor.color,
              },
            ] }
            __experimentalIsRenderedInSidebar={ true }
          />
        </PanelBody>
      </InspectorControls>

      {/* <Tabs
        selectedTabId={selectedTabId}
        onSelect= {(selectedId) => {
          setSelectedTabId(selectedId);
          onSelect(selectedId);
        }}
      >
        <Tabs.TabList>
          {getTabList(tabLabelsArray)}
        </Tabs.TabList>
        {getTabPanel(tabLabelsArray.length)}
      </Tabs> */}

      <div { ...blockProps }>
        <div className="tab-title">
          <RichText
            value={title}
            onChange={ (value) => setAttributes( {title: value})}
            placeholder={__('タイトル', THEME_NAME)}
          />
        </div>
        {/* <div className="tab"> */}
          {/* <RadioControl
            selected={selectedTabId}
            options={createRadioControlOptions(tabLabelsArray)}
            onChange={(value) => setSelectedTabId(value)}
          /> */}
          <InnerBlocks
            //template={ getTabsTemplate( tabLabelsArray ) }
            //templateLock="all"
            allowedBlocks={ ALLOWED_BLOCKS }
            renderAppender={ InnerBlocks.ButtonBlockAppender }
          />
        {/* </div> */}
      </div>
    </Fragment>
  );
}

export default compose( [
  withColors( 'backgroundColor', {
    textColor: 'color',
    borderColor: 'border-color',
  } ),
  withFontSizes( 'fontSize' ),
] )( TabEdit );
