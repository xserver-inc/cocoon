import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
  SelectControl,
  PanelBody,
  TextControl,
  ToggleControl,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/editor';
import classnames from 'classnames';

export default function edit( props ) {
  const { attributes, setAttributes, className } = props;
  const { id, label, isImageCircle } = attributes;
  const classes = classnames( 'profile-block-box', 'block-box', {
    [ 'profile-block-box-' + id ]: !! ( id !== '-1' ),
    [ className ]: !! className,
    [ attributes.className ]: !! attributes.className,
  } );
  setAttributes( { classNames: classes } );

  // attributesのidが存在するかしないかを判断するフラグ
  let isProfileIdExist = false;

  // ドロップダウンリストに表示される有効なランキングアイテムの数
  let abledDropdownListItemCount = 0;

  function createOptions() {
    var options = [];
    options.push( { value: '-1', label: __( '未選択', THEME_NAME ) } );
    if ( typeof gbUsers !== 'undefined' ) {
      gbUsers.forEach( ( user ) => {
        if ( isProfileIdExist === false && user.id == id ) {
          isProfileIdExist = true;
        }

        options.push( {
          value: user.id,
          label: user.display_name,
          disabled: false,
        } );
        abledDropdownListItemCount += 1;
      } );
    }

    return options;
  }

  const getProfileMessage = () => {
    let msg = '';
    if ( typeof gbUsers !== 'undefined' && abledDropdownListItemCount === 0 ) {
      msg = __( '選択できるユーザーがありません。', THEME_NAME );
    } else if ( typeof gbUsers !== 'undefined' ) {
      msg = __( 'ユーザーを選択してください。', THEME_NAME );
    } else {
      return '';
    }
    return (
      <div class="cocoon-render-message editor-profile-block-box-message">
        { msg }
      </div>
    );
  };

  const getProfileContent = () => {
    if ( id == '-1' ) {
      return getProfileMessage();
    } else {
      return (
        <ServerSideRender block={ props.name } attributes={ attributes } />
      );
    }
  };

  var options = createOptions();

  if ( ! isProfileIdExist ) {
    setAttributes( { id: '-1' } );
  }

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody>
          <TextControl
            label={ __( 'ラベル', THEME_NAME ) }
            value={ label }
            onChange={ ( value ) => setAttributes( { label: value } ) }
          />
          <ToggleControl
            label={ __( '画像を円形にする', THEME_NAME ) }
            checked={ isImageCircle }
            onChange={ ( isChecked ) =>
              setAttributes( { isImageCircle: isChecked } )
            }
          />
        </PanelBody>
      </InspectorControls>
      <div { ...useBlockProps() }>
        <SelectControl
          label={ __( 'ユーザー', THEME_NAME ) }
          labelPosition="side"
          className="cocoon-render-dropdown editor-profile-block-box-dropdown"
          value={ id }
          onChange={ ( value ) =>
            setAttributes( { id: value, classNames: classes } )
          }
          options={ options }
        />
        { getProfileContent() }
      </div>
    </Fragment>
  );
}
