import { RichText, useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
  let { content, style } = attributes;
  const classes = style;
  const blockProps = useBlockProps.save( {
    className: classes,
  } );
  const urlbr = /(http|https):\/\/[^\s$.?#].[^\s]*<br\/?>/g;
  const urldiv = /(http|https):\/\/[^\s$.?#].[^\s]*$/g;
  return (
    <div { ...blockProps }>
      <RichText.Content
        value={ content
          .replace( /<\/p><p>/g, '</p>\n<p>' )
          .replace( /^<p>/g, '\n<p>' )
          .replace( /<\/p>$/g, '</p>\n' )
          .replace( /\s+<p>/g, '\n<p>' )
          .replace( /<\p>\s+/g, '<p>\n' )
          .replace( /<br>/g, '\n<br>\n' )
          .replace( /^/g, '\n' )
          .replace( /$/g, '\n' )
          .replace( /\n /g, '\n' )
          // .replace( / \n/g, '\n' )
          .replace( /\n\n/g, '\n' )
          // console.log(content);
          //改行や空白なら変更（置換）してもいいけど文章内容やタグを変更するとエラーになるので注意!
        }
        // multiline={ 'p' }
      />
    </div>
  );
}
