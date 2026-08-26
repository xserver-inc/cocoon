# Cocoon設定 データ互換性監査報告書

> マーカー内の静的契約は `scripts/audit-cocoon-settings-data-contract.php` が生成します。マーカー外のブラウザー・テスト証跡は記録時点を添えて手動管理し、再生成では変更しません。実際の設定値、nonce、POST本文、DBダンプは記録しません。

<!-- BEGIN AUTO-GENERATED STATIC DATA CONTRACT -->

## 文書情報

- 静的契約基準の記録日: 2026年8月26日
- 対象ブランチ: `feature/cocoon-settings-design`
- 改修前の基準: `5ac3b6b1e4c01a842774d89ed9ff7fa722e1d0f9`
- 改修後: `--check`実行時の作業ツリー（報告書自身のコミットで変わるHEADは自動生成領域へ埋め込まない）
- 対象画面: `admin.php?page=theme-settings`

## 監査対象ソースの識別

以下は公開ソースコードのSHA-256です。実際の設定値をハッシュ化したものではありません。

| 保護領域 | 改修前SHA-256 | 改修後SHA-256 | 結果 |
| --- | --- | --- | --- |
| 設定フォーム43ファイル | `fbef7e47368ab67533aa02f2c1642eed29e610021e687d90dbcaee18abfa1e72` | `fbef7e47368ab67533aa02f2c1642eed29e610021e687d90dbcaee18abfa1e72` | 一致 |
| 設定保存42ファイル | `6fcee961cf99a00a16c95f33b79cb7759c5478b6a998a8ab781e2a07f4d9dab1` | `6fcee961cf99a00a16c95f33b79cb7759c5478b6a998a8ab781e2a07f4d9dab1` | 一致 |
| 設定読込43ファイル | `ffc7df1ee8808add5cd0871b858776072d5421758622bfb5472916c75884c9be` | `ffc7df1ee8808add5cd0871b858776072d5421758622bfb5472916c75884c9be` | 一致 |
| 共通保存・読込保護ファイル | `c9cbca44f17a3f7e1672a6a48ba19925abfb4067934f47ee2c2d3d1b5b0b12f2` | `c9cbca44f17a3f7e1672a6a48ba19925abfb4067934f47ee2c2d3d1b5b0b12f2` | 一致 |
| `_top-page.php`保存ワークフロー | `b288888276ee720e1be446a8e69b105b521749dd3edbda888979eb51c1fe1f69` | `b288888276ee720e1be446a8e69b105b521749dd3edbda888979eb51c1fe1f69` | 一致 |
| **監査契約全体** | `5cdaa7a90f968905e57d52553b680e83c6af771117773e01c35fd8210d681245` | `5cdaa7a90f968905e57d52553b680e83c6af771117773e01c35fd8210d681245` | **一致** |

## 結論

**表示モード専用user_optionを除き、Cocoon本体設定の保存値・保存先キー・読込呼出・フォーム定義に差分はありません。**

改修前後をPHPトークンで比較した結果、コメントを除いた実行可能な保存呼出、動的SNS設定を展開した全保存キー、読込呼出と既定値、`OP_*`からtheme_modキーへの対応が一致しました。`*-forms.php`、`*-posts.php`、`*-funcs.php`の内容もファイル集合単位で完全一致しています。

許可された例外は、現在の管理者のサイト別`user_option`へ保存する`cocoon_settings_navigation_mode`だけです。これはCocoon本体のtheme_mod、バックアップ、リセット、通常POSTから分離されています。

## 件数の定義

| 名称 | 定義 |
| --- | --- |
| 保存呼出箇所 | 実行可能コード中の`update_theme_option()`記述箇所。SNSの動的ループは各1箇所として数える |
| 有効保存オプション | SNS定義配列の`top_key`・`bottom_key`を展開した、コアが実際に保存する一意の`OP_*`定数 |
| theme_modキー | `define('OP_*', 'key')`から解決した永続化キー |
| 読込呼出 | `*-funcs.php`にある実行可能な`get_theme_option()`呼出 |
| 名前付き要素 | 実DOMで`name`属性を持つフォーム要素 |
| 成功コントロール | disabled、未選択checkbox/radio、submit等を除き、現在のPOSTへ入る要素 |

## 全体結果

| 検証層 | 改修前 | 改修後 | 差分 | 結果 |
| --- | ---: | ---: | ---: | --- |
| 保存include対象ファイル | 41 | 41 | 0 | 一致 |
| `update_theme_option()`呼出箇所 | 518 | 518 | 0 | 一致 |
| 動的SNS定義展開後の保存実行単位 | 542 | 542 | 0 | 一致 |
| 有効保存オプション | 542 | 542 | 0 | 一致 |
| 解決済みtheme_modキー | 542 | 542 | 0 | 全件解決・一致 |
| 一意theme_modキー | 542 | 542 | 0 | 一致 |
| `*-forms.php` | 43 | 43 | 0 | 内容完全一致 |
| `*-posts.php` | 42 | 42 | 0 | 内容完全一致 |
| `*-funcs.php` | 43 | 43 | 0 | 内容完全一致 |
| `get_theme_option()`呼出 | 535 | 535 | 0 | 呼出・既定値一致 |
| 既存の直接`set_theme_mod()` | 1 | 1 | 0 | 一致 |
| リセット呼出 | 1 | 1 | 0 | 一致 |
| 保存・読込共通保護ファイル | 10 | 10 | 0 | 内容完全一致 |
| `_top-page.php`保存ワークフロー | 1 | 1 | 0 | 意味・文字列一致 |
| 許可済み表示モードuser_option | 0 | 1 | +1 | 仕様どおり |

## 設定群別結果

| 設定群 | 保存オプション数 | フォーム契約 | 保存契約 | 読込契約 | 総合結果 |
| --- | ---: | --- | --- | --- | --- |
| リセット | 0 | 一致 | 一致 | 一致 | 合格 |
| 全体 | 18 | 一致 | 一致 | 一致 | 合格 |
| ヘッダー | 21 | 一致 | 一致 | 一致 | 合格 |
| スキン | 2 | 一致 | 一致 | 一致 | 合格 |
| 広告 | 67 | 一致 | 一致 | 一致 | 合格 |
| タイトル | 15 | 一致 | 一致 | 一致 | 合格 |
| SEO | 10 | 一致 | 一致 | 一致 | 合格 |
| OGP | 5 | 一致 | 一致 | 一致 | 合格 |
| アクセス解析・認証 | 8 | 一致 | 一致 | 一致 | 合格 |
| カラム | 11 | 一致 | 一致 | 一致 | 合格 |
| インデックス | 19 | 一致 | 一致 | 一致 | 合格 |
| 投稿 | 27 | 一致 | 一致 | 一致 | 合格 |
| 固定ページ | 3 | 一致 | 一致 | 一致 | 合格 |
| 本文 | 25 | 一致 | 一致 | 一致 | 合格 |
| 目次 | 17 | 一致 | 一致 | 一致 | 合格 |
| SNSシェア | 55 | 一致 | 一致 | 一致 | 合格 |
| SNSフォロー | 16 | 一致 | 一致 | 一致 | 合格 |
| 画像 | 11 | 一致 | 一致 | 一致 | 合格 |
| ブログカード | 13 | 一致 | 一致 | 一致 | 合格 |
| コード | 6 | 一致 | 一致 | 一致 | 合格 |
| コメント | 8 | 一致 | 一致 | 一致 | 合格 |
| 通知 | 7 | 一致 | 一致 | 一致 | 合格 |
| アピールエリア | 12 | 一致 | 一致 | 一致 | 合格 |
| おすすめカード | 5 | 一致 | 一致 | 一致 | 合格 |
| カルーセル | 11 | 一致 | 一致 | 一致 | 合格 |
| フッター | 10 | 一致 | 一致 | 一致 | 合格 |
| ボタン | 5 | 一致 | 一致 | 一致 | 合格 |
| モバイル | 4 | 一致 | 一致 | 一致 | 合格 |
| 404ページ | 3 | 一致 | 一致 | 一致 | 合格 |
| AMP | 9 | 一致 | 一致 | 一致 | 合格 |
| PWA | 9 | 一致 | 一致 | 一致 | 合格 |
| 管理者画面 | 34 | 一致 | 一致 | 一致 | 合格 |
| ウィジェット | 1 | 一致 | 一致 | 一致 | 合格 |
| ウィジェットエリア | 1 | 一致 | 一致 | 一致 | 合格 |
| エディター | 26 | 一致 | 一致 | 一致 | 合格 |
| API | 44 | 一致 | 一致 | 一致 | 合格 |
| その他 | 4 | 一致 | 一致 | 一致 | 合格 |
| **合計** | **542** | **一致** | **一致** | **一致** | **合格** |

## 全設定オプション結果

一行を一意のコア保存オプションとして記載します。`入力参照`は`*-forms.php`内の直接参照ファイル数、`読込参照`は`*-funcs.php`内の直接`get_theme_option()`呼出数です。0件でも、共通helperや動的定義を介する項目があるため不一致を意味しません。

