/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InspectorControls,
  InnerBlocks,
  useBlockProps,
} from '@wordpress/block-editor';
import {
  PanelBody,
  Dropdown,
  Button,
  DateTimePicker,
} from '@wordpress/components';
import { format } from '@wordpress/date';
import { Fragment } from '@wordpress/element';

export default function CampaignEdit( { attributes, setAttributes } ) {
  const { from, to } = attributes;

  // 現在日時を取得
  const now = new Date();

  // 期間内かどうかを判定する関数
  const isInPeriod = () => {
    // 開始日時の判定（未入力の場合は1日前扱い）
    const fromTime = from
      ? new Date( from ).getTime()
      : now.getTime() - 86400000;
    // 終了日時の判定（未入力の場合は1日後扱い）
    const toTime = to ? new Date( to ).getTime() : now.getTime() + 86400000;
    return fromTime < now.getTime() && toTime > now.getTime();
  };

  // 期間外の場合の透過スタイル
  const inPeriod = isInPeriod();

  const blockProps = useBlockProps( {
    className: 'campaign block-box',
    style: {
      opacity: inPeriod ? 1 : 0.6,
      border: `2px dashed ${ inPeriod ? '#4caf50' : '#999' }`,
      padding: '24px',
    },
  } );

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __( '期間設定', THEME_NAME ) }>
          <div style={ { marginBottom: '16px' } }>
            <p style={ { marginBottom: '8px' } }>
              { __( '開始日時 (from)', THEME_NAME ) }
            </p>
            <Dropdown
              position="bottom left"
              renderToggle={ ( { isOpen, onToggle } ) => (
                <Button
                  variant="secondary"
                  onClick={ onToggle }
                  aria-expanded={ isOpen }
                  style={ { width: '100%', justifyContent: 'center' } }
                >
                  { from
                    ? format( 'Y/m/d H:i', from )
                    : __( '日時を選択', THEME_NAME ) }
                </Button>
              ) }
              renderContent={ () => (
                <DateTimePicker
                  currentDate={ from }
                  onChange={ ( newDate ) => setAttributes( { from: newDate } ) }
                />
              ) }
            />
            { from && (
              <Button
                variant="link"
                isDestructive
                onClick={ () => setAttributes( { from: '' } ) }
                style={ { marginTop: '4px', display: 'block' } }
              >
                { __( 'クリア', THEME_NAME ) }
              </Button>
            ) }
          </div>

          <div style={ { marginBottom: '16px' } }>
            <p style={ { marginBottom: '8px' } }>
              { __( '終了日時 (to)', THEME_NAME ) }
            </p>
            <Dropdown
              position="bottom left"
              renderToggle={ ( { isOpen, onToggle } ) => (
                <Button
                  variant="secondary"
                  onClick={ onToggle }
                  aria-expanded={ isOpen }
                  style={ { width: '100%', justifyContent: 'center' } }
                >
                  { to
                    ? format( 'Y/m/d H:i', to )
                    : __( '日時を選択', THEME_NAME ) }
                </Button>
              ) }
              renderContent={ () => (
                <DateTimePicker
                  currentDate={ to }
                  onChange={ ( newDate ) => setAttributes( { to: newDate } ) }
                />
              ) }
            />
            { to && (
              <Button
                variant="link"
                isDestructive
                onClick={ () => setAttributes( { to: '' } ) }
                style={ { marginTop: '4px', display: 'block' } }
              >
                { __( 'クリア', THEME_NAME ) }
              </Button>
            ) }
          </div>
        </PanelBody>
      </InspectorControls>

      <div { ...blockProps }>
        { /* 状態ラベルの表示 */ }
        <div
          style={ {
            background: inPeriod ? '#4caf50' : '#e09b3d',
            color: inPeriod ? '#fff' : '#111',
            padding: '6px 12px',
            fontSize: '13px',
            fontWeight: 'bold',
            borderRadius: '4px',
            marginBottom: '16px',
            display: 'inline-block',
          } }
        >
          { inPeriod
            ? __( '現在は表示期間内です', THEME_NAME )
            : __( '現在は表示期間外です', THEME_NAME ) }
        </div>
        <InnerBlocks />

      </div>
    </Fragment>
  );
}
