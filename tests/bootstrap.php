<?php

/**
 * Bootstrap phpMyFAQ PHPUnit testing environment
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package phpMyFAQ
 * @author Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2015 phpMyFAQ Team
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link https://www.phpmyfaq.de
 * @since 2015-02-12
 */

use Composer\Autoload\ClassLoader;
use phpMyFAQ\Installer;

date_default_timezone_set('Europe/Berlin');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);

//
// The root directory
//
define('PMF_ROOT_DIR', dirname(__DIR__) . '/phpmyfaq');
define('PMF_CONFIG_DIR', dirname(__DIR__) . '/phpmyfaq/config');

const PMF_LOG_DIR = __DIR__ . '/logs';
const PMF_TEST_DIR = __DIR__;
const IS_VALID_PHPMYFAQ = true;
const DEBUG = true;

$_SERVER['HTTP_HOST'] = 'https://localhost/';

require PMF_CONFIG_DIR . '/constants.php';

//
// The include directory
//
define('PMF_SRC_DIR', dirname(__DIR__) . '/phpmyfaq/src');

//
// The directory where the translations reside
//
define('PMF_LANGUAGE_DIR', dirname(__DIR__) . '/phpmyfaq/lang');
require PMF_LANGUAGE_DIR . '/language_en.php';

//
// Setting up autoloader
//
$loader = new ClassLoader();
$loader->add('phpMyFAQ', PMF_SRC_DIR);
$loader->register();

//
// Delete possible SQLite file first
//
@unlink(PMF_TEST_DIR . '/test.db');

//
// Create database credentials for SQLite
//
$setup = [
    'dbServer' => PMF_TEST_DIR . '/test.db',
    'dbType' => 'sqlite3',
    'dbPort' => null,
    'dbDatabaseName' => '',
    'loginname' => 'admin',
    'password' => 'password',
    'password_retyped' => 'password',
    'rootDir' => PMF_TEST_DIR
];

$installer = new Installer();
try {
    $installer->startInstall($setup);
} catch (\phpMyFAQ\Exception $e) {
    echo $e->getMessage();
}

require PMF_TEST_DIR . '/config/database.php';
