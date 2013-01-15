<?php

/**
 * This file holds the settings used by the csi-lite application.
 *
 * ONLY CHANGE IF YOU KNOW WHAT YOU'RE DOING!
 */

date_default_timezone_set('Europe/Amsterdam');

$aSettings['VERSION']            = '1.2';

$aSettings['MAIN_DIR']           = '/share/Apps/csi-lite/';
$aSettings['TEMP_DIR']           = $aSettings['MAIN_DIR'] .'temp/';

$aSettings['INSTALLPREPARE_URL'] = 'http://78.46.108.209:8100/installprepare.cgi';
$aSettings['APPINIT_URL']        = 'http://78.46.108.209:8100/appinit.cgi';

$aSettings['SESSION_TIMEOUT']    = 5; // Timeout in minutes

$aSettings['DRIVE']              = 'SATA_DISK';

$aSettings['REPOSITORY_URL']     = 'http://78.46.108.209:8100/RepositoryInfo.zip';

/**
 * COMPATIBILITY SETTINGS
 */
if (is_dir('/opt/sybhttpd/localhost.drives/HARD_DISK/')) {
    $aSettings['DRIVE']          = 'HARD_DISK';
}

if (is_dir('/nmt/apps/')) {
    $aSettings['REPOSITORY_URL'] = 'http://78.46.108.209:8100/RepositoryInfo_C200.zip';
}