| No. | 設定群 | OP定数 | theme_modキー | 入力参照 | 読込参照 | 保存元 | 改修前 | 改修後 | 結果 |
| ---: | --- | --- | --- | ---: | ---: | --- | --- | --- | --- |
| 1 | 全体 | `OP_SITE_KEY_COLOR` | `site_key_color` | 1 | 1 | lib/page-settings/all-posts.php:11 | 同一 | 同一 | 合格 |
| 2 | 全体 | `OP_SITE_KEY_TEXT_COLOR` | `site_key_text_color` | 1 | 1 | lib/page-settings/all-posts.php:14 | 同一 | 同一 | 合格 |
| 3 | 全体 | `OP_SITE_FONT_FAMILY` | `site_font_family` | 1 | 1 | lib/page-settings/all-posts.php:17 | 同一 | 同一 | 合格 |
| 4 | 全体 | `OP_SITE_FONT_SIZE` | `site_font_size` | 1 | 1 | lib/page-settings/all-posts.php:20 | 同一 | 同一 | 合格 |
| 5 | 全体 | `OP_MOBILE_SITE_FONT_SIZE` | `mobile_site_font_size` | 1 | 1 | lib/page-settings/all-posts.php:23 | 同一 | 同一 | 合格 |
| 6 | 全体 | `OP_SITE_TEXT_COLOR` | `site_text_color` | 1 | 1 | lib/page-settings/all-posts.php:26 | 同一 | 同一 | 合格 |
| 7 | 全体 | `OP_SITE_FONT_WEIGHT` | `site_font_weight` | 1 | 1 | lib/page-settings/all-posts.php:29 | 同一 | 同一 | 合格 |
| 8 | 全体 | `OP_SITE_ICON_FONT` | `site_icon_font` | 2 | 1 | lib/page-settings/all-posts.php:32 | 同一 | 同一 | 合格 |
| 9 | 全体 | `OP_SITE_BACKGROUND_COLOR` | `site_background_color` | 1 | 1 | lib/page-settings/all-posts.php:35 | 同一 | 同一 | 合格 |
| 10 | 全体 | `OP_SITE_LINK_COLOR` | `site_link_color` | 1 | 1 | lib/page-settings/all-posts.php:38 | 同一 | 同一 | 合格 |
| 11 | 全体 | `OP_SITE_SELECTION_COLOR` | `site_selection_color` | 1 | 1 | lib/page-settings/all-posts.php:41 | 同一 | 同一 | 合格 |
| 12 | 全体 | `OP_SITE_SELECTION_BACKGROUND_COLOR` | `site_selection_background_color` | 1 | 1 | lib/page-settings/all-posts.php:44 | 同一 | 同一 | 合格 |
| 13 | 全体 | `OP_SITE_BACKGROUND_IMAGE_URL` | `site_background_image_url` | 1 | 1 | lib/page-settings/all-posts.php:47 | 同一 | 同一 | 合格 |
| 14 | 全体 | `OP_ALIGN_SITE_WIDTH` | `align_site_width` | 1 | 1 | lib/page-settings/all-posts.php:50 | 同一 | 同一 | 合格 |
| 15 | 全体 | `OP_SIDEBAR_POSITION` | `sidebar_position` | 2 | 1 | lib/page-settings/all-posts.php:53 | 同一 | 同一 | 合格 |
| 16 | 全体 | `OP_SIDEBAR_DISPLAY_TYPE` | `sidebar_display_type` | 2 | 1 | lib/page-settings/all-posts.php:56 | 同一 | 同一 | 合格 |
| 17 | 全体 | `OP_ALL_THUMBNAIL_VISIBLE` | `all_thumbnail_visible` | 1 | 1 | lib/page-settings/all-posts.php:62 | 同一 | 同一 | 合格 |
| 18 | 全体 | `OP_SITE_DATE_FORMAT` | `site_date_format` | 1 | 1 | lib/page-settings/all-posts.php:65 | 同一 | 同一 | 合格 |
| 19 | ヘッダー | `OP_HEADER_LAYOUT_TYPE` | `header_layout_type` | 1 | 1 | lib/page-settings/header-posts.php:11 | 同一 | 同一 | 合格 |
| 20 | ヘッダー | `OP_HEADER_FIXED` | `header_fixed` | 1 | 1 | lib/page-settings/header-posts.php:14 | 同一 | 同一 | 合格 |
| 21 | ヘッダー | `OP_HEADER_AREA_HEIGHT` | `header_area_height` | 1 | 1 | lib/page-settings/header-posts.php:17 | 同一 | 同一 | 合格 |
| 22 | ヘッダー | `OP_MOBILE_HEADER_AREA_HEIGHT` | `mobile_header_area_height` | 1 | 1 | lib/page-settings/header-posts.php:20 | 同一 | 同一 | 合格 |
| 23 | ヘッダー | `OP_THE_SITE_LOGO_URL` | `the_site_logo_url` | 1 | 1 | lib/page-settings/header-posts.php:23 | 同一 | 同一 | 合格 |
| 24 | ヘッダー | `OP_THE_FIXED_SITE_LOGO_URL` | `the_fixed_site_logo_url` | 1 | 1 | lib/page-settings/header-posts.php:26 | 同一 | 同一 | 合格 |
| 25 | ヘッダー | `OP_THE_SITE_LOGO_WIDTH` | `the_site_logo_width` | 1 | 1 | lib/page-settings/header-posts.php:29 | 同一 | 同一 | 合格 |
| 26 | ヘッダー | `OP_THE_SITE_LOGO_HEIGHT` | `the_site_logo_height` | 1 | 1 | lib/page-settings/header-posts.php:32 | 同一 | 同一 | 合格 |
| 27 | ヘッダー | `OP_TAGLINE_POSITION` | `tagline_position` | 1 | 1 | lib/page-settings/header-posts.php:35 | 同一 | 同一 | 合格 |
| 28 | ヘッダー | `OP_HEADER_BACKGROUND_IMAGE_URL` | `header_background_image_url` | 1 | 1 | lib/page-settings/header-posts.php:38 | 同一 | 同一 | 合格 |
| 29 | ヘッダー | `OP_HEADER_BACKGROUND_ATTACHMENT_FIXED` | `header_background_attachment_fixed` | 1 | 1 | lib/page-settings/header-posts.php:41 | 同一 | 同一 | 合格 |
| 30 | ヘッダー | `OP_HEADER_SIZE_BACKGROUND_IMAGE_ASPECT_RATIO` | `header_size_background_image_aspect_ratio` | 1 | 1 | lib/page-settings/header-posts.php:44 | 同一 | 同一 | 合格 |
| 31 | ヘッダー | `OP_HEADER_CONTAINER_BACKGROUND_COLOR` | `header_container_background_color` | 1 | 1 | lib/page-settings/header-posts.php:47 | 同一 | 同一 | 合格 |
| 32 | ヘッダー | `OP_HEADER_CONTAINER_TEXT_COLOR` | `header_container_text_color` | 1 | 1 | lib/page-settings/header-posts.php:50 | 同一 | 同一 | 合格 |
| 33 | ヘッダー | `OP_HEADER_BACKGROUND_COLOR` | `header_background_color` | 1 | 1 | lib/page-settings/header-posts.php:53 | 同一 | 同一 | 合格 |
| 34 | ヘッダー | `OP_HEADER_TEXT_COLOR` | `header_text_color` | 1 | 1 | lib/page-settings/header-posts.php:56 | 同一 | 同一 | 合格 |
| 35 | スキン | `OP_SKIN_URL` | `skin_url` | 1 | 1 | lib/page-settings/skin-posts.php:11 | 同一 | 同一 | 合格 |
| 36 | スキン | `OP_INCLUDE_SKIN_TYPE` | `include_skin_type` | 1 | 1 | lib/page-settings/skin-posts.php:14 | 同一 | 同一 | 合格 |
| 37 | ヘッダー | `OP_GLOBAL_NAVI_BACKGROUND_COLOR` | `global_navi_background_color` | 1 | 1 | lib/page-settings/navi-posts.php:11 | 同一 | 同一 | 合格 |
| 38 | ヘッダー | `OP_GLOBAL_NAVI_TEXT_COLOR` | `global_navi_text_color` | 1 | 1 | lib/page-settings/navi-posts.php:14 | 同一 | 同一 | 合格 |
| 39 | ヘッダー | `OP_GLOBAL_NAVI_MENU_WIDTH` | `global_navi_menu_width` | 1 | 1 | lib/page-settings/navi-posts.php:20 | 同一 | 同一 | 合格 |
| 40 | ヘッダー | `OP_GLOBAL_NAVI_MENU_TEXT_WIDTH_ENABLE` | `global_navi_menu_text_width_enable` | 1 | 1 | lib/page-settings/navi-posts.php:23 | 同一 | 同一 | 合格 |
| 41 | ヘッダー | `OP_GLOBAL_NAVI_SUB_MENU_WIDTH` | `global_navi_sub_menu_width` | 1 | 1 | lib/page-settings/navi-posts.php:26 | 同一 | 同一 | 合格 |
| 42 | 広告 | `OP_ALL_ADS_VISIBLE` | `all_ads_visible` | 2 | 1 | lib/page-settings/ads-posts.php:11 | 同一 | 同一 | 合格 |
| 43 | 広告 | `OP_ALL_ADSENSES_VISIBLE` | `all_adsenses_visible` | 1 | 1 | lib/page-settings/ads-posts.php:13 | 同一 | 同一 | 合格 |
| 44 | 広告 | `OP_AD_CODE` | `ad_code` | 1 | 1 | lib/page-settings/ads-posts.php:15 | 同一 | 同一 | 合格 |
| 45 | 広告 | `OP_AD_LABEL_CAPTION` | `ad_label_caption` | 1 | 1 | lib/page-settings/ads-posts.php:17 | 同一 | 同一 | 合格 |
| 46 | 広告 | `OP_ADSENSE_DISPLAY_METHOD` | `adsense_display_method` | 2 | 0 | lib/page-settings/ads-posts.php:21 | 同一 | 同一 | 合格 |
| 47 | 広告 | `OP_MOBILE_ADSENSE_WIDTH_WIDE` | `mobile_adsense_width_wide` | 1 | 1 | lib/page-settings/ads-posts.php:23 | 同一 | 同一 | 合格 |
| 48 | 広告 | `OP_AD_POS_INDEX_TOP_VISIBLE` | `ad_pos_index_top_visible` | 1 | 1 | lib/page-settings/ads-posts.php:26 | 同一 | 同一 | 合格 |
| 49 | 広告 | `OP_AD_POS_INDEX_TOP_FORMAT` | `ad_pos_index_top_format` | 1 | 1 | lib/page-settings/ads-posts.php:28 | 同一 | 同一 | 合格 |
| 50 | 広告 | `OP_AD_POS_INDEX_TOP_LABEL_VISIBLE` | `ad_pos_index_top_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:30 | 同一 | 同一 | 合格 |
| 51 | 広告 | `OP_AD_POS_INDEX_MIDDLE_VISIBLE` | `ad_pos_index_middle_visible` | 1 | 1 | lib/page-settings/ads-posts.php:33 | 同一 | 同一 | 合格 |
| 52 | 広告 | `OP_AD_POS_INDEX_MIDDLE_FORMAT` | `ad_pos_index_middle_format` | 1 | 1 | lib/page-settings/ads-posts.php:35 | 同一 | 同一 | 合格 |
| 53 | 広告 | `OP_AD_POS_INDEX_MIDDLE_LABEL_VISIBLE` | `ad_pos_index_middle_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:37 | 同一 | 同一 | 合格 |
| 54 | 広告 | `OP_AD_POS_INDEX_BOTTOM_VISIBLE` | `ad_pos_index_bottom_visible` | 1 | 1 | lib/page-settings/ads-posts.php:40 | 同一 | 同一 | 合格 |
| 55 | 広告 | `OP_AD_POS_INDEX_BOTTOM_FORMAT` | `ad_pos_index_bottom_format` | 1 | 1 | lib/page-settings/ads-posts.php:42 | 同一 | 同一 | 合格 |
| 56 | 広告 | `OP_AD_POS_INDEX_BOTTOM_LABEL_VISIBLE` | `ad_pos_index_bottom_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:44 | 同一 | 同一 | 合格 |
| 57 | 広告 | `OP_AD_POS_SIDEBAR_TOP_VISIBLE` | `ad_pos_sidebar_top_visible` | 1 | 1 | lib/page-settings/ads-posts.php:47 | 同一 | 同一 | 合格 |
| 58 | 広告 | `OP_AD_POS_SIDEBAR_TOP_FORMAT` | `ad_pos_sidebar_top_format` | 1 | 1 | lib/page-settings/ads-posts.php:49 | 同一 | 同一 | 合格 |
| 59 | 広告 | `OP_AD_POS_SIDEBAR_TOP_LABEL_VISIBLE` | `ad_pos_sidebar_top_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:51 | 同一 | 同一 | 合格 |
| 60 | 広告 | `OP_AD_POS_SIDEBAR_BOTTOM_VISIBLE` | `ad_pos_sidebar_bottom_visible` | 1 | 1 | lib/page-settings/ads-posts.php:54 | 同一 | 同一 | 合格 |
| 61 | 広告 | `OP_AD_POS_SIDEBAR_BOTTOM_FORMAT` | `ad_pos_sidebar_bottom_format` | 1 | 1 | lib/page-settings/ads-posts.php:56 | 同一 | 同一 | 合格 |
| 62 | 広告 | `OP_AD_POS_SIDEBAR_BOTTOM_LABEL_VISIBLE` | `ad_pos_sidebar_bottom_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:58 | 同一 | 同一 | 合格 |
| 63 | 広告 | `OP_AD_POS_ABOVE_TITLE_VISIBLE` | `ad_pos_above_title_visible` | 1 | 1 | lib/page-settings/ads-posts.php:61 | 同一 | 同一 | 合格 |
| 64 | 広告 | `OP_AD_POS_ABOVE_TITLE_FORMAT` | `ad_pos_above_title_format` | 1 | 1 | lib/page-settings/ads-posts.php:63 | 同一 | 同一 | 合格 |
| 65 | 広告 | `OP_AD_POS_ABOVE_TITLE_LABEL_VISIBLE` | `ad_pos_above_title_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:65 | 同一 | 同一 | 合格 |
| 66 | 広告 | `OP_AD_POS_BELOW_TITLE_VISIBLE` | `ad_pos_below_title_visible` | 1 | 1 | lib/page-settings/ads-posts.php:68 | 同一 | 同一 | 合格 |
| 67 | 広告 | `OP_AD_POS_BELOW_TITLE_FORMAT` | `ad_pos_below_title_format` | 1 | 1 | lib/page-settings/ads-posts.php:70 | 同一 | 同一 | 合格 |
| 68 | 広告 | `OP_AD_POS_BELOW_TITLE_LABEL_VISIBLE` | `ad_pos_below_title_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:72 | 同一 | 同一 | 合格 |
| 69 | 広告 | `OP_AD_POS_CONTENT_TOP_VISIBLE` | `ad_pos_content_top_visible` | 1 | 1 | lib/page-settings/ads-posts.php:75 | 同一 | 同一 | 合格 |
| 70 | 広告 | `OP_AD_POS_CONTENT_TOP_FORMAT` | `ad_pos_content_top_format` | 1 | 1 | lib/page-settings/ads-posts.php:77 | 同一 | 同一 | 合格 |
| 71 | 広告 | `OP_AD_POS_CONTENT_TOP_LABEL_VISIBLE` | `ad_pos_content_top_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:79 | 同一 | 同一 | 合格 |
| 72 | 広告 | `OP_AD_POS_CONTENT_MIDDLE_VISIBLE` | `ad_pos_content_middle_visible` | 1 | 1 | lib/page-settings/ads-posts.php:82 | 同一 | 同一 | 合格 |
| 73 | 広告 | `OP_AD_POS_CONTENT_MIDDLE_FORMAT` | `ad_pos_content_middle_format` | 1 | 1 | lib/page-settings/ads-posts.php:84 | 同一 | 同一 | 合格 |
| 74 | 広告 | `OP_AD_POS_CONTENT_MIDDLE_LABEL_VISIBLE` | `ad_pos_content_middle_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:86 | 同一 | 同一 | 合格 |
| 75 | 広告 | `OP_AD_POS_ALL_CONTENT_MIDDLE_VISIBLE` | `ad_pos_all_content_middle_visible` | 1 | 1 | lib/page-settings/ads-posts.php:88 | 同一 | 同一 | 合格 |
| 76 | 広告 | `OP_AD_POS_CONTENT_MIDDLE_COUNT` | `ad_pos_content_middle_count` | 0 | 1 | lib/page-settings/ads-posts.php:90 | 同一 | 同一 | 合格 |
| 77 | 広告 | `OP_AD_POS_CONTENT_BOTTOM_VISIBLE` | `ad_pos_content_bottom_visible` | 1 | 1 | lib/page-settings/ads-posts.php:93 | 同一 | 同一 | 合格 |
| 78 | 広告 | `OP_AD_POS_CONTENT_BOTTOM_FORMAT` | `ad_pos_content_bottom_format` | 1 | 1 | lib/page-settings/ads-posts.php:95 | 同一 | 同一 | 合格 |
| 79 | 広告 | `OP_AD_POS_CONTENT_BOTTOM_LABEL_VISIBLE` | `ad_pos_content_bottom_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:97 | 同一 | 同一 | 合格 |
| 80 | 広告 | `OP_AD_POS_ABOVE_SNS_BUTTONS_VISIBLE` | `ad_pos_above_sns_buttons_visible` | 1 | 1 | lib/page-settings/ads-posts.php:100 | 同一 | 同一 | 合格 |
| 81 | 広告 | `OP_AD_POS_ABOVE_SNS_BUTTONS_FORMAT` | `ad_pos_above_sns_buttons_format` | 1 | 1 | lib/page-settings/ads-posts.php:102 | 同一 | 同一 | 合格 |
| 82 | 広告 | `OP_AD_POS_ABOVE_SNS_BUTTONS_LABEL_VISIBLE` | `ad_pos_above_sns_buttons_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:104 | 同一 | 同一 | 合格 |
| 83 | 広告 | `OP_AD_POS_BELOW_SNS_BUTTONS_VISIBLE` | `ad_pos_below_sns_buttons_visible` | 1 | 1 | lib/page-settings/ads-posts.php:107 | 同一 | 同一 | 合格 |
| 84 | 広告 | `OP_AD_POS_BELOW_SNS_BUTTONS_FORMAT` | `ad_pos_below_sns_buttons_format` | 1 | 1 | lib/page-settings/ads-posts.php:109 | 同一 | 同一 | 合格 |
| 85 | 広告 | `OP_AD_POS_BELOW_SNS_BUTTONS_LABEL_VISIBLE` | `ad_pos_below_sns_buttons_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:111 | 同一 | 同一 | 合格 |
| 86 | 広告 | `OP_AD_POS_BELOW_RELATED_POSTS_VISIBLE` | `ad_pos_below_related_posts_visible` | 1 | 1 | lib/page-settings/ads-posts.php:114 | 同一 | 同一 | 合格 |
| 87 | 広告 | `OP_AD_POS_BELOW_RELATED_POSTS_FORMAT` | `ad_pos_below_related_posts_format` | 1 | 1 | lib/page-settings/ads-posts.php:116 | 同一 | 同一 | 合格 |
| 88 | 広告 | `OP_AD_POS_BELOW_RELATED_POSTS_LABEL_VISIBLE` | `ad_pos_below_related_posts_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:118 | 同一 | 同一 | 合格 |
| 89 | 広告 | `OP_AD_SHORTCODE_ENABLE` | `ad_shortcode_enable` | 1 | 1 | lib/page-settings/ads-posts.php:121 | 同一 | 同一 | 合格 |
| 90 | 広告 | `OP_AD_SHORTCODE_FORMAT` | `ad_shortcode_format` | 1 | 1 | lib/page-settings/ads-posts.php:123 | 同一 | 同一 | 合格 |
| 91 | 広告 | `OP_AD_SHORTCODE_LABEL_VISIBLE` | `ad_shortcode_label_visible` | 1 | 1 | lib/page-settings/ads-posts.php:125 | 同一 | 同一 | 合格 |
| 92 | 広告 | `OP_AD_ADS_TXT_ENABLE` | `ad_ads_txt_enable` | 1 | 1 | lib/page-settings/ads-posts.php:128 | 同一 | 同一 | 合格 |
| 93 | 広告 | `OP_AD_ADS_TXT_CONTENT` | `ad_ads_txt_content` | 1 | 1 | lib/page-settings/ads-posts.php:130 | 同一 | 同一 | 合格 |
| 94 | 広告 | `OP_PR_LABEL_SINGLE_VISIBLE` | `pr_label_single_visible` | 1 | 1 | lib/page-settings/ads-posts.php:133 | 同一 | 同一 | 合格 |
| 95 | 広告 | `OP_PR_LABEL_PAGE_VISIBLE` | `pr_label_page_visible` | 1 | 1 | lib/page-settings/ads-posts.php:135 | 同一 | 同一 | 合格 |
| 96 | 広告 | `OP_PR_LABEL_CATEGORY_PAGE_VISIBLE` | `pr_label_category_page_visible` | 1 | 1 | lib/page-settings/ads-posts.php:137 | 同一 | 同一 | 合格 |
| 97 | 広告 | `OP_PR_LABEL_TAG_PAGE_VISIBLE` | `pr_label_tag_page_visible` | 1 | 1 | lib/page-settings/ads-posts.php:139 | 同一 | 同一 | 合格 |
| 98 | 広告 | `OP_PR_LABEL_SMALL_VISIBLE` | `pr_label_small_visible` | 1 | 1 | lib/page-settings/ads-posts.php:141 | 同一 | 同一 | 合格 |
| 99 | 広告 | `OP_PR_LABEL_LARGE_VISIBLE` | `pr_label_large_visible` | 1 | 1 | lib/page-settings/ads-posts.php:143 | 同一 | 同一 | 合格 |
| 100 | 広告 | `OP_PR_LABEL_SMALL_CAPTION` | `pr_label_small_caption` | 1 | 1 | lib/page-settings/ads-posts.php:145 | 同一 | 同一 | 合格 |
| 101 | 広告 | `OP_PR_LABEL_LARGE_CAPTION` | `pr_label_large_caption` | 1 | 1 | lib/page-settings/ads-posts.php:147 | 同一 | 同一 | 合格 |
| 102 | 広告 | `OP_PR_LABEL_EXCLUDE_POST_IDS` | `pr_label_exclude_post_ids` | 1 | 1 | lib/page-settings/ads-posts.php:149 | 同一 | 同一 | 合格 |
| 103 | 広告 | `OP_PR_LABEL_EXCLUDE_CATEGORY_IDS` | `pr_label_exclude_category_ids` | 1 | 1 | lib/page-settings/ads-posts.php:151 | 同一 | 同一 | 合格 |
| 104 | 広告 | `OP_PR_LABEL_EXCLUDE_TAG_IDS` | `pr_label_exclude_tag_ids` | 1 | 1 | lib/page-settings/ads-posts.php:153 | 同一 | 同一 | 合格 |
| 105 | 広告 | `OP_AD_LINKSWITCH_ENABLE` | `ad_linkswitch_enable` | 1 | 1 | lib/page-settings/ads-posts.php:156 | 同一 | 同一 | 合格 |
| 106 | 広告 | `OP_AD_LINKSWITCH_ID` | `ad_linkswitch_id` | 1 | 1 | lib/page-settings/ads-posts.php:158 | 同一 | 同一 | 合格 |
| 107 | 広告 | `OP_AD_EXCLUDE_POST_IDS` | `ad_exclude_post_ids` | 1 | 1 | lib/page-settings/ads-posts.php:161 | 同一 | 同一 | 合格 |
| 108 | 広告 | `OP_AD_EXCLUDE_CATEGORY_IDS` | `ad_exclude_category_ids` | 1 | 1 | lib/page-settings/ads-posts.php:163 | 同一 | 同一 | 合格 |
| 109 | タイトル | `OP_FRONT_PAGE_TITLE_FORMAT` | `front_page_title_format` | 1 | 1 | lib/page-settings/title-posts.php:11 | 同一 | 同一 | 合格 |
| 110 | タイトル | `OP_FREE_FRONT_PAGE_TITLE` | `free_front_page_title` | 1 | 1 | lib/page-settings/title-posts.php:14 | 同一 | 同一 | 合格 |
| 111 | タイトル | `OP_META_DESCRIPTION_TO_FRONT_PAGE` | `meta_description_to_front_page` | 1 | 1 | lib/page-settings/title-posts.php:17 | 同一 | 同一 | 合格 |
| 112 | タイトル | `OP_FRONT_PAGE_META_DESCRIPTION` | `front_page_meta_description` | 1 | 1 | lib/page-settings/title-posts.php:20 | 同一 | 同一 | 合格 |
| 113 | タイトル | `OP_META_KEYWORDS_TO_FRONT_PAGE` | `meta_keywords_to_front_page` | 1 | 1 | lib/page-settings/title-posts.php:23 | 同一 | 同一 | 合格 |
| 114 | タイトル | `OP_FRONT_PAGE_META_KEYWORDS` | `front_page_meta_keywords` | 1 | 1 | lib/page-settings/title-posts.php:26 | 同一 | 同一 | 合格 |
| 115 | タイトル | `OP_SINGULAR_PAGE_TITLE_FORMAT` | `singular_page_title_format` | 1 | 1 | lib/page-settings/title-posts.php:29 | 同一 | 同一 | 合格 |
| 116 | タイトル | `OP_META_DESCRIPTION_TO_SINGULAR` | `meta_description_to_singular` | 1 | 1 | lib/page-settings/title-posts.php:32 | 同一 | 同一 | 合格 |
| 117 | タイトル | `OP_META_KEYWORDS_TO_SINGULAR` | `meta_keywords_to_singular` | 1 | 1 | lib/page-settings/title-posts.php:35 | 同一 | 同一 | 合格 |
| 118 | タイトル | `OP_CATEGORY_PAGE_TITLE_FORMAT` | `category_page_title_format` | 1 | 1 | lib/page-settings/title-posts.php:38 | 同一 | 同一 | 合格 |
| 119 | タイトル | `OP_META_DESCRIPTION_TO_CATEGORY` | `meta_description_to_category` | 1 | 1 | lib/page-settings/title-posts.php:41 | 同一 | 同一 | 合格 |
| 120 | タイトル | `OP_META_KEYWORDS_TO_CATEGORY` | `meta_keywords_to_category` | 1 | 1 | lib/page-settings/title-posts.php:44 | 同一 | 同一 | 合格 |
| 121 | タイトル | `OP_SEO_DATE_TYPE` | `seo_date_type` | 1 | 1 | lib/page-settings/title-posts.php:47 | 同一 | 同一 | 合格 |
| 122 | タイトル | `OP_SIMPLIFIED_SITE_NAME` | `simplified_site_name` | 1 | 1 | lib/page-settings/title-posts.php:50 | 同一 | 同一 | 合格 |
| 123 | タイトル | `OP_TITLE_SEPARATOR` | `title_separator` | 1 | 1 | lib/page-settings/title-posts.php:53 | 同一 | 同一 | 合格 |
| 124 | SEO | `OP_CANONICAL_TAG_ENABLE` | `canonical_tag_enable` | 2 | 1 | lib/page-settings/seo-posts.php:11 | 同一 | 同一 | 合格 |
| 125 | SEO | `OP_PREV_NEXT_ENABLE` | `prev_next_enable` | 1 | 1 | lib/page-settings/seo-posts.php:14 | 同一 | 同一 | 合格 |
| 126 | SEO | `OP_CATEGORY_PAGE_NOINDEX` | `category_page_noindex` | 2 | 1 | lib/page-settings/seo-posts.php:17 | 同一 | 同一 | 合格 |
| 127 | SEO | `OP_PAGED_CATEGORY_PAGE_NOINDEX` | `paged_category_page_noindex` | 1 | 1 | lib/page-settings/seo-posts.php:20 | 同一 | 同一 | 合格 |
| 128 | SEO | `OP_TAG_PAGE_NOINDEX` | `tag_page_noindex` | 2 | 1 | lib/page-settings/seo-posts.php:23 | 同一 | 同一 | 合格 |
| 129 | SEO | `OP_PAGED_TAG_PAGE_NOINDEX` | `paged_tag_page_noindex` | 1 | 1 | lib/page-settings/seo-posts.php:26 | 同一 | 同一 | 合格 |
| 130 | SEO | `OP_OTHER_ARCHIVE_PAGE_NOINDEX` | `other_archive_page_noindex` | 1 | 1 | lib/page-settings/seo-posts.php:29 | 同一 | 同一 | 合格 |
| 131 | SEO | `OP_ATTACHMENT_PAGE_NOINDEX` | `attachment_page_noindex` | 1 | 1 | lib/page-settings/seo-posts.php:32 | 同一 | 同一 | 合格 |
| 132 | SEO | `OP_JSON_LD_TAG_ENABLE` | `json_ld_tag_enable` | 1 | 1 | lib/page-settings/seo-posts.php:35 | 同一 | 同一 | 合格 |
| 133 | SEO | `OP_META_REFERRER_CONTENT` | `meta_referrer_content` | 1 | 1 | lib/page-settings/seo-posts.php:38 | 同一 | 同一 | 合格 |
| 134 | OGP | `OP_FACEBOOK_OGP_ENABLE` | `facebook_ogp_enable` | 1 | 1 | lib/page-settings/ogp-posts.php:11 | 同一 | 同一 | 合格 |
| 135 | OGP | `OP_FACEBOOK_APP_ID` | `facebook_app_id` | 1 | 1 | lib/page-settings/ogp-posts.php:14 | 同一 | 同一 | 合格 |
| 136 | OGP | `OP_TWITTER_CARD_ENABLE` | `twitter_card_enable` | 1 | 1 | lib/page-settings/ogp-posts.php:17 | 同一 | 同一 | 合格 |
| 137 | OGP | `OP_TWITTER_CARD_TYPE` | `twitter_card_type` | 1 | 1 | lib/page-settings/ogp-posts.php:20 | 同一 | 同一 | 合格 |
| 138 | OGP | `OP_OGP_HOME_IMAGE_URL` | `ogp_home_image_url` | 2 | 1 | lib/page-settings/ogp-posts.php:23 | 同一 | 同一 | 合格 |
| 139 | アクセス解析・認証 | `OP_ANALYTICS_ADMIN_INCLUDE` | `analytics_admin_include` | 1 | 1 | lib/page-settings/analytics-posts.php:11 | 同一 | 同一 | 合格 |
| 140 | アクセス解析・認証 | `OP_GOOGLE_TAG_MANAGER_TRACKING_ID` | `google_tag_manager_tracking_id` | 2 | 1 | lib/page-settings/analytics-posts.php:14 | 同一 | 同一 | 合格 |
| 141 | アクセス解析・認証 | `OP_GA4_TRACKING_ID` | `ga4_tracking_id` | 2 | 1 | lib/page-settings/analytics-posts.php:17 | 同一 | 同一 | 合格 |
| 142 | アクセス解析・認証 | `OP_GOOGLE_SEARCH_CONSOLE_ID` | `google_search_console_id` | 2 | 1 | lib/page-settings/analytics-posts.php:20 | 同一 | 同一 | 合格 |
| 143 | アクセス解析・認証 | `OP_CLARITY_PROJECT_ID` | `clarity_project_id` | 1 | 1 | lib/page-settings/analytics-posts.php:23 | 同一 | 同一 | 合格 |
| 144 | アクセス解析・認証 | `OP_OTHER_ANALYTICS_HEAD_TAGS` | `other_analytics_head_tags` | 1 | 1 | lib/page-settings/analytics-posts.php:26 | 同一 | 同一 | 合格 |
| 145 | アクセス解析・認証 | `OP_OTHER_ANALYTICS_HEADER_TAGS` | `other_analytics_header_tags` | 1 | 1 | lib/page-settings/analytics-posts.php:29 | 同一 | 同一 | 合格 |
| 146 | アクセス解析・認証 | `OP_OTHER_ANALYTICS_FOOTER_TAGS` | `other_analytics_footer_tags` | 1 | 1 | lib/page-settings/analytics-posts.php:32 | 同一 | 同一 | 合格 |
| 147 | カラム | `OP_MAIN_COLUMN_CONTENTS_WIDTH` | `main_column_contents_width` | 2 | 1 | lib/page-settings/column-posts.php:15 | 同一 | 同一 | 合格 |
| 148 | カラム | `OP_MAIN_COLUMN_MARGIN` | `main_column_margin` | 0 | 1 | lib/page-settings/column-posts.php:18 | 同一 | 同一 | 合格 |
| 149 | カラム | `OP_MAIN_COLUMN_PADDING` | `main_column_padding` | 1 | 1 | lib/page-settings/column-posts.php:21 | 同一 | 同一 | 合格 |
| 150 | カラム | `OP_MAIN_COLUMN_BORDER_WIDTH` | `main_column_border_width` | 1 | 1 | lib/page-settings/column-posts.php:24 | 同一 | 同一 | 合格 |
| 151 | カラム | `OP_MAIN_COLUMN_BORDER_COLOR` | `main_column_border_color` | 1 | 1 | lib/page-settings/column-posts.php:27 | 同一 | 同一 | 合格 |
| 152 | カラム | `OP_SIDEBAR_CONTENTS_WIDTH` | `sidebar_contents_width` | 2 | 1 | lib/page-settings/column-posts.php:34 | 同一 | 同一 | 合格 |
| 153 | カラム | `OP_SIDEBAR_MARGIN` | `sidebar_margin` | 0 | 1 | lib/page-settings/column-posts.php:37 | 同一 | 同一 | 合格 |
| 154 | カラム | `OP_SIDEBAR_PADDING` | `sidebar_padding` | 1 | 1 | lib/page-settings/column-posts.php:40 | 同一 | 同一 | 合格 |
| 155 | カラム | `OP_SIDEBAR_BORDER_WIDTH` | `sidebar_border_width` | 1 | 1 | lib/page-settings/column-posts.php:43 | 同一 | 同一 | 合格 |
| 156 | カラム | `OP_SIDEBAR_BORDER_COLOR` | `sidebar_border_color` | 1 | 1 | lib/page-settings/column-posts.php:46 | 同一 | 同一 | 合格 |
| 157 | カラム | `OP_MAIN_SIDEBAR_MARGIN` | `main_sidebar_margin` | 1 | 1 | lib/page-settings/column-posts.php:50 | 同一 | 同一 | 合格 |
| 158 | インデックス | `OP_FRONT_PAGE_TYPE` | `front_page_type` | 1 | 1 | lib/page-settings/index-posts.php:11 | 同一 | 同一 | 合格 |
| 159 | インデックス | `OP_INDEX_CATEGORY_IDS` | `index_category_ids` | 1 | 1 | lib/page-settings/index-posts.php:14 | 同一 | 同一 | 合格 |
| 160 | インデックス | `OP_INDEX_CATEGORY_IDS_COMMA_TEXT` | `index_category_ids_comma_text` | 1 | 1 | lib/page-settings/index-posts.php:17 | 同一 | 同一 | 合格 |
| 161 | インデックス | `OP_INDEX_NEW_ENTRY_CARD_COUNT` | `index_new_entry_card_count` | 1 | 1 | lib/page-settings/index-posts.php:20 | 同一 | 同一 | 合格 |
| 162 | インデックス | `OP_INDEX_CATEGORY_ENTRY_CARD_COUNT` | `index_category_entry_card_count` | 1 | 1 | lib/page-settings/index-posts.php:23 | 同一 | 同一 | 合格 |
| 163 | インデックス | `OP_INDEX_SORT_ORDERBY` | `index_sort_orderby` | 1 | 1 | lib/page-settings/index-posts.php:26 | 同一 | 同一 | 合格 |
| 164 | インデックス | `OP_ENTRY_CARD_TYPE` | `entry_card_type` | 1 | 1 | lib/page-settings/index-posts.php:29 | 同一 | 同一 | 合格 |
| 165 | インデックス | `OP_SMARTPHONE_ENTRY_CARD_1_COLUMN` | `smartphone_entry_card_1_column` | 1 | 1 | lib/page-settings/index-posts.php:32 | 同一 | 同一 | 合格 |
| 166 | インデックス | `OP_ENTRY_CARD_BORDER_VISIBLE` | `entry_card_border_visible` | 1 | 1 | lib/page-settings/index-posts.php:35 | 同一 | 同一 | 合格 |
| 167 | インデックス | `OP_ENTRY_CARD_EXCERPT_MAX_LENGTH` | `entry_card_excerpt_max_length` | 1 | 1 | lib/page-settings/index-posts.php:38 | 同一 | 同一 | 合格 |
| 168 | インデックス | `OP_ENTRY_CARD_EXCERPT_MORE` | `entry_card_excerpt_more` | 1 | 1 | lib/page-settings/index-posts.php:41 | 同一 | 同一 | 合格 |
| 169 | インデックス | `OP_ENTRY_CARD_SNIPPET_VISIBLE` | `entry_card_snippet_visible` | 1 | 1 | lib/page-settings/index-posts.php:48 | 同一 | 同一 | 合格 |
| 170 | インデックス | `OP_SMARTPHONE_ENTRY_CARD_SNIPPET_VISIBLE` | `smartphone_entry_card_snippet_visible` | 1 | 1 | lib/page-settings/index-posts.php:51 | 同一 | 同一 | 合格 |
| 171 | インデックス | `OP_ENTRY_CARD_POST_DATE_VISIBLE` | `entry_card_post_date_visible` | 1 | 1 | lib/page-settings/index-posts.php:54 | 同一 | 同一 | 合格 |
| 172 | インデックス | `OP_ENTRY_CARD_POST_DATE_OR_UPDATE_VISIBLE` | `entry_card_post_date_or_update_visible` | 0 | 0 | lib/page-settings/index-posts.php:57 | 同一 | 同一 | 合格 |
| 173 | インデックス | `OP_ENTRY_CARD_POST_UPDATE_VISIBLE` | `entry_card_post_update_visible` | 1 | 1 | lib/page-settings/index-posts.php:60 | 同一 | 同一 | 合格 |
| 174 | インデックス | `OP_ENTRY_CARD_POST_AUTHOR_VISIBLE` | `entry_card_post_author_visible` | 1 | 1 | lib/page-settings/index-posts.php:63 | 同一 | 同一 | 合格 |
| 175 | インデックス | `OP_ENTRY_CARD_POST_COMMENT_COUNT_VISIBLE` | `entry_card_post_comment_count_visible` | 1 | 1 | lib/page-settings/index-posts.php:66 | 同一 | 同一 | 合格 |
| 176 | インデックス | `OP_ARCHIVE_EXCLUDE_CATEGORY_IDS` | `archive_exclude_category_ids` | 1 | 1 | lib/page-settings/index-posts.php:69 | 同一 | 同一 | 合格 |
| 177 | 投稿 | `OP_CATEGORY_TAG_DISPLAY_TYPE` | `category_tag_display_type` | 1 | 1 | lib/page-settings/single-posts.php:14 | 同一 | 同一 | 合格 |
| 178 | 投稿 | `OP_CATEGORY_TAG_DISPLAY_POSITION` | `category_tag_display_position` | 1 | 1 | lib/page-settings/single-posts.php:17 | 同一 | 同一 | 合格 |
| 179 | 投稿 | `OP_RELATED_ENTRIES_VISIBLE` | `related_entries_visible` | 1 | 1 | lib/page-settings/single-posts.php:20 | 同一 | 同一 | 合格 |
| 180 | 投稿 | `OP_RELATED_ASSOCIATION_TYPE` | `related_association_type` | 1 | 1 | lib/page-settings/single-posts.php:23 | 同一 | 同一 | 合格 |
| 181 | 投稿 | `OP_RELATED_ENTRY_HEADING` | `related_entry_heading` | 1 | 1 | lib/page-settings/single-posts.php:26 | 同一 | 同一 | 合格 |
| 182 | 投稿 | `OP_RELATED_ENTRY_SUB_HEADING` | `related_entry_sub_heading` | 1 | 1 | lib/page-settings/single-posts.php:29 | 同一 | 同一 | 合格 |
| 183 | 投稿 | `OP_RELATED_ENTRY_COUNT` | `related_entry_count` | 1 | 1 | lib/page-settings/single-posts.php:32 | 同一 | 同一 | 合格 |
| 184 | 投稿 | `OP_RELATED_ENTRY_PERIOD` | `related_entry_period` | 1 | 1 | lib/page-settings/single-posts.php:35 | 同一 | 同一 | 合格 |
| 185 | 投稿 | `OP_RELATED_ENTRY_TYPE` | `related_entry_type` | 1 | 1 | lib/page-settings/single-posts.php:38 | 同一 | 同一 | 合格 |
| 186 | 投稿 | `OP_RELATED_ENTRY_BORDER_VISIBLE` | `related_entry_border_visible` | 1 | 1 | lib/page-settings/single-posts.php:41 | 同一 | 同一 | 合格 |
| 187 | 投稿 | `OP_RELATED_EXCERPT_MAX_LENGTH` | `related_excerpt_max_length` | 1 | 1 | lib/page-settings/single-posts.php:44 | 同一 | 同一 | 合格 |
| 188 | 投稿 | `OP_RELATED_ENTRY_CARD_SNIPPET_VISIBLE` | `related_entry_card_snippet_visible` | 1 | 1 | lib/page-settings/single-posts.php:47 | 同一 | 同一 | 合格 |
| 189 | 投稿 | `OP_SMARTPHONE_RELATED_ENTRY_CARD_SNIPPET_VISIBLE` | `smartphone_related_entry_card_snippet_visible` | 1 | 1 | lib/page-settings/single-posts.php:50 | 同一 | 同一 | 合格 |
| 190 | 投稿 | `OP_RELATED_ENTRY_CARD_POST_DATE_VISIBLE` | `related_entry_card_post_date_visible` | 1 | 1 | lib/page-settings/single-posts.php:53 | 同一 | 同一 | 合格 |
| 191 | 投稿 | `OP_RELATED_ENTRY_CARD_POST_DATE_OR_UPDATE_VISIBLE` | `related_entry_card_post_date_or_update_visible` | 0 | 0 | lib/page-settings/single-posts.php:56 | 同一 | 同一 | 合格 |
| 192 | 投稿 | `OP_RELATED_ENTRY_CARD_POST_UPDATE_VISIBLE` | `related_entry_card_post_update_visible` | 1 | 1 | lib/page-settings/single-posts.php:59 | 同一 | 同一 | 合格 |
| 193 | 投稿 | `OP_RELATED_ENTRY_CARD_POST_AUTHOR_VISIBLE` | `related_entry_card_post_author_visible` | 1 | 1 | lib/page-settings/single-posts.php:62 | 同一 | 同一 | 合格 |
| 194 | 投稿 | `OP_POST_NAVI_VISIBLE` | `post_navi_visible` | 1 | 1 | lib/page-settings/single-posts.php:68 | 同一 | 同一 | 合格 |
| 195 | 投稿 | `OP_POST_NAVI_TYPE` | `post_navi_type` | 1 | 1 | lib/page-settings/single-posts.php:71 | 同一 | 同一 | 合格 |
| 196 | 投稿 | `OP_POST_NAVI_POSITION` | `post_navi_position` | 1 | 1 | lib/page-settings/single-posts.php:74 | 同一 | 同一 | 合格 |
| 197 | 投稿 | `OP_POST_NAVI_SAME_CATEGORY_ENABLE` | `post_navi_same_category_enable` | 1 | 1 | lib/page-settings/single-posts.php:77 | 同一 | 同一 | 合格 |
| 198 | 投稿 | `OP_POST_NAVI_EXCLUDE_CATEGORY_IDS` | `post_navi_exclude_category_ids` | 1 | 1 | lib/page-settings/single-posts.php:80 | 同一 | 同一 | 合格 |
| 199 | 投稿 | `OP_POST_NAVI_BORDER_VISIBLE` | `post_navi_border_visible` | 1 | 1 | lib/page-settings/single-posts.php:83 | 同一 | 同一 | 合格 |
| 200 | 投稿 | `OP_SINGLE_COMMENT_VISIBLE` | `single_comment_visible` | 1 | 1 | lib/page-settings/single-posts.php:91 | 同一 | 同一 | 合格 |
| 201 | 投稿 | `OP_SINGLE_BREADCRUMBS_POSITION` | `single_breadcrumbs_position` | 1 | 1 | lib/page-settings/single-posts.php:98 | 同一 | 同一 | 合格 |
| 202 | 投稿 | `OP_SINGLE_BREADCRUMBS_INCLUDE_POST` | `single_breadcrumbs_include_post` | 1 | 1 | lib/page-settings/single-posts.php:101 | 同一 | 同一 | 合格 |
| 203 | 投稿 | `OP_SINGLE_BREADCRUMBS_CATEGORY_PRIORITY` | `single_breadcrumbs_category_priority` | 1 | 1 | lib/page-settings/single-posts.php:104 | 同一 | 同一 | 合格 |
| 204 | 固定ページ | `OP_PAGE_COMMENT_VISIBLE` | `page_comment_visible` | 1 | 1 | lib/page-settings/page-posts.php:15 | 同一 | 同一 | 合格 |
| 205 | 固定ページ | `OP_PAGE_BREADCRUMBS_POSITION` | `page_breadcrumbs_position` | 1 | 1 | lib/page-settings/page-posts.php:22 | 同一 | 同一 | 合格 |
| 206 | 固定ページ | `OP_PAGE_BREADCRUMBS_INCLUDE_POST` | `page_breadcrumbs_include_post` | 1 | 1 | lib/page-settings/page-posts.php:25 | 同一 | 同一 | 合格 |
| 207 | 本文 | `OP_ENTRY_CONTENT_LINE_HIGHT` | `entry_content_line_hight` | 1 | 1 | lib/page-settings/content-posts.php:14 | 同一 | 同一 | 合格 |
| 208 | 本文 | `OP_ENTRY_CONTENT_MARGIN_HIGHT` | `entry_content_margin_hight` | 1 | 1 | lib/page-settings/content-posts.php:17 | 同一 | 同一 | 合格 |
| 209 | 本文 | `OP_EXTERNAL_LINK_OPEN_TYPE` | `external_link_open_type` | 1 | 1 | lib/page-settings/content-posts.php:24 | 同一 | 同一 | 合格 |
| 210 | 本文 | `OP_EXTERNAL_LINK_FOLLOW_TYPE` | `external_link_follow_type` | 1 | 1 | lib/page-settings/content-posts.php:27 | 同一 | 同一 | 合格 |
| 211 | 本文 | `OP_EXTERNAL_LINK_NOOPENER_ENABLE` | `external_link_noopener_enable` | 1 | 1 | lib/page-settings/content-posts.php:30 | 同一 | 同一 | 合格 |
| 212 | 本文 | `OP_EXTERNAL_TARGET_BLANK_LINK_NOOPENER_ENABLE` | `external_target_blank_link_noopener_enable` | 1 | 1 | lib/page-settings/content-posts.php:33 | 同一 | 同一 | 合格 |
| 213 | 本文 | `OP_EXTERNAL_LINK_NOREFERRER_ENABLE` | `external_link_noreferrer_enable` | 1 | 1 | lib/page-settings/content-posts.php:36 | 同一 | 同一 | 合格 |
| 214 | 本文 | `OP_EXTERNAL_TARGET_BLANK_LINK_NOREFERRER_ENABLE` | `external_target_blank_link_noreferrer_enable` | 1 | 1 | lib/page-settings/content-posts.php:39 | 同一 | 同一 | 合格 |
| 215 | 本文 | `OP_EXTERNAL_LINK_EXTERNAL_ENABLE` | `external_link_external_enable` | 1 | 1 | lib/page-settings/content-posts.php:42 | 同一 | 同一 | 合格 |
| 216 | 本文 | `OP_EXTERNAL_LINK_ICON_VISIBLE` | `external_link_icon_visible` | 1 | 1 | lib/page-settings/content-posts.php:45 | 同一 | 同一 | 合格 |
| 217 | 本文 | `OP_EXTERNAL_LINK_ICON` | `external_link_icon` | 1 | 1 | lib/page-settings/content-posts.php:48 | 同一 | 同一 | 合格 |
| 218 | 本文 | `OP_INTERNAL_LINK_OPEN_TYPE` | `internal_link_open_type` | 1 | 1 | lib/page-settings/content-posts.php:55 | 同一 | 同一 | 合格 |
| 219 | 本文 | `OP_INTERNAL_LINK_FOLLOW_TYPE` | `internal_link_follow_type` | 1 | 1 | lib/page-settings/content-posts.php:58 | 同一 | 同一 | 合格 |
| 220 | 本文 | `OP_INTERNAL_LINK_NOOPENER_ENABLE` | `internal_link_noopener_enable` | 1 | 1 | lib/page-settings/content-posts.php:61 | 同一 | 同一 | 合格 |
| 221 | 本文 | `OP_INTERNAL_TARGET_BLANK_LINK_NOOPENER_ENABLE` | `internal_target_blank_link_noopener_enable` | 1 | 1 | lib/page-settings/content-posts.php:64 | 同一 | 同一 | 合格 |
| 222 | 本文 | `OP_INTERNAL_LINK_NOREFERRER_ENABLE` | `internal_link_noreferrer_enable` | 1 | 1 | lib/page-settings/content-posts.php:67 | 同一 | 同一 | 合格 |
| 223 | 本文 | `OP_INTERNAL_TARGET_BLANK_LINK_NOREFERRER_ENABLE` | `internal_target_blank_link_noreferrer_enable` | 1 | 1 | lib/page-settings/content-posts.php:70 | 同一 | 同一 | 合格 |
| 224 | 本文 | `OP_INTERNAL_LINK_ICON_VISIBLE` | `internal_link_icon_visible` | 1 | 1 | lib/page-settings/content-posts.php:73 | 同一 | 同一 | 合格 |
| 225 | 本文 | `OP_INTERNAL_LINK_ICON` | `internal_link_icon` | 1 | 1 | lib/page-settings/content-posts.php:76 | 同一 | 同一 | 合格 |
| 226 | 本文 | `OP_RESPONSIVE_TABLE_ENABLE` | `responsive_table_enable` | 1 | 1 | lib/page-settings/content-posts.php:83 | 同一 | 同一 | 合格 |
| 227 | 本文 | `OP_RESPONSIVE_TABLE_FIRST_COLUMN_STICKY_ENABLE` | `responsive_table_first_column_sticky_enable` | 1 | 1 | lib/page-settings/content-posts.php:86 | 同一 | 同一 | 合格 |
| 228 | 本文 | `OP_POST_DATE_VISIBLE` | `post_date_visible` | 1 | 1 | lib/page-settings/content-posts.php:93 | 同一 | 同一 | 合格 |
| 229 | 本文 | `OP_POST_UPDATE_VISIBLE` | `post_update_visible` | 1 | 1 | lib/page-settings/content-posts.php:96 | 同一 | 同一 | 合格 |
| 230 | 本文 | `OP_POST_AUTHOR_VISIBLE` | `post_author_visible` | 1 | 1 | lib/page-settings/content-posts.php:99 | 同一 | 同一 | 合格 |
| 231 | 本文 | `OP_CONTENT_READ_TIME_VISIBLE` | `content_read_time_visible` | 1 | 1 | lib/page-settings/content-posts.php:102 | 同一 | 同一 | 合格 |
| 232 | 目次 | `OP_TOC_VISIBLE` | `toc_visible` | 1 | 1 | lib/page-settings/toc-posts.php:11 | 同一 | 同一 | 合格 |
| 233 | 目次 | `OP_MULTI_PAGE_TOC_VISIBLE` | `multi_page_toc_visible` | 1 | 1 | lib/page-settings/toc-posts.php:14 | 同一 | 同一 | 合格 |
| 234 | 目次 | `OP_SINGLE_TOC_VISIBLE` | `single_toc_visible` | 1 | 1 | lib/page-settings/toc-posts.php:17 | 同一 | 同一 | 合格 |
| 235 | 目次 | `OP_PAGE_TOC_VISIBLE` | `page_toc_visible` | 1 | 1 | lib/page-settings/toc-posts.php:20 | 同一 | 同一 | 合格 |
| 236 | 目次 | `OP_CATEGORY_TOC_VISIBLE` | `category_toc_visible` | 1 | 1 | lib/page-settings/toc-posts.php:23 | 同一 | 同一 | 合格 |
| 237 | 目次 | `OP_TAG_TOC_VISIBLE` | `tag_toc_visible` | 1 | 1 | lib/page-settings/toc-posts.php:26 | 同一 | 同一 | 合格 |
| 238 | 目次 | `OP_TOC_TITLE` | `toc_title` | 1 | 1 | lib/page-settings/toc-posts.php:29 | 同一 | 同一 | 合格 |
| 239 | 目次 | `OP_TOC_TOGGLE_SWITCH_ENABLE` | `toc_toggle_switch_enable` | 1 | 1 | lib/page-settings/toc-posts.php:32 | 同一 | 同一 | 合格 |
| 240 | 目次 | `OP_TOC_OPEN_CAPTION` | `toc_open_caption` | 1 | 1 | lib/page-settings/toc-posts.php:35 | 同一 | 同一 | 合格 |
| 241 | 目次 | `OP_TOC_CLOSE_CAPTION` | `toc_close_caption` | 1 | 1 | lib/page-settings/toc-posts.php:38 | 同一 | 同一 | 合格 |
| 242 | 目次 | `OP_TOC_CONTENT_VISIBLE` | `toc_content_visible` | 1 | 1 | lib/page-settings/toc-posts.php:41 | 同一 | 同一 | 合格 |
| 243 | 目次 | `OP_TOC_DISPLAY_COUNT` | `toc_display_count` | 1 | 1 | lib/page-settings/toc-posts.php:44 | 同一 | 同一 | 合格 |
| 244 | 目次 | `OP_TOC_DEPTH` | `toc_depth` | 1 | 1 | lib/page-settings/toc-posts.php:47 | 同一 | 同一 | 合格 |
| 245 | 目次 | `OP_TOC_NUMBER_TYPE` | `toc_number_type` | 1 | 1 | lib/page-settings/toc-posts.php:50 | 同一 | 同一 | 合格 |
| 246 | 目次 | `OP_TOC_POSITION_CENTER` | `toc_position_center` | 1 | 1 | lib/page-settings/toc-posts.php:53 | 同一 | 同一 | 合格 |
| 247 | 目次 | `OP_TOC_BEFORE_ADS` | `toc_before_ads` | 1 | 1 | lib/page-settings/toc-posts.php:56 | 同一 | 同一 | 合格 |
| 248 | 目次 | `OP_TOC_HEADING_INNER_HTML_TAG_ENABLE` | `toc_heading_inner_html_tag_enable` | 1 | 1 | lib/page-settings/toc-posts.php:59 | 同一 | 同一 | 合格 |
| 249 | SNSシェア | `OP_TWITTER_ID_INCLUDE` | `twitter_id_include` | 1 | 1 | lib/page-settings/sns-share-posts.php:16 | 同一 | 同一 | 合格 |
| 250 | SNSシェア | `OP_TWITTER_HASH_TAG` | `twitter_hash_tag` | 1 | 1 | lib/page-settings/sns-share-posts.php:20 | 同一 | 同一 | 合格 |
| 251 | SNSシェア | `OP_FACEBOOK_ACCESS_TOKEN` | `facebook_access_token` | 1 | 1 | lib/page-settings/sns-share-posts.php:23 | 同一 | 同一 | 合格 |
| 252 | SNSシェア | `OP_PINTEREST_SHARE_PIN_VISIBLE` | `pinterest_share_button_visible` | 1 | 1 | lib/page-settings/sns-share-posts.php:26 | 同一 | 同一 | 合格 |
| 253 | SNSシェア | `OP_SNS_SHARE_COUNT_CACHE_ENABLE` | `sns_share_count_cache_enable` | 1 | 1 | lib/page-settings/sns-share-posts.php:29 | 同一 | 同一 | 合格 |
| 254 | SNSシェア | `OP_SNS_SHARE_COUNT_CACHE_INTERVAL` | `sns_share_count_cache_interval` | 1 | 1 | lib/page-settings/sns-share-posts.php:31 | 同一 | 同一 | 合格 |
| 255 | SNSシェア | `OP_ANOTHER_SCHEME_SNS_SHARE_COUNT` | `another_scheme_sns_share_count` | 1 | 1 | lib/page-settings/sns-share-posts.php:33 | 同一 | 同一 | 合格 |
| 256 | SNSシェア | `OP_SNS_TOP_SHARE_BUTTONS_VISIBLE` | `sns_top_share_buttons_visible` | 1 | 1 | lib/page-settings/sns-share-posts-top.php:11 | 同一 | 同一 | 合格 |
| 257 | SNSシェア | `OP_SNS_TOP_SHARE_MESSAGE` | `sns_top_share_message` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:13 | 同一 | 同一 | 合格 |
| 258 | SNSシェア | `OP_TOP_TWITTER_SHARE_BUTTON_VISIBLE` | `top_twitter_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 259 | SNSシェア | `OP_TOP_MASTODON_SHARE_BUTTON_VISIBLE` | `top_mastodon_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 260 | SNSシェア | `OP_TOP_BLUESKY_SHARE_BUTTON_VISIBLE` | `top_bluesky_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 261 | SNSシェア | `OP_TOP_MISSKEY_SHARE_BUTTON_VISIBLE` | `top_misskey_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 262 | SNSシェア | `OP_TOP_FACEBOOK_SHARE_BUTTON_VISIBLE` | `top_facebook_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 263 | SNSシェア | `OP_TOP_THREADS_SHARE_BUTTON_VISIBLE` | `top_threads_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 264 | SNSシェア | `OP_TOP_REDDIT_SHARE_BUTTON_VISIBLE` | `top_reddit_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 265 | SNSシェア | `OP_TOP_HATEBU_SHARE_BUTTON_VISIBLE` | `top_hatebu_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 266 | SNSシェア | `OP_TOP_LINE_AT_SHARE_BUTTON_VISIBLE` | `top_line_at_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 267 | SNSシェア | `OP_TOP_PINTEREST_SHARE_BUTTON_VISIBLE` | `top_pinterest_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 268 | SNSシェア | `OP_TOP_LINKEDIN_SHARE_BUTTON_VISIBLE` | `top_linkedin_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 269 | SNSシェア | `OP_TOP_COPY_SHARE_BUTTON_VISIBLE` | `top_copy_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 270 | SNSシェア | `OP_TOP_COMMENT_SHARE_BUTTON_VISIBLE` | `top_comment_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-top.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 271 | SNSシェア | `OP_SNS_FRONT_PAGE_TOP_SHARE_BUTTONS_VISIBLE` | `sns_front_page_top_share_buttons_visible` | 1 | 1 | lib/page-settings/sns-share-posts-top.php:21 | 同一 | 同一 | 合格 |
| 272 | SNSシェア | `OP_SNS_SINGLE_TOP_SHARE_BUTTONS_VISIBLE` | `sns_single_top_share_buttons_visible` | 1 | 1 | lib/page-settings/sns-share-posts-top.php:23 | 同一 | 同一 | 合格 |
| 273 | SNSシェア | `OP_SNS_PAGE_TOP_SHARE_BUTTONS_VISIBLE` | `sns_page_top_share_buttons_visible` | 1 | 1 | lib/page-settings/sns-share-posts-top.php:25 | 同一 | 同一 | 合格 |
| 274 | SNSシェア | `OP_SNS_CATEGORY_TOP_SHARE_BUTTONS_VISIBLE` | `sns_category_top_share_buttons_visible` | 1 | 1 | lib/page-settings/sns-share-posts-top.php:27 | 同一 | 同一 | 合格 |
| 275 | SNSシェア | `OP_SNS_TAG_TOP_SHARE_BUTTONS_VISIBLE` | `sns_tag_top_share_buttons_visible` | 1 | 1 | lib/page-settings/sns-share-posts-top.php:29 | 同一 | 同一 | 合格 |
| 276 | SNSシェア | `OP_SNS_TOP_SHARE_BUTTON_COLOR` | `sns_top_share_button_color` | 1 | 1 | lib/page-settings/sns-share-posts-top.php:31 | 同一 | 同一 | 合格 |
| 277 | SNSシェア | `OP_SNS_TOP_SHARE_COLUMN_COUNT` | `sns_top_share_column_count` | 1 | 1 | lib/page-settings/sns-share-posts-top.php:33 | 同一 | 同一 | 合格 |
| 278 | SNSシェア | `OP_SNS_TOP_SHARE_LOGO_CAPTION_POSITION` | `sns_top_share_logo_caption_position` | 1 | 1 | lib/page-settings/sns-share-posts-top.php:35 | 同一 | 同一 | 合格 |
| 279 | SNSシェア | `OP_SNS_TOP_SHARE_BUTTONS_COUNT_VISIBLE` | `sns_top_share_buttons_count_visible` | 1 | 1 | lib/page-settings/sns-share-posts-top.php:37 | 同一 | 同一 | 合格 |
| 280 | SNSシェア | `OP_SNS_BOTTOM_SHARE_BUTTONS_VISIBLE` | `sns_bottom_share_buttons_visible` | 1 | 1 | lib/page-settings/sns-share-posts-bottom.php:11 | 同一 | 同一 | 合格 |
| 281 | SNSシェア | `OP_SNS_BOTTOM_SHARE_MESSAGE` | `sns_bottom_share_message` | 1 | 1 | lib/page-settings/sns-share-posts-bottom.php:13 | 同一 | 同一 | 合格 |
| 282 | SNSシェア | `OP_BOTTOM_TWITTER_SHARE_BUTTON_VISIBLE` | `bottom_twitter_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-bottom.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 283 | SNSシェア | `OP_BOTTOM_MASTODON_SHARE_BUTTON_VISIBLE` | `bottom_mastodon_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-bottom.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 284 | SNSシェア | `OP_BOTTOM_BLUESKY_SHARE_BUTTON_VISIBLE` | `bottom_bluesky_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-bottom.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 285 | SNSシェア | `OP_BOTTOM_MISSKEY_SHARE_BUTTON_VISIBLE` | `bottom_misskey_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-bottom.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 286 | SNSシェア | `OP_BOTTOM_FACEBOOK_SHARE_BUTTON_VISIBLE` | `bottom_facebook_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-bottom.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 287 | SNSシェア | `OP_BOTTOM_THREADS_SHARE_BUTTON_VISIBLE` | `bottom_threads_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-bottom.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 288 | SNSシェア | `OP_BOTTOM_REDDIT_SHARE_BUTTON_VISIBLE` | `bottom_reddit_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-bottom.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 289 | SNSシェア | `OP_BOTTOM_HATEBU_SHARE_BUTTON_VISIBLE` | `bottom_hatebu_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-bottom.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 290 | SNSシェア | `OP_BOTTOM_LINE_AT_SHARE_BUTTON_VISIBLE` | `bottom_line_at_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-bottom.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 291 | SNSシェア | `OP_BOTTOM_PINTEREST_SHARE_BUTTON_VISIBLE` | `bottom_pinterest_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-bottom.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 292 | SNSシェア | `OP_BOTTOM_LINKEDIN_SHARE_BUTTON_VISIBLE` | `bottom_linkedin_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-bottom.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 293 | SNSシェア | `OP_BOTTOM_COPY_SHARE_BUTTON_VISIBLE` | `bottom_copy_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-bottom.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 294 | SNSシェア | `OP_BOTTOM_COMMENT_SHARE_BUTTON_VISIBLE` | `bottom_comment_share_button_visible` | 0 | 1 | lib/page-settings/sns-share-posts-bottom.php:17<br>（SNS定義配列から展開） | 同一 | 同一 | 合格 |
| 295 | SNSシェア | `OP_SNS_FRONT_PAGE_BOTTOM_SHARE_BUTTONS_VISIBLE` | `sns_front_page_bottom_share_buttons_visible` | 1 | 1 | lib/page-settings/sns-share-posts-bottom.php:21 | 同一 | 同一 | 合格 |
| 296 | SNSシェア | `OP_SNS_SINGLE_BOTTOM_SHARE_BUTTONS_VISIBLE` | `sns_single_bottom_share_buttons_visible` | 1 | 1 | lib/page-settings/sns-share-posts-bottom.php:23 | 同一 | 同一 | 合格 |
| 297 | SNSシェア | `OP_SNS_PAGE_BOTTOM_SHARE_BUTTONS_VISIBLE` | `sns_page_bottom_share_buttons_visible` | 1 | 1 | lib/page-settings/sns-share-posts-bottom.php:25 | 同一 | 同一 | 合格 |
| 298 | SNSシェア | `OP_SNS_CATEGORY_BOTTOM_SHARE_BUTTONS_VISIBLE` | `sns_category_bottom_share_buttons_visible` | 1 | 1 | lib/page-settings/sns-share-posts-bottom.php:27 | 同一 | 同一 | 合格 |
| 299 | SNSシェア | `OP_SNS_TAG_BOTTOM_SHARE_BUTTONS_VISIBLE` | `sns_tag_bottom_share_buttons_visible` | 1 | 1 | lib/page-settings/sns-share-posts-bottom.php:29 | 同一 | 同一 | 合格 |
| 300 | SNSシェア | `OP_SNS_BOTTOM_SHARE_BUTTON_COLOR` | `sns_bottom_share_button_color` | 1 | 1 | lib/page-settings/sns-share-posts-bottom.php:31 | 同一 | 同一 | 合格 |
| 301 | SNSシェア | `OP_SNS_BOTTOM_SHARE_COLUMN_COUNT` | `sns_bottom_share_column_count` | 1 | 1 | lib/page-settings/sns-share-posts-bottom.php:33 | 同一 | 同一 | 合格 |
| 302 | SNSシェア | `OP_SNS_BOTTOM_SHARE_LOGO_CAPTION_POSITION` | `sns_bottom_share_logo_caption_position` | 1 | 1 | lib/page-settings/sns-share-posts-bottom.php:35 | 同一 | 同一 | 合格 |
| 303 | SNSシェア | `OP_SNS_BOTTOM_SHARE_BUTTONS_COUNT_VISIBLE` | `sns_bottom_share_buttons_count_visible` | 1 | 1 | lib/page-settings/sns-share-posts-bottom.php:37 | 同一 | 同一 | 合格 |
| 304 | SNSフォロー | `OP_SNS_FOLLOW_BUTTONS_VISIBLE` | `sns_follow_buttons_visible` | 1 | 1 | lib/page-settings/sns-follow-posts.php:11 | 同一 | 同一 | 合格 |
| 305 | SNSフォロー | `OP_SNS_FOLLOW_MESSAGE` | `sns_follow_message` | 1 | 1 | lib/page-settings/sns-follow-posts.php:13 | 同一 | 同一 | 合格 |
| 306 | SNSフォロー | `OP_SNS_FRONT_PAGE_FOLLOW_BUTTONS_VISIBLE` | `sns_front_page_follow_buttons_visible` | 1 | 1 | lib/page-settings/sns-follow-posts.php:15 | 同一 | 同一 | 合格 |
| 307 | SNSフォロー | `OP_SNS_SINGLE_FOLLOW_BUTTONS_VISIBLE` | `sns_single_follow_buttons_visible` | 1 | 1 | lib/page-settings/sns-follow-posts.php:17 | 同一 | 同一 | 合格 |
| 308 | SNSフォロー | `OP_SNS_PAGE_FOLLOW_BUTTONS_VISIBLE` | `sns_page_follow_buttons_visible` | 1 | 1 | lib/page-settings/sns-follow-posts.php:19 | 同一 | 同一 | 合格 |
| 309 | SNSフォロー | `OP_SNS_CATEGORY_FOLLOW_BUTTONS_VISIBLE` | `sns_category_follow_buttons_visible` | 1 | 1 | lib/page-settings/sns-follow-posts.php:21 | 同一 | 同一 | 合格 |
| 310 | SNSフォロー | `OP_SNS_TAG_FOLLOW_BUTTONS_VISIBLE` | `sns_tag_follow_buttons_visible` | 1 | 1 | lib/page-settings/sns-follow-posts.php:23 | 同一 | 同一 | 合格 |
| 311 | SNSフォロー | `OP_FEEDLY_FOLLOW_BUTTON_VISIBLE` | `feedly_follow_button_visible` | 1 | 1 | lib/page-settings/sns-follow-posts.php:25 | 同一 | 同一 | 合格 |
| 312 | SNSフォロー | `OP_RSS_FOLLOW_BUTTON_VISIBLE` | `rss_follow_button_visible` | 1 | 1 | lib/page-settings/sns-follow-posts.php:27 | 同一 | 同一 | 合格 |
| 313 | SNSフォロー | `OP_SNS_FOLLOW_BUTTON_COLOR` | `sns_follow_button_color` | 1 | 1 | lib/page-settings/sns-follow-posts.php:29 | 同一 | 同一 | 合格 |
| 314 | SNSフォロー | `OP_SNS_DEFAULT_FOLLOW_USER` | `sns_default_follow_user` | 1 | 1 | lib/page-settings/sns-follow-posts.php:31 | 同一 | 同一 | 合格 |
| 315 | SNSフォロー | `OP_SNS_FOLLOW_BUTTONS_COUNT_VISIBLE` | `sns_follow_buttons_count_visible` | 1 | 1 | lib/page-settings/sns-follow-posts.php:33 | 同一 | 同一 | 合格 |
| 316 | SNSフォロー | `OP_SNS_FEEDLY_FOLLOW_COUNT` | `sns_feedly_follow_count` | 1 | 1 | lib/page-settings/sns-follow-posts.php:35 | 同一 | 同一 | 合格 |
| 317 | SNSフォロー | `OP_SNS_FOLLOW_COUNT_CACHE_ENABLE` | `sns_follow_count_cache_enable` | 1 | 1 | lib/page-settings/sns-follow-posts.php:38 | 同一 | 同一 | 合格 |
| 318 | SNSフォロー | `OP_SNS_FOLLOW_COUNT_CACHE_INTERVAL` | `sns_follow_count_cache_interval` | 1 | 1 | lib/page-settings/sns-follow-posts.php:40 | 同一 | 同一 | 合格 |
| 319 | SNSフォロー | `OP_ANOTHER_SCHEME_SNS_FOLLOW_COUNT` | `another_scheme_sns_follow_count` | 1 | 1 | lib/page-settings/sns-follow-posts.php:42 | 同一 | 同一 | 合格 |
| 320 | 画像 | `OP_EYECATCH_VISIBLE` | `eyecatch_visible` | 1 | 1 | lib/page-settings/image-posts.php:11 | 同一 | 同一 | 合格 |
| 321 | 画像 | `OP_EYECATCH_LABEL_VISIBLE` | `eyecatch_label_visible` | 1 | 1 | lib/page-settings/image-posts.php:14 | 同一 | 同一 | 合格 |
| 322 | 画像 | `OP_EYECATCH_CENTER_ENABLE` | `eyecatch_center_enable` | 1 | 1 | lib/page-settings/image-posts.php:17 | 同一 | 同一 | 合格 |
| 323 | 画像 | `OP_EYECATCH_WIDTH_100_PERCENT_ENABLE` | `eyecatch_width_100_percent_enable` | 1 | 1 | lib/page-settings/image-posts.php:20 | 同一 | 同一 | 合格 |
| 324 | 画像 | `OP_EYECATCH_CAPTION_VISIBLE` | `eyecatch_caption_visible` | 1 | 1 | lib/page-settings/image-posts.php:23 | 同一 | 同一 | 合格 |
| 325 | 画像 | `OP_AUTO_POST_THUMBNAIL_ENABLE` | `auto_post_thumbnail_enable` | 2 | 1 | lib/page-settings/image-posts.php:26 | 同一 | 同一 | 合格 |
| 326 | 画像 | `OP_IMAGE_WRAP_EFFECT` | `image_wrap_effect` | 1 | 1 | lib/page-settings/image-posts.php:29 | 同一 | 同一 | 合格 |
| 327 | 画像 | `OP_IMAGE_ZOOM_EFFECT` | `image_zoom_effect` | 1 | 1 | lib/page-settings/image-posts.php:32 | 同一 | 同一 | 合格 |
| 328 | 画像 | `OP_CONTENT_IMAGE_CENTER_ENABLE` | `content_image_center_enable` | 0 | 1 | lib/page-settings/image-posts.php:35 | 同一 | 同一 | 合格 |
| 329 | 画像 | `OP_THUMBNAIL_IMAGE_TYPE` | `thumbnail_image_type` | 1 | 1 | lib/page-settings/image-posts.php:38 | 同一 | 同一 | 合格 |
| 330 | 画像 | `OP_NO_IMAGE_URL` | `no_image_url` | 1 | 1 | lib/page-settings/image-posts.php:43 | 同一 | 同一 | 合格 |
| 331 | ブログカード | `OP_INTERNAL_BLOGCARD_ENABLE` | `internal_blogcard_enable` | 1 | 1 | lib/page-settings/blogcard-in-posts.php:11 | 同一 | 同一 | 合格 |
| 332 | ブログカード | `OP_COMMENT_INTERNAL_BLOGCARD_ENABLE` | `comment_internal_blogcard_enable` | 1 | 1 | lib/page-settings/blogcard-in-posts.php:14 | 同一 | 同一 | 合格 |
| 333 | ブログカード | `OP_INTERNAL_BLOGCARD_DOMAIN_STYLE` | `internal_blogcard_domain_style` | 1 | 1 | lib/page-settings/blogcard-in-posts.php:17 | 同一 | 同一 | 合格 |
| 334 | ブログカード | `OP_INTERNAL_BLOGCARD_THUMBNAIL_STYLE` | `internal_blogcard_thumbnail_style` | 1 | 1 | lib/page-settings/blogcard-in-posts.php:20 | 同一 | 同一 | 合格 |
| 335 | ブログカード | `OP_INTERNAL_BLOGCARD_DATE_TYPE` | `internal_blogcard_date_type` | 1 | 1 | lib/page-settings/blogcard-in-posts.php:23 | 同一 | 同一 | 合格 |
| 336 | ブログカード | `OP_INTERNAL_BLOGCARD_TARGET_BLANK` | `internal_blogcard_target_blank` | 1 | 1 | lib/page-settings/blogcard-in-posts.php:26 | 同一 | 同一 | 合格 |
| 337 | ブログカード | `OP_EXTERNAL_BLOGCARD_ENABLE` | `external_blogcard_enable` | 1 | 1 | lib/page-settings/blogcard-out-posts.php:11 | 同一 | 同一 | 合格 |
| 338 | ブログカード | `OP_COMMENT_EXTERNAL_BLOGCARD_ENABLE` | `comment_external_blogcard_enable` | 1 | 1 | lib/page-settings/blogcard-out-posts.php:14 | 同一 | 同一 | 合格 |
| 339 | ブログカード | `OP_EXTERNAL_BLOGCARD_DOMAIN_STYLE` | `external_blogcard_domain_style` | 1 | 1 | lib/page-settings/blogcard-out-posts.php:17 | 同一 | 同一 | 合格 |
| 340 | ブログカード | `OP_EXTERNAL_BLOGCARD_THUMBNAIL_STYLE` | `external_blogcard_thumbnail_style` | 1 | 1 | lib/page-settings/blogcard-out-posts.php:20 | 同一 | 同一 | 合格 |
| 341 | ブログカード | `OP_EXTERNAL_BLOGCARD_TARGET_BLANK` | `external_blogcard_target_blank` | 1 | 1 | lib/page-settings/blogcard-out-posts.php:23 | 同一 | 同一 | 合格 |
| 342 | ブログカード | `OP_EXTERNAL_BLOGCARD_CACHE_RETENTION_PERIOD` | `external_blogcard_cache_retention_period` | 1 | 1 | lib/page-settings/blogcard-out-posts.php:26 | 同一 | 同一 | 合格 |
| 343 | ブログカード | `OP_EXTERNAL_BLOGCARD_REFRESH_MODE` | `external_blogcard_refresh_mode` | 1 | 1 | lib/page-settings/blogcard-out-posts.php:29 | 同一 | 同一 | 合格 |
| 344 | コード | `OP_CODE_HIGHLIGHT_ENABLE` | `code_highlight_enable` | 2 | 1 | lib/page-settings/code-posts.php:11 | 同一 | 同一 | 合格 |
| 345 | コード | `OP_CODE_ROW_NUMBER_ENABLE` | `code_row_number_enable` | 1 | 1 | lib/page-settings/code-posts.php:14 | 同一 | 同一 | 合格 |
| 346 | コード | `OP_CODE_HIGHLIGHT_PACKAGE` | `code_highlight_package` | 1 | 1 | lib/page-settings/code-posts.php:17 | 同一 | 同一 | 合格 |
| 347 | コード | `OP_CODE_HIGHLIGHT_STYLE` | `code_highlight_style` | 1 | 1 | lib/page-settings/code-posts.php:20 | 同一 | 同一 | 合格 |
| 348 | コード | `OP_CODE_HIGHLIGHT_CSS_SELECTOR` | `code_highlight_css_selector` | 1 | 1 | lib/page-settings/code-posts.php:23 | 同一 | 同一 | 合格 |
| 349 | コード | `OP_FORMULA_ENABLE` | `formula_enable` | 1 | 1 | lib/page-settings/code-posts.php:27 | 同一 | 同一 | 合格 |
| 350 | コメント | `OP_COMMENT_DISPLAY_TYPE` | `comment_display_type` | 1 | 1 | lib/page-settings/comment-posts.php:11 | 同一 | 同一 | 合格 |
| 351 | コメント | `OP_COMMENT_HEADING` | `comment_heading` | 1 | 1 | lib/page-settings/comment-posts.php:14 | 同一 | 同一 | 合格 |
| 352 | コメント | `OP_COMMENT_SUB_HEADING` | `comment_sub_heading` | 1 | 1 | lib/page-settings/comment-posts.php:17 | 同一 | 同一 | 合格 |
| 353 | コメント | `OP_COMMENT_FORM_DISPLAY_TYPE` | `comment_form_display_type` | 1 | 1 | lib/page-settings/comment-posts.php:20 | 同一 | 同一 | 合格 |
| 354 | コメント | `OP_COMMENT_FORM_HEADING` | `comment_form_heading` | 1 | 1 | lib/page-settings/comment-posts.php:23 | 同一 | 同一 | 合格 |
| 355 | コメント | `OP_COMMENT_INFORMATION_MESSAGE` | `comment_information_message` | 1 | 1 | lib/page-settings/comment-posts.php:26 | 同一 | 同一 | 合格 |
| 356 | コメント | `OP_COMMENT_WEBSITE_VISIBLE` | `comment_website_visible` | 1 | 1 | lib/page-settings/comment-posts.php:29 | 同一 | 同一 | 合格 |
| 357 | コメント | `OP_COMMENT_SUBMIT_LABEL` | `comment_submit_label` | 1 | 1 | lib/page-settings/comment-posts.php:32 | 同一 | 同一 | 合格 |
| 358 | 通知 | `OP_NOTICE_AREA_VISIBLE` | `notice_area_visible` | 1 | 1 | lib/page-settings/notice-posts.php:11 | 同一 | 同一 | 合格 |
| 359 | 通知 | `OP_NOTICE_AREA_MESSAGE` | `notice_area_message` | 1 | 1 | lib/page-settings/notice-posts.php:14 | 同一 | 同一 | 合格 |
| 360 | 通知 | `OP_NOTICE_AREA_URL` | `notice_area_url` | 1 | 1 | lib/page-settings/notice-posts.php:17 | 同一 | 同一 | 合格 |
| 361 | 通知 | `OP_NOTICE_LINK_TARGET_BLANK` | `notice_link_target_blank` | 1 | 1 | lib/page-settings/notice-posts.php:20 | 同一 | 同一 | 合格 |
| 362 | 通知 | `OP_NOTICE_TYPE` | `notice_type` | 1 | 1 | lib/page-settings/notice-posts.php:23 | 同一 | 同一 | 合格 |
| 363 | 通知 | `OP_NOTICE_AREA_BACKGROUND_COLOR` | `notice_area_background_color` | 1 | 1 | lib/page-settings/notice-posts.php:26 | 同一 | 同一 | 合格 |
| 364 | 通知 | `OP_NOTICE_AREA_TEXT_COLOR` | `notice_area_text_color` | 1 | 1 | lib/page-settings/notice-posts.php:29 | 同一 | 同一 | 合格 |
| 365 | アピールエリア | `OP_APPEAL_AREA_DISPLAY_TYPE` | `appeal_area_display_type` | 1 | 1 | lib/page-settings/appeal-posts.php:11 | 同一 | 同一 | 合格 |
| 366 | アピールエリア | `OP_APPEAL_AREA_HEIGHT` | `appeal_area_height` | 1 | 1 | lib/page-settings/appeal-posts.php:14 | 同一 | 同一 | 合格 |
| 367 | アピールエリア | `OP_APPEAL_AREA_IMAGE_URL` | `appeal_area_image_url` | 1 | 1 | lib/page-settings/appeal-posts.php:17 | 同一 | 同一 | 合格 |
| 368 | アピールエリア | `OP_APPEAL_AREA_BACKGROUND_COLOR` | `appeal_area_background_color` | 1 | 1 | lib/page-settings/appeal-posts.php:20 | 同一 | 同一 | 合格 |
| 369 | アピールエリア | `OP_APPEAL_AREA_BACKGROUND_ATTACHMENT_FIXED` | `appeal_area_background_attachment_fixed` | 1 | 1 | lib/page-settings/appeal-posts.php:23 | 同一 | 同一 | 合格 |
| 370 | アピールエリア | `OP_APPEAL_AREA_CONTENT_VISIBLE` | `appeal_area_content_visible` | 1 | 1 | lib/page-settings/appeal-posts.php:26 | 同一 | 同一 | 合格 |
| 371 | アピールエリア | `OP_APPEAL_AREA_TITLE` | `appeal_area_title` | 1 | 1 | lib/page-settings/appeal-posts.php:29 | 同一 | 同一 | 合格 |
| 372 | アピールエリア | `OP_APPEAL_AREA_MESSAGE` | `appeal_area_message` | 1 | 1 | lib/page-settings/appeal-posts.php:32 | 同一 | 同一 | 合格 |
| 373 | アピールエリア | `OP_APPEAL_AREA_BUTTON_MESSAGE` | `appeal_area_button_message` | 1 | 1 | lib/page-settings/appeal-posts.php:35 | 同一 | 同一 | 合格 |
| 374 | アピールエリア | `OP_APPEAL_AREA_BUTTON_URL` | `appeal_area_button_url` | 1 | 1 | lib/page-settings/appeal-posts.php:38 | 同一 | 同一 | 合格 |
| 375 | アピールエリア | `OP_APPEAL_AREA_BUTTON_TARGET` | `appeal_area_button_target` | 1 | 1 | lib/page-settings/appeal-posts.php:41 | 同一 | 同一 | 合格 |
| 376 | アピールエリア | `OP_APPEAL_AREA_BUTTON_BACKGROUND_COLOR` | `appeal_area_button_background_color` | 1 | 1 | lib/page-settings/appeal-posts.php:44 | 同一 | 同一 | 合格 |
| 377 | おすすめカード | `OP_RECOMMENDED_CARDS_DISPLAY_TYPE` | `recommended_cards_display_type` | 1 | 1 | lib/page-settings/recommended-posts.php:11 | 同一 | 同一 | 合格 |
| 378 | おすすめカード | `OP_RECOMMENDED_CARDS_MENU_NAME` | `recommended_cards_menu_name` | 1 | 1 | lib/page-settings/recommended-posts.php:14 | 同一 | 同一 | 合格 |
| 379 | おすすめカード | `OP_RECOMMENDED_CARDS_STYLE` | `recommended_cards_style` | 1 | 1 | lib/page-settings/recommended-posts.php:17 | 同一 | 同一 | 合格 |
| 380 | おすすめカード | `OP_RECOMMENDED_CARDS_MARGIN_ENABLE` | `recommended_cards_margin_enable` | 1 | 1 | lib/page-settings/recommended-posts.php:20 | 同一 | 同一 | 合格 |
| 381 | おすすめカード | `OP_RECOMMENDED_CARDS_AREA_BOTH_SIDES_MARGIN_ENABLE` | `recommended_cards_area_both_sides_margin_enable` | 1 | 1 | lib/page-settings/recommended-posts.php:23 | 同一 | 同一 | 合格 |
| 382 | カルーセル | `OP_CAROUSEL_DISPLAY_TYPE` | `carousel_display_type` | 1 | 1 | lib/page-settings/carousel-posts.php:11 | 同一 | 同一 | 合格 |
| 383 | カルーセル | `OP_CAROUSEL_SMARTPHONE_VISIBLE` | `carousel_smartphone_visible` | 1 | 1 | lib/page-settings/carousel-posts.php:14 | 同一 | 同一 | 合格 |
| 384 | カルーセル | `OP_CAROUSEL_POPULAR_POSTS_ENABLE` | `carousel_popular_posts_enable` | 1 | 1 | lib/page-settings/carousel-posts.php:17 | 同一 | 同一 | 合格 |
| 385 | カルーセル | `OP_CAROUSEL_POPULAR_POSTS_COUNT_DAYS` | `carousel_popular_posts_count_days` | 1 | 1 | lib/page-settings/carousel-posts.php:20 | 同一 | 同一 | 合格 |
| 386 | カルーセル | `OP_CAROUSEL_CATEGORY_IDS` | `carousel_category_ids` | 1 | 1 | lib/page-settings/carousel-posts.php:23 | 同一 | 同一 | 合格 |
| 387 | カルーセル | `OP_CAROUSEL_TAG_IDS` | `carousel_tag_ids` | 1 | 1 | lib/page-settings/carousel-posts.php:26 | 同一 | 同一 | 合格 |
| 388 | カルーセル | `OP_CAROUSEL_ORDERBY` | `carousel_orderby` | 1 | 1 | lib/page-settings/carousel-posts.php:29 | 同一 | 同一 | 合格 |
| 389 | カルーセル | `OP_CAROUSEL_MAX_COUNT` | `carousel_max_count` | 1 | 1 | lib/page-settings/carousel-posts.php:32 | 同一 | 同一 | 合格 |
| 390 | カルーセル | `OP_CAROUSEL_CARD_BORDER_VISIBLE` | `carousel_card_border_visible` | 1 | 1 | lib/page-settings/carousel-posts.php:35 | 同一 | 同一 | 合格 |
| 391 | カルーセル | `OP_CAROUSEL_AUTOPLAY_ENABLE` | `carousel_autoplay_enable` | 1 | 1 | lib/page-settings/carousel-posts.php:38 | 同一 | 同一 | 合格 |
| 392 | カルーセル | `OP_CAROUSEL_AUTOPLAY_INTERVAL` | `carousel_autoplay_interval` | 1 | 1 | lib/page-settings/carousel-posts.php:41 | 同一 | 同一 | 合格 |
| 393 | フッター | `OP_FOOTER_BACKGROUND_COLOR` | `footer_background_color` | 1 | 1 | lib/page-settings/footer-posts.php:11 | 同一 | 同一 | 合格 |
| 394 | フッター | `OP_FOOTER_TEXT_COLOR` | `footer_text_color` | 1 | 1 | lib/page-settings/footer-posts.php:14 | 同一 | 同一 | 合格 |
| 395 | フッター | `OP_FOOTER_DISPLAY_TYPE` | `footer_display_type` | 1 | 1 | lib/page-settings/footer-posts.php:17 | 同一 | 同一 | 合格 |
| 396 | フッター | `OP_FOOTER_LOGO_URL` | `footer_logo_url` | 1 | 1 | lib/page-settings/footer-posts.php:20 | 同一 | 同一 | 合格 |
| 397 | フッター | `OP_SITE_INITIATION_YEAR` | `site_initiation_year` | 1 | 1 | lib/page-settings/footer-posts.php:23 | 同一 | 同一 | 合格 |
| 398 | フッター | `OP_COPYRIGHT_NAME` | `copyright_name` | 1 | 1 | lib/page-settings/footer-posts.php:26 | 同一 | 同一 | 合格 |
| 399 | フッター | `OP_CREDIT_NOTATION` | `credit_notation` | 1 | 1 | lib/page-settings/footer-posts.php:29 | 同一 | 同一 | 合格 |
| 400 | フッター | `OP_USER_CREDIT_NOTATION` | `user_credit_notation` | 1 | 1 | lib/page-settings/footer-posts.php:32 | 同一 | 同一 | 合格 |
| 401 | フッター | `OP_FOOTER_NAVI_MENU_WIDTH` | `footer_navi_menu_width` | 1 | 1 | lib/page-settings/footer-posts.php:35 | 同一 | 同一 | 合格 |
| 402 | フッター | `OP_FOOTER_NAVI_MENU_TEXT_WIDTH_ENABLE` | `footer_navi_menu_text_width_enable` | 1 | 1 | lib/page-settings/footer-posts.php:38 | 同一 | 同一 | 合格 |
| 403 | ボタン | `OP_GO_TO_TOP_BUTTON_VISIBLE` | `go_to_top_button_visible` | 1 | 1 | lib/page-settings/buttons-posts.php:11 | 同一 | 同一 | 合格 |
| 404 | ボタン | `OP_GO_TO_TOP_BUTTON_ICON_FONT` | `go_to_top_button_icon_font` | 1 | 1 | lib/page-settings/buttons-posts.php:14 | 同一 | 同一 | 合格 |
| 405 | ボタン | `OP_GO_TO_TOP_BUTTON_IMAGE_URL` | `go_to_top_button_image_url` | 1 | 1 | lib/page-settings/buttons-posts.php:17 | 同一 | 同一 | 合格 |
| 406 | ボタン | `OP_GO_TO_TOP_BACKGROUND_COLOR` | `go_to_top_background_color` | 1 | 1 | lib/page-settings/buttons-posts.php:20 | 同一 | 同一 | 合格 |
| 407 | ボタン | `OP_GO_TO_TOP_TEXT_COLOR` | `go_to_top_text_color` | 1 | 1 | lib/page-settings/buttons-posts.php:23 | 同一 | 同一 | 合格 |
| 408 | モバイル | `OP_MOBILE_BUTTON_LAYOUT_TYPE` | `mobile_button_layout_type` | 1 | 1 | lib/page-settings/mobile-buttons-posts.php:11 | 同一 | 同一 | 合格 |
| 409 | モバイル | `OP_FIXED_MOBILE_BUTTONS_ENABLE` | `fixed_mobile_buttons_enable` | 1 | 1 | lib/page-settings/mobile-buttons-posts.php:14 | 同一 | 同一 | 合格 |
| 410 | モバイル | `OP_MOBILE_HEADER_LOGO_VISIBLE` | `mobile_header_logo_visible` | 1 | 1 | lib/page-settings/mobile-buttons-posts.php:17 | 同一 | 同一 | 合格 |
| 411 | モバイル | `OP_SLIDE_IN_CONTENT_BOTTOM_SIDEBAR_VISIBLE` | `slide_in_content_bottom_sidebar_visible` | 1 | 1 | lib/page-settings/mobile-buttons-posts.php:20 | 同一 | 同一 | 合格 |
| 412 | 404ページ | `OP_404_IMAGE_URL` | `404_image_url` | 1 | 1 | lib/page-settings/404-posts.php:11 | 同一 | 同一 | 合格 |
| 413 | 404ページ | `OP_404_PAGE_TITLE` | `404_page_title` | 1 | 1 | lib/page-settings/404-posts.php:14 | 同一 | 同一 | 合格 |
| 414 | 404ページ | `OP_404_PAGE_MESSAGE` | `404_page_message` | 1 | 1 | lib/page-settings/404-posts.php:17 | 同一 | 同一 | 合格 |
| 415 | AMP | `OP_AMP_ENABLE` | `amp_enable` | 1 | 0 | lib/page-settings/amp-posts.php:11 | 同一 | 同一 | 合格 |
| 416 | AMP | `OP_AMP_LOGO_IMAGE_URL` | `amp_logo_image_url` | 1 | 1 | lib/page-settings/amp-posts.php:14 | 同一 | 同一 | 合格 |
| 417 | AMP | `OP_AMP_IMAGE_ZOOM_EFFECT` | `amp_image_zoom_effect` | 1 | 1 | lib/page-settings/amp-posts.php:17 | 同一 | 同一 | 合格 |
| 418 | AMP | `OP_AMP_VALIDATOR` | `amp_validator` | 1 | 1 | lib/page-settings/amp-posts.php:20 | 同一 | 同一 | 合格 |
| 419 | AMP | `OP_AMP_REMOVAL_INLINE_STYLE_ENABLE` | `amp_removal_inline_style_enable` | 0 | 1 | lib/page-settings/amp-posts.php:23 | 同一 | 同一 | 合格 |
| 420 | AMP | `OP_AMP_INLINE_STYLE_ENABLE` | `amp_inline_style_enable` | 1 | 1 | lib/page-settings/amp-posts.php:26 | 同一 | 同一 | 合格 |
| 421 | AMP | `OP_AMP_SKIN_STYLE_ENABLE` | `amp_skin_style_enable` | 1 | 1 | lib/page-settings/amp-posts.php:29 | 同一 | 同一 | 合格 |
| 422 | AMP | `OP_AMP_CHILD_THEME_STYLE_ENABLE` | `amp_child_theme_style_enable` | 1 | 1 | lib/page-settings/amp-posts.php:32 | 同一 | 同一 | 合格 |
| 423 | AMP | `OP_AMP_EXCLUDE_CATEGORY_IDS` | `amp_exclude_category_ids` | 1 | 1 | lib/page-settings/amp-posts.php:35 | 同一 | 同一 | 合格 |
| 424 | PWA | `OP_PWA_ENABLE` | `pwa_enable` | 1 | 0 | lib/page-settings/pwa-posts.php:11 | 同一 | 同一 | 合格 |
| 425 | PWA | `OP_PWA_ADMIN_ENABLE` | `pwa_admin_enable` | 1 | 1 | lib/page-settings/pwa-posts.php:14 | 同一 | 同一 | 合格 |
| 426 | PWA | `OP_PWA_NAME` | `pwa_name` | 1 | 1 | lib/page-settings/pwa-posts.php:17 | 同一 | 同一 | 合格 |
| 427 | PWA | `OP_PWA_SHORT_NAME` | `pwa_short_name` | 1 | 1 | lib/page-settings/pwa-posts.php:20 | 同一 | 同一 | 合格 |
| 428 | PWA | `OP_PWA_DESCRIPTION` | `pwa_description` | 1 | 1 | lib/page-settings/pwa-posts.php:23 | 同一 | 同一 | 合格 |
| 429 | PWA | `OP_PWA_THEME_COLOR` | `pwa_theme_color` | 1 | 1 | lib/page-settings/pwa-posts.php:26 | 同一 | 同一 | 合格 |
| 430 | PWA | `OP_PWA_BACKGROUND_COLOR` | `pwa_background_color` | 1 | 1 | lib/page-settings/pwa-posts.php:29 | 同一 | 同一 | 合格 |
| 431 | PWA | `OP_PWA_DISPLAY` | `pwa_display` | 1 | 1 | lib/page-settings/pwa-posts.php:32 | 同一 | 同一 | 合格 |
| 432 | PWA | `OP_PWA_ORIENTATION` | `pwa_orientation` | 1 | 1 | lib/page-settings/pwa-posts.php:35 | 同一 | 同一 | 合格 |
| 433 | 管理者画面 | `OP_ADMIN_TOOL_MENU_VISIBLE` | `admin_tool_menu_visible` | 1 | 1 | lib/page-settings/admin-posts.php:11 | 同一 | 同一 | 合格 |
| 434 | 管理者画面 | `OP_ADMIN_INDEX_PV_VISIBLE` | `admin_index_pv_visible` | 1 | 1 | lib/page-settings/admin-posts.php:17 | 同一 | 同一 | 合格 |
| 435 | 管理者画面 | `OP_ADMIN_LIST_AUTHOR_VISIBLE` | `admin_list_author_visible` | 1 | 1 | lib/page-settings/admin-posts.php:23 | 同一 | 同一 | 合格 |
| 436 | 管理者画面 | `OP_ADMIN_LIST_CATEGORIES_VISIBLE` | `admin_list_categories_visible` | 1 | 1 | lib/page-settings/admin-posts.php:26 | 同一 | 同一 | 合格 |
| 437 | 管理者画面 | `OP_ADMIN_LIST_TAGS_VISIBLE` | `admin_list_tags_visible` | 1 | 1 | lib/page-settings/admin-posts.php:29 | 同一 | 同一 | 合格 |
| 438 | 管理者画面 | `OP_ADMIN_LIST_COMMENTS_VISIBLE` | `admin_list_comments_visible` | 1 | 1 | lib/page-settings/admin-posts.php:32 | 同一 | 同一 | 合格 |
| 439 | 管理者画面 | `OP_ADMIN_LIST_DATE_VISIBLE` | `admin_list_date_visible` | 1 | 1 | lib/page-settings/admin-posts.php:35 | 同一 | 同一 | 合格 |
| 440 | 管理者画面 | `OP_ADMIN_LIST_POST_ID_VISIBLE` | `admin_list_post_id_visible` | 1 | 1 | lib/page-settings/admin-posts.php:38 | 同一 | 同一 | 合格 |
| 441 | 管理者画面 | `OP_ADMIN_LIST_WORD_COUNT_VISIBLE` | `admin_list_word_count_visible` | 1 | 1 | lib/page-settings/admin-posts.php:41 | 同一 | 同一 | 合格 |
| 442 | 管理者画面 | `OP_ADMIN_LIST_PV_VISIBLE` | `admin_list_pv_visible` | 1 | 1 | lib/page-settings/admin-posts.php:44 | 同一 | 同一 | 合格 |
| 443 | 管理者画面 | `OP_ADMIN_LIST_EYECATCH_VISIBLE` | `admin_list_eyecatch_visible` | 1 | 1 | lib/page-settings/admin-posts.php:47 | 同一 | 同一 | 合格 |
| 444 | 管理者画面 | `OP_ADMIN_LIST_MEMO_VISIBLE` | `admin_list_memo_visible` | 1 | 1 | lib/page-settings/admin-posts.php:50 | 同一 | 同一 | 合格 |
| 445 | 管理者画面 | `OP_ADMIN_PANEL_DISPLAY_TYPE` | `admin_panel_display_type` | 1 | 1 | lib/page-settings/admin-posts.php:57 | 同一 | 同一 | 合格 |
| 446 | 管理者画面 | `OP_ADMIN_PANEL_PV_AREA_VISIBLE` | `admin_panel_pv_area_visible` | 1 | 1 | lib/page-settings/admin-posts.php:60 | 同一 | 同一 | 合格 |
| 447 | 管理者画面 | `OP_ADMIN_PANEL_PV_TYPE` | `admin_panel_pv_type` | 1 | 1 | lib/page-settings/admin-posts.php:63 | 同一 | 同一 | 合格 |
| 448 | 管理者画面 | `OP_ADMIN_PANEL_EDIT_AREA_VISIBLE` | `admin_panel_edit_area_visible` | 1 | 1 | lib/page-settings/admin-posts.php:66 | 同一 | 同一 | 合格 |
| 449 | 管理者画面 | `OP_ADMIN_PANEL_WP_DASHBOARD_VISIBLE` | `admin_panel_wp_dashboard_visible` | 1 | 1 | lib/page-settings/admin-posts.php:69 | 同一 | 同一 | 合格 |
| 450 | 管理者画面 | `OP_ADMIN_PANEL_WP_EDIT_VISIBLE` | `admin_panel_wp_edit_visible` | 1 | 1 | lib/page-settings/admin-posts.php:72 | 同一 | 同一 | 合格 |
| 451 | 管理者画面 | `OP_ADMIN_PANEL_WLW_EDIT_VISIBLE` | `admin_panel_wlw_edit_visible` | 1 | 1 | lib/page-settings/admin-posts.php:75 | 同一 | 同一 | 合格 |
| 452 | 管理者画面 | `OP_ADMIN_PANEL_AMP_AREA_VISIBLE` | `admin_panel_amp_area_visible` | 1 | 0 | lib/page-settings/admin-posts.php:78 | 同一 | 同一 | 合格 |
| 453 | 管理者画面 | `OP_ADMIN_GOOGLE_AMP_TEST_VISIBLE` | `admin_google_amp_test_visible` | 1 | 0 | lib/page-settings/admin-posts.php:81 | 同一 | 同一 | 合格 |
| 454 | 管理者画面 | `OP_ADMIN_THE_AMP_VALIDATOR_VISIBLE` | `admin_the_amp_validator_visible` | 1 | 0 | lib/page-settings/admin-posts.php:84 | 同一 | 同一 | 合格 |
| 455 | 管理者画面 | `OP_ADMIN_AMPBENCH_VISIBLE` | `admin_ampbench_visible` | 0 | 0 | lib/page-settings/admin-posts.php:87 | 同一 | 同一 | 合格 |
| 456 | 管理者画面 | `OP_ADMIN_PANEL_CHECK_TOOLS_AREA_VISIBLE` | `admin_panel_check_tools_area_visible` | 1 | 1 | lib/page-settings/admin-posts.php:90 | 同一 | 同一 | 合格 |
| 457 | 管理者画面 | `OP_ADMIN_PAGESPEED_INSIGHTS_VISIBLE` | `admin_pagespeed_insights_visible` | 1 | 1 | lib/page-settings/admin-posts.php:93 | 同一 | 同一 | 合格 |
| 458 | 管理者画面 | `OP_ADMIN_GTMETRIX_VISIBLE` | `admin_gtmetrix_visible` | 1 | 1 | lib/page-settings/admin-posts.php:96 | 同一 | 同一 | 合格 |
| 459 | 管理者画面 | `OP_ADMIN_STRUCTURED_DATA_VISIBLE` | `admin_structured_data_visible` | 1 | 1 | lib/page-settings/admin-posts.php:99 | 同一 | 同一 | 合格 |
| 460 | 管理者画面 | `OP_ADMIN_NU_HTML_CHECKER_VISIBLE` | `admin_nu_html_checker_visible` | 1 | 1 | lib/page-settings/admin-posts.php:102 | 同一 | 同一 | 合格 |
| 461 | 管理者画面 | `OP_ADMIN_SEOCHEKI_VISIBLE` | `admin_seocheki_visible` | 1 | 1 | lib/page-settings/admin-posts.php:106 | 同一 | 同一 | 合格 |
| 462 | 管理者画面 | `OP_ADMIN_TWEET_CHECK_VISIBLE` | `admin_tweet_check_visible` | 1 | 1 | lib/page-settings/admin-posts.php:109 | 同一 | 同一 | 合格 |
| 463 | 管理者画面 | `OP_ADMIN_PANEL_RESPONSIVE_TOOLS_AREA_VISIBLE` | `admin_panel_responsive_tools_area_visible` | 1 | 1 | lib/page-settings/admin-posts.php:112 | 同一 | 同一 | 合格 |
| 464 | 管理者画面 | `OP_ADMIN_RESPONSINATOR_VISIBLE` | `admin_responsinator_visible` | 1 | 1 | lib/page-settings/admin-posts.php:115 | 同一 | 同一 | 合格 |
| 465 | 管理者画面 | `OP_ADMIN_SIZZY_VISIBLE` | `admin_sizzy_visible` | 1 | 1 | lib/page-settings/admin-posts.php:118 | 同一 | 同一 | 合格 |
| 466 | 管理者画面 | `OP_ADMIN_MULTI_SCREEN_RESOLUTION_TEST_VISIBLE` | `admin_multi_screen_resolution_test_visible` | 1 | 1 | lib/page-settings/admin-posts.php:121 | 同一 | 同一 | 合格 |
| 467 | ウィジェット | `OP_EXCLUDE_WIDGET_CLASSES` | `exclude_widget_classes` | 1 | 1 | lib/page-settings/widget-posts.php:11 | 同一 | 同一 | 合格 |
| 468 | ウィジェットエリア | `OP_EXCLUDE_WIDGET_AREA_IDS` | `exclude_widget_area_ids` | 1 | 1 | lib/page-settings/widget-area-posts.php:11 | 同一 | 同一 | 合格 |
| 469 | エディター | `OP_GUTENBERG_EDITOR_ENABLE` | `gutenberg_editor_enable` | 2 | 1 | lib/page-settings/editor-posts.php:11 | 同一 | 同一 | 合格 |
| 470 | エディター | `OP_VISUAL_EDITOR_STYLE_ENABLE` | `visual_editor_style_enable` | 1 | 1 | lib/page-settings/editor-posts.php:14 | 同一 | 同一 | 合格 |
| 471 | エディター | `OP_EDITOR_BACKGROUND_COLOR` | `editor_background_color` | 1 | 1 | lib/page-settings/editor-posts.php:17 | 同一 | 同一 | 合格 |
| 472 | エディター | `OP_EDITOR_TEXT_COLOR` | `editor_text_color` | 1 | 1 | lib/page-settings/editor-posts.php:20 | 同一 | 同一 | 合格 |
| 473 | エディター | `OP_EDITOR_TAG_CHECK_LIST_ENABLE` | `editor_tag_check_list_enable` | 1 | 1 | lib/page-settings/editor-posts.php:23 | 同一 | 同一 | 合格 |
| 474 | エディター | `OP_FEATURED_IMAGE_FROM_TITLE_ENABLE` | `featured_image_from_title_enable` | 1 | 1 | lib/page-settings/editor-posts.php:26 | 同一 | 同一 | 合格 |
| 475 | エディター | `OP_FEATURED_IMAGE_FROM_TITLE_DEFAULT_ENABLE` | `featured_image_from_title_default_enable` | 1 | 1 | lib/page-settings/editor-posts.php:30 | 同一 | 同一 | 合格 |
| 476 | エディター | `OP_BLOCK_EDITOR_RUBY_BUTTON_VISIBLE` | `block_editor_ruby_button_visible` | 1 | 1 | lib/page-settings/editor-posts.php:33 | 同一 | 同一 | 合格 |
| 477 | エディター | `OP_BLOCK_EDITOR_CLEAR_FORMAT_BUTTON_VISIBLE` | `block_editor_clear_format_button_visible` | 1 | 1 | lib/page-settings/editor-posts.php:36 | 同一 | 同一 | 合格 |
| 478 | エディター | `OP_BLOCK_EDITOR_LETTER_STYLE_DROPDOWN_VISIBLE` | `block_editor_letter_style_dropdown_visible` | 1 | 1 | lib/page-settings/editor-posts.php:39 | 同一 | 同一 | 合格 |
| 479 | エディター | `OP_BLOCK_EDITOR_MARKER_STYLE_DROPDOWN_VISIBLE` | `block_editor_marker_style_dropdown_visible` | 1 | 1 | lib/page-settings/editor-posts.php:42 | 同一 | 同一 | 合格 |
| 480 | エディター | `OP_BLOCK_EDITOR_BADGE_STYLE_DROPDOWN_VISIBLE` | `block_editor_badge_style_dropdown_visible` | 1 | 1 | lib/page-settings/editor-posts.php:45 | 同一 | 同一 | 合格 |
| 481 | エディター | `OP_BLOCK_EDITOR_FONT_SIZE_STYLE_DROPDOWN_VISIBLE` | `block_editor_font_size_style_dropdown_visible` | 1 | 1 | lib/page-settings/editor-posts.php:48 | 同一 | 同一 | 合格 |
| 482 | エディター | `OP_BLOCK_EDITOR_GENERAL_SHORTCODE_DROPDOWN_VISIBLE` | `block_editor_general_shortcode_dropdown_visible` | 1 | 1 | lib/page-settings/editor-posts.php:51 | 同一 | 同一 | 合格 |
| 483 | エディター | `OP_BLOCK_EDITOR_TEMPLATE_SHORTCODE_DROPDOWN_VISIBLE` | `block_editor_template_shortcode_dropdown_visible` | 1 | 1 | lib/page-settings/editor-posts.php:54 | 同一 | 同一 | 合格 |
| 484 | エディター | `OP_BLOCK_EDITOR_AFFILIATE_SHORTCODE_DROPDOWN_VISIBLE` | `block_editor_affiliate_shortcode_dropdown_visible` | 1 | 1 | lib/page-settings/editor-posts.php:57 | 同一 | 同一 | 合格 |
| 485 | エディター | `OP_BLOCK_EDITOR_RANKING_SHORTCODE_DROPDOWN_VISIBLE` | `block_editor_ranking_shortcode_dropdown_visible` | 1 | 1 | lib/page-settings/editor-posts.php:60 | 同一 | 同一 | 合格 |
| 486 | エディター | `OP_BLOCK_EDITOR_STYLE_BLOCK_OPTION_VISIBLE` | `block_editor_style_block_option_visible` | 1 | 1 | lib/page-settings/editor-posts.php:63 | 同一 | 同一 | 合格 |
| 487 | エディター | `OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_A` | `block_editor_extended_palette_color_a` | 1 | 1 | lib/page-settings/editor-posts.php:66 | 同一 | 同一 | 合格 |
| 488 | エディター | `OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_B` | `block_editor_extended_palette_color_b` | 1 | 1 | lib/page-settings/editor-posts.php:69 | 同一 | 同一 | 合格 |
| 489 | エディター | `OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_C` | `block_editor_extended_palette_color_c` | 1 | 1 | lib/page-settings/editor-posts.php:72 | 同一 | 同一 | 合格 |
| 490 | エディター | `OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_D` | `block_editor_extended_palette_color_d` | 1 | 1 | lib/page-settings/editor-posts.php:75 | 同一 | 同一 | 合格 |
| 491 | エディター | `OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_E` | `block_editor_extended_palette_color_e` | 1 | 1 | lib/page-settings/editor-posts.php:78 | 同一 | 同一 | 合格 |
| 492 | エディター | `OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_F` | `block_editor_extended_palette_color_f` | 1 | 1 | lib/page-settings/editor-posts.php:81 | 同一 | 同一 | 合格 |
| 493 | エディター | `OP_ADMIN_EDITOR_COUNTER_VISIBLE` | `admin_editor_counter_visible` | 1 | 1 | lib/page-settings/editor-posts.php:84 | 同一 | 同一 | 合格 |
| 494 | エディター | `OP_CONFIRMATION_BEFORE_PUBLISH` | `confirmation_before_publish` | 1 | 1 | lib/page-settings/editor-posts.php:87 | 同一 | 同一 | 合格 |
| 495 | API | `OP_AMAZON_API_ACCESS_KEY_ID` | `amazon_api_access_key_id` | 1 | 1 | lib/page-settings/apis-posts.php:11 | 同一 | 同一 | 合格 |
| 496 | API | `OP_AMAZON_API_SECRET_KEY` | `amazon_api_secret_key` | 1 | 1 | lib/page-settings/apis-posts.php:14 | 同一 | 同一 | 合格 |
| 497 | API | `OP_AMAZON_ASSOCIATE_TRACKING_ID` | `amazon_associate_tracking_id` | 1 | 1 | lib/page-settings/apis-posts.php:17 | 同一 | 同一 | 合格 |
| 498 | API | `OP_AMAZON_CREATORS_API_CREDENTIAL_ID` | `amazon_creators_api_credential_id` | 1 | 1 | lib/page-settings/apis-posts.php:20 | 同一 | 同一 | 合格 |
| 499 | API | `OP_AMAZON_CREATORS_API_SECRET` | `amazon_creators_api_secret` | 1 | 1 | lib/page-settings/apis-posts.php:23 | 同一 | 同一 | 合格 |
| 500 | API | `OP_AMAZON_SEARCH_BUTTON_VISIBLE` | `amazon_search_button_visible` | 1 | 1 | lib/page-settings/apis-posts.php:29 | 同一 | 同一 | 合格 |
| 501 | API | `OP_AMAZON_ITEM_CATALOG_IMAGE_VISIBLE` | `amazon_item_catalog_image_visible` | 1 | 1 | lib/page-settings/apis-posts.php:32 | 同一 | 同一 | 合格 |
| 502 | API | `OP_AMAZON_ITEM_PRICE_VISIBLE` | `amazon_item_price_visible` | 1 | 1 | lib/page-settings/apis-posts.php:35 | 同一 | 同一 | 合格 |
| 503 | API | `OP_AMAZON_ITEM_PRICE_TYPE` | `amazon_item_price_type` | 1 | 1 | lib/page-settings/apis-posts.php:39 | 同一 | 同一 | 合格 |
| 504 | API | `OP_AMAZON_ITEM_DESCRIPTION_VISIBLE` | `amazon_item_description_visible` | 1 | 1 | lib/page-settings/apis-posts.php:48 | 同一 | 同一 | 合格 |
| 505 | API | `OP_AMAZON_ITEM_CUSTOMER_REVIEWS_VISIBLE` | `amazon_item_customer_reviews_visible` | 1 | 1 | lib/page-settings/apis-posts.php:51 | 同一 | 同一 | 合格 |
| 506 | API | `OP_AMAZON_ITEM_CUSTOMER_REVIEWS_TEXT` | `amazon_item_customer_reviews_text` | 1 | 1 | lib/page-settings/apis-posts.php:54 | 同一 | 同一 | 合格 |
| 507 | API | `OP_AMAZON_ITEM_LOGO_VISIBLE` | `amazon_item_logo_visible` | 1 | 1 | lib/page-settings/apis-posts.php:57 | 同一 | 同一 | 合格 |
| 508 | API | `OP_AMAZON_SEARCH_BUTTON_TEXT` | `amazon_search_button_text` | 1 | 1 | lib/page-settings/apis-posts.php:60 | 同一 | 同一 | 合格 |
| 509 | API | `OP_RAKUTEN_APPLICATION_ID` | `rakuten_application_id` | 1 | 1 | lib/page-settings/apis-posts.php:63 | 同一 | 同一 | 合格 |
| 510 | API | `OP_RAKUTEN_AFFILIATE_ID` | `rakuten_affiliate_id` | 1 | 1 | lib/page-settings/apis-posts.php:66 | 同一 | 同一 | 合格 |
| 511 | API | `OP_RAKUTEN_ACCESS_KEY` | `rakuten_access_key` | 1 | 1 | lib/page-settings/apis-posts.php:69 | 同一 | 同一 | 合格 |
| 512 | API | `OP_GET_RAKUTEN_API_SORT` | `get_rakuten_api_sort` | 1 | 1 | lib/page-settings/apis-posts.php:72 | 同一 | 同一 | 合格 |
| 513 | API | `OP_RAKUTEN_ITEM_PRICE_VISIBLE` | `rakuten_item_price_visible` | 1 | 1 | lib/page-settings/apis-posts.php:75 | 同一 | 同一 | 合格 |
| 514 | API | `OP_RAKUTEN_ITEM_DESCRIPTION_VISIBLE` | `rakuten_item_description_visible` | 1 | 1 | lib/page-settings/apis-posts.php:78 | 同一 | 同一 | 合格 |
| 515 | API | `OP_RAKUTEN_ITEM_LOGO_VISIBLE` | `rakuten_item_logo_visible` | 1 | 1 | lib/page-settings/apis-posts.php:81 | 同一 | 同一 | 合格 |
| 516 | API | `OP_RAKUTEN_SEARCH_BUTTON_VISIBLE` | `rakuten_search_button_visible` | 1 | 1 | lib/page-settings/apis-posts.php:84 | 同一 | 同一 | 合格 |
| 517 | API | `OP_RAKUTEN_SEARCH_BUTTON_TEXT` | `rakuten_search_button_text` | 1 | 1 | lib/page-settings/apis-posts.php:87 | 同一 | 同一 | 合格 |
| 518 | API | `OP_RAKUTEN_BUTTON_SEARCH_TO_DETAIL` | `rakuten_button_search_to_detail` | 1 | 1 | lib/page-settings/apis-posts.php:90 | 同一 | 同一 | 合格 |
| 519 | API | `OP_AMAZON_BUTTON_SEARCH_TO_DETAIL` | `amazon_button_search_to_detail` | 1 | 1 | lib/page-settings/apis-posts.php:93 | 同一 | 同一 | 合格 |
| 520 | API | `OP_YAHOO_VALUECOMMERCE_SID` | `yahoo_valuecommerce_sid` | 1 | 1 | lib/page-settings/apis-posts.php:96 | 同一 | 同一 | 合格 |
| 521 | API | `OP_YAHOO_VALUECOMMERCE_PID` | `yahoo_valuecommerce_pid` | 1 | 1 | lib/page-settings/apis-posts.php:99 | 同一 | 同一 | 合格 |
| 522 | API | `OP_YAHOO_SEARCH_BUTTON_VISIBLE` | `yahoo_search_button_visible` | 1 | 1 | lib/page-settings/apis-posts.php:102 | 同一 | 同一 | 合格 |
| 523 | API | `OP_YAHOO_SEARCH_BUTTON_TEXT` | `yahoo_search_button_text` | 1 | 1 | lib/page-settings/apis-posts.php:105 | 同一 | 同一 | 合格 |
| 524 | API | `OP_MERCARI_AFFILIATE_ID` | `mercari_affiliate_id` | 1 | 1 | lib/page-settings/apis-posts.php:108 | 同一 | 同一 | 合格 |
| 525 | API | `OP_MERCARI_SEARCH_BUTTON_VISIBLE` | `mercari_search_button_visible` | 1 | 1 | lib/page-settings/apis-posts.php:111 | 同一 | 同一 | 合格 |
| 526 | API | `OP_MERCARI_SEARCH_BUTTON_TEXT` | `mercari_search_button_text` | 1 | 1 | lib/page-settings/apis-posts.php:114 | 同一 | 同一 | 合格 |
| 527 | API | `OP_DMM_AFFILIATE_ID` | `dmm_affiliate_id` | 1 | 1 | lib/page-settings/apis-posts.php:117 | 同一 | 同一 | 合格 |
| 528 | API | `OP_DMM_SEARCH_BUTTON_VISIBLE` | `dmm_search_button_visible` | 1 | 0 | lib/page-settings/apis-posts.php:120 | 同一 | 同一 | 合格 |
| 529 | API | `OP_DMM_SEARCH_BUTTON_TEXT` | `dmm_search_button_text` | 1 | 1 | lib/page-settings/apis-posts.php:123 | 同一 | 同一 | 合格 |
| 530 | API | `OP_MOSHIMO_AFFILIATE_LINK_ENABLE` | `moshimo_affiliate_link_enable` | 1 | 1 | lib/page-settings/apis-posts.php:126 | 同一 | 同一 | 合格 |
| 531 | API | `OP_MOSHIMO_AMAZON_ID` | `moshimo_amazon_id` | 1 | 1 | lib/page-settings/apis-posts.php:129 | 同一 | 同一 | 合格 |
| 532 | API | `OP_MOSHIMO_RAKUTEN_ID` | `moshimo_rakuten_id` | 1 | 1 | lib/page-settings/apis-posts.php:132 | 同一 | 同一 | 合格 |
| 533 | API | `OP_MOSHIMO_YAHOO_ID` | `moshimo_yahoo_id` | 1 | 1 | lib/page-settings/apis-posts.php:135 | 同一 | 同一 | 合格 |
| 534 | API | `OP_API_CACHE_RETENTION_PERIOD` | `api_cache_retention_period` | 1 | 1 | lib/page-settings/apis-posts.php:138 | 同一 | 同一 | 合格 |
| 535 | API | `OP_API_ERROR_MAIL_ENABLE` | `api_error_mail_enable` | 1 | 1 | lib/page-settings/apis-posts.php:141 | 同一 | 同一 | 合格 |
| 536 | API | `OP_PRODUCT_BLOCK_AUTO_UPDATE_ENABLE` | `product_block_auto_update_enable` | 1 | 1 | lib/page-settings/apis-posts.php:144 | 同一 | 同一 | 合格 |
| 537 | API | `OP_PRODUCT_BLOCK_AUTO_UPDATE_INTERVAL` | `product_block_auto_update_interval` | 1 | 1 | lib/page-settings/apis-posts.php:147 | 同一 | 同一 | 合格 |
| 538 | API | `OP_PRODUCT_BLOCK_AUTO_UPDATE_BATCH_SIZE` | `product_block_auto_update_batch_size` | 1 | 1 | lib/page-settings/apis-posts.php:150 | 同一 | 同一 | 合格 |
| 539 | その他 | `OP_EASY_SSL_ENABLE` | `easy_ssl_enable` | 2 | 1 | lib/page-settings/others-posts.php:11 | 同一 | 同一 | 合格 |
| 540 | その他 | `OP_REQUEST_FILESYSTEM_CREDENTIALS_ENABLE` | `request_filesystem_credentials_enable` | 1 | 1 | lib/page-settings/others-posts.php:14 | 同一 | 同一 | 合格 |
| 541 | その他 | `OP_MIGRATE_FROM_SIMPLICITY` | `migrate_from_simplicity` | 1 | 1 | lib/page-settings/others-posts.php:17 | 同一 | 同一 | 合格 |
| 542 | その他 | `OP_AUTO_POST_SLUG_ENABLE` | `auto_post_slug_enable` | 1 | 1 | lib/page-settings/others-posts.php:20 | 同一 | 同一 | 合格 |

