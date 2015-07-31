<?php
/**
 * @link      http://github.com/nfephp-org/sped-common for the canonical source repository
 * @copyright Copyright (c) 2008-2015 NFePHP.org (http://www.nfephp.org)
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPL v3 License
 */

error_reporting(E_ALL | E_STRICT);

if (class_exists('PHPUnit_Runner_Version', true)) {
    $phpUnitVersion = PHPUnit_Runner_Version::id();
    if ('@package_version@' !== $phpUnitVersion && version_compare($phpUnitVersion, '4.0.0', '<')) {
        echo 'This version of PHPUnit (' . PHPUnit_Runner_Version::id() . ') is not supported'
           . ' in the sped-common unit tests. Supported is version 4.0.0 or higher.'
           . ' See also the CONTRIBUTING.md file in the component root.' . PHP_EOL;
        exit(1);
    }
    unset($phpUnitVersion);
}
/**
 * Setup autoloading
 */
require __DIR__ . '/../vendor/autoload.php';
