<?php //AdSense AMP自動広告の</head>手前コード
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (is_all_linkswitch_enable()): ?>
<!-- LinkSwitch head script -->
<script async custom-element="amp-link-rewriter" src="https://cdn.ampproject.org/v0/amp-link-rewriter-0.1.js"></script>
<!-- End LinkSwitch head script -->
<?php endif ?>