## 別系統の既存処理

- `editor-posts.php`の旧キー移行用`set_theme_mod()`は改修前後で同一です。通常のフォーム保存オプション数には含めていません。
- リセットは全theme_modを対象にする別操作です。呼出と確認条件は改修前後で同一です。実クリックの有無は自動生成領域外の検証証跡へ記録します。
- `cocoon_settings_before_save`と`cocoon_settings_after_save`は維持されています。子テーマ・プラグインがフック内で扱う独自データはCocoonコア全件表の対象外です。
- `cocoon_sns_share_options`フィルターで追加される外部SNS設定は実行環境依存です。コア定義と動的保存ループ自体の同一性を確認しています。

<!-- END AUTO-GENERATED STATIC DATA CONTRACT -->

## 記録時点の実ブラウザー保存・再読込結果

以下は2026年8月26日にローカルWordPress（`localhost:8085`）で記録した証跡です。設定値を意図的に変更せず上部の「変更をまとめて保存」を実クリックし、保存成功通知と同一URLへの再読込を確認しました。この節は自動生成対象外であり、対象コードまたは環境を変更した場合は再試験して日時と結果を手動更新します。

| 比較対象 | 保存前 | 保存・再読込後 | 比較方法 | 記録結果 |
| --- | ---: | ---: | --- | --- |
| 名前付きフォーム要素（nonce・`select_index`を除外） | 872 | 872 | 順序、tag、type、name、value、checked、disabled、select選択値をメモリ内で完全比較 | 一致 |
| POST成功コントロール（nonce・`select_index`を除外） | 374 | 374 | nameと送信値を順序込みでメモリ内比較 | 一致 |
| 新ナビゲーション内の名前付き要素 | 0 | 0 | DOM件数 | 一致 |
| 新ナビゲーション内のsubmit要素 | 0 | 0 | DOM件数 | 一致 |

