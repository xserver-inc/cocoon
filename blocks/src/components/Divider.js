// WordPressの内部部品やエディター装飾CSSに依存しない区切り線
export default function Divider() {
	return (
		<hr
			className="cocoon-block-editor-divider"
			style={ {
				border: 0,
				borderBottom: '1px solid currentColor',
				height: 0,
				// WordPressの設定パネルに合わせた上下の余白
				margin: '1.5em 0',
				width: 'auto',
			} }
		/>
	);
}
