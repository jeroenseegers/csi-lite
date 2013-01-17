<?php

/**
 * This file holds the settings used by the csi-lite application.
 *
 * ONLY CHANGE IF YOU KNOW WHAT YOU'RE DOING!
 */

date_default_timezone_set('Europe/Amsterdam');

$aSettings['VERSION']            = '1.3';

$aSettings['MAIN_DIR']           = '/share/Apps/csi-lite/';
$aSettings['TEMP_DIR']           = $aSettings['MAIN_DIR'] .'temp/';

$aSettings['INSTALLPREPARE_URL'] = 'http://78.46.108.209:8100/installprepare.cgi';
$aSettings['APPINIT_URL']        = 'http://78.46.108.209:8100/appinit.cgi';

$aSettings['SESSION_TIMEOUT']    = 5; // Timeout in minutes

$aSettings['DRIVE']              = 'SATA_DISK';


/**
 * COMPATIBILITY SETTINGS
 */
if (is_dir('/opt/sybhttpd/localhost.drives/HARD_DISK/')) {
    $aSettings['DRIVE']          = 'HARD_DISK';
}

if (isset($_SESSION['SET_DEVICE'])) {
    $aSettings['DEVICE_TYPE'] = $_SESSION['SET_DEVICE'];
} else {
    $aSettings['DEVICE_TYPE'] = file_get_contents($aSettings['MAIN_DIR'] .'/device');
}

switch($aSettings['DEVICE_TYPE']) {
    case 'A/C-200':
    case 'A/C-300':
        $aSettings['REPOSITORY_URL'] = 'http://78.46.108.209:8100/RepositoryInfo_C200.zip';
    break;

    case 'A-400':
        $aSettings['REPOSITORY_URL'] = 'http://78.46.108.209:8100/RepositoryInfo_A400.zip';
    break;

    default:
        // A-1xx/B-110
        $aSettings['REPOSITORY_URL']     = 'http://78.46.108.209:8100/RepositoryInfo.zip';
    break;
}