この実ブラウザー試験は、その時点で画面へ生成された設定を一括して検証しています。AMP・PWAのような条件未成立項目、スキンにより非表示の項目、外部フィルターが後付けする項目は静的契約比較で補完します。リセットは現在の利用DBを破壊するため実クリックしていません。raw DB行のダンプは機密保護のため取得・保存していません。

## 機密情報の取り扱い

- APIキー、トークン、認証ID、nonce、POST本文、DBダンプ、実設定値を文書・コンソールへ出力しない
- 値の一致比較はブラウザー実行中のメモリ内だけで行い、値ハッシュも報告書へ永続化しない
- 本表のtheme_modキーは公開ソース中の保存先識別子であり、利用者の設定値ではない

## 再現手順

```powershell
git branch --show-current
php scripts/audit-cocoon-settings-data-contract.php --check
php scripts/audit-cocoon-settings-data-contract.php
php scripts/audit-cocoon-settings-data-contract.php --check
npm run lint:settings
npm run test:settings-navigation
npm run test:settings-css
npm run test:i18n
.\vendor\bin\phpunit --do-not-cache-result --testsuite unit --exclude-group distribution
.\vendor\bin\phpunit --do-not-cache-result --group distribution
.\vendor\bin\phpunit --do-not-cache-result tests\Unit\CocoonSettingsDesignContractTest.php tests\Unit\CocoonSettingsNavigationModeTest.php
```

