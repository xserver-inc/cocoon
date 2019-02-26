registerBlockType( 'my-plugin/my-block', {
    title: 'my-block', //ブロック要素のタイトル,(required)
    description: 'this is a my-block description', //ブロックの説明
    category: 'layout', // common,formatting,layout,widgets,embedから選択(required）
    icon: 'book-alt', //　https://developer.wordpress.org/resource/dashicons/ から指定,
    keywords: [ image, photo, pics ], //ブロック要素検索時のタグキーワード
    styles: [
        // ブロック要素のスタイルを設定
        {
            name: 'default', //.is-style-defaultクラスが生成される
            label: __( 'Rounded' ), //エディターでの表示名
            isDefault: true, //trueでこれがデフォルトスタイルになる
        },
        {
            name: 'squared', //.is-style-squaredクラスが生成される
            label: __( 'Squared' ),
        },
    ],
    attributes: {
        //ブロック要素内のコンテンツをどのように表示させるか、という属性を指定する(https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-attributes/)
        content: {
            type: 'string',
            source: 'html',
            selector: 'p',
        },
        align: {
            type: 'string',
        },
    },
    transforms: {
        //ブロックタイプの変更
        from: [
            //どのブロックタイプから変更できるようにするか
            {
                type: 'block',
                blocks: [ 'core/paragraph' ],
                transform: function( content ) {
                    return createBlock( 'core/heading', {
                        content,
                    } );
                },
            },
        ],
        to: [
            //どのブロックタイプへ変更できるようにするか
            {
                type: 'block',
                blocks: [ 'core/paragraph' ],
                transform: function( content ) {
                    return createBlock( 'core/paragraph', {
                        content,
                    } );
                },
            },
        ],
    },
    parent: [ 'core/columns' ], //ブロック要素を入れ子にする場合、入れ子にできるブロックを指定する
    supports: {
        //save関数で返される要素に対する設定
        align: true, //(default:false) ブロックのalign設定。配列で個別指定も可能 (left, center, right, wide, full)
        alignWide: false, //(default:true)全幅・幅広表示の設定。falseで無効にする
        anchor: true, //(default:false)アンカーリンクの設定。
        customClassName: false, //(default:true)クラス名の設定。有効にするとオリジナルのクラス名を入力する欄が表示される。
        className: false, //(default:true)ブロック要素を作成した際に付く　.wp-block-[ブロック名]で自動生成されるクラス名の設定。
        html: false, //(default:true)HTMLでの編集設定。通常Gutenbergはビジュアル編集ですが、trueでHTML編集が選択可能になります。
        inserter: false, //(default:true)trueで「ブロックの追加」ボタンからこのブロック要素を選択可能にします。falseでブロック要素の選択画面からこのブロック要素を隠します。
        multiple: false, //(default:true)trueでこのブロック要素を一つのページで複数作成可能になります。
        reusable: false, //(default:true)trueで再利用可能なブロックに追加する設定が選択可能になります。
    },
    edit() {
        //エディターでの表示設定
        return 'テスト';
    },
    save() {
        //公開記事での表示設定
        return 'テスト';
    },
} );