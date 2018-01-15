<?php
/**
 * User: andrew
 * Date: 13.12.2017
 * Time: 17:31.
 */
require dirname(__DIR__) . '/vendor/autoload.php';

define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_CHECK', true);
$GLOBALS['DBType'] = 'mysql';
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__) . '/web';

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

defined('TEST_ENVIRONMENT') or define('TEST_ENVIRONMENT', true);

global $DB;
$app = \Bitrix\Main\Application::getInstance();
$con = $app->getConnection();
$DB->db_Conn = $con->getResource();

$_SESSION['SESS_AUTH']['USER_ID'] = 1;
