import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { SelectControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/editor';
import classnames from 'classnames';

export default function edit(props) {
  const { attributes, setAttributes, className } = props;
  const { id } = attributes;
  const classes = classnames('ad-box', 'block-box',
    {
      [ className ]: !! className,
      [ attributes.className ]: !! attributes.className,
    }
  );
  setAttributes({ classNames: classes });

  return (
    <Fragment>
      <div {...useBlockProps()}>
        {"[ad]"}
        <ServerSideRender
          block={props.name}
          attributes={attributes}
        />
      </div>
    </Fragment>
  );
}