最初の`--check`は文書を書き換えず、静的契約または自動生成領域が古ければ終了コード1で失敗します。通常実行はマーカー内だけを更新し、記録時点の証跡を保持します。その後にもう一度`--check`を実行して鮮度を確認します。実ブラウザー試験はログイン済みのローカル環境で、設定値を変更せず実施します。

## 制約

- 全件表はCocoonコアの保存実行経路を対象とし、子テーマ・プラグインがフックやフィルターで追加する独自設定は含めない
- 実ブラウザー試験は現在のローカルDBで生成された項目を対象とし、条件未成立項目はソース完全一致で保証する
- raw DBシリアライズ文字列のバイト比較ではなく、保存・読込コードの完全一致と、再読込後フォーム状態の一致を組み合わせて判定する

## 関連テスト

- `tests/Unit/CocoonSettingsDesignContractTest.php`: nonce、フォーム、38タブ、上下保存、新UIの保存分離
- `tests/Unit/CocoonSettingsNavigationModeTest.php`: 許可された表示モードだけを専用user_optionへ保存
- `tests/js/cocoon-settings-navigation.test.js`: 合成フォームでFormData不変、スキン非表示、幅変更、通信例外等を実行確認
- `tests/Unit/DistributionFilesTest.php`: 実行ファイルの配布収録と内部監査文書の除外

