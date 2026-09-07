<?php //クリック解析一括ローダー
/**
 * Cocoon WordPress Theme
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

require_once dirname(__FILE__) . '/settings-func.php';
require_once dirname(__FILE__) . '/statistics-func.php';
require_once dirname(__FILE__) . '/transaction-func.php';
require_once dirname(__FILE__) . '/schema-func.php';
require_once dirname(__FILE__) . '/normalize-func.php';
require_once dirname(__FILE__) . '/rest-func.php';
require_once dirname(__FILE__) . '/cron-func.php';
require_once dirname(__FILE__) . '/enqueue-func.php';
require_once dirname(__FILE__) . '/admin-query-func.php';
require_once dirname(__FILE__) . '/admin-actions.php';
