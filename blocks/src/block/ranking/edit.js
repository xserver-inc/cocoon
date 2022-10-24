import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { SelectControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/editor'

export default function edit(props) {
  const { attributes, setAttributes, className } = props;
  const { id } = attributes;

  function createOptions() {
    var options = [];
    console.log(gbItemRankings);
    gbItemRankings.forEach((rank) => {
      if (rank.visible == '1') {
        options.unshift({ value: rank.id, label: rank.title });
      }
    });

    return options;
  }

  const blockProps = useBlockProps({
    className: className,
  });

  var options = createOptions();
  return (
    <Fragment>
      <div {...blockProps}>
        <SelectControl
          label={__('ランキング', THEME_NAME)}
          labelPosition="side"
          value={id}
          onChange={(value) => setAttributes({ id: value })}
          options={options}
        />
        <ServerSideRender
          block={props.name}
          attributes={attributes}
        />
      </div>
    </Fragment>
  );
}