## 2026年8月26日時点のテスト実行記録

以下は記録時点の実測値であり、自動生成されません。コードまたはテストを変更した場合は上記コマンドを再実行し、件数と結果を手動更新します。

| 範囲 | 再現コマンド | 記録結果 |
| --- | --- | --- |
| データ契約監査 | `php scripts/audit-cocoon-settings-data-contract.php --check` | 合格。542設定、未解決0、重複0、差分0、自動生成領域は最新 |
| 全Unitテスト（distribution除外） | `.\vendor\bin\phpunit --do-not-cache-result --testsuite unit --exclude-group distribution` | 合格。1316テスト、12198アサーション |
| 設定UI対象PHPUnit | `.\vendor\bin\phpunit --do-not-cache-result tests\Unit\CocoonSettingsDesignContractTest.php tests\Unit\CocoonSettingsNavigationModeTest.php` | 合格。24テスト、1094アサーション |
| 実配布tar検査 | `.\vendor\bin\phpunit --do-not-cache-result --group distribution` | 合格。1テスト、41アサーション |
| 設定UI lint | `npm run lint:settings` | 合格 |
| ナビゲーション実行型DOMテスト | `npm run test:settings-navigation` | 合格 |
| SCSS/CSS完全同期テスト | `npm run test:settings-css` | 合格 |
| 翻訳整合テスト | `npm run test:i18n` | 合格。8言語を検証 |
