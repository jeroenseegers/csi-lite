<?php

error_reporting(E_STRICT | E_ALL);
ini_set('display_errors', 'On');

include_once '../shared/settings.php';
include_once '../shared/functions.php';
include_once '../shared/repository.php';

header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
header('Expires: Thu, 14 Sep 1978 05:00:00 GMT'); // Date in the past
session_start();

// Reset the current session if $_GET['rc'] parameter is given
if (isset($_GET['rc']) && $_GET['rc'] == 1) {
    session_unset();
}

#------------------------- SESSION INVALIDATOR -------------------------
if (isset($_SESSION['PrevDate'])) {
    if($_SESSION['PrevDate'] < strtotime($aSettings['SESSION_TIMEOUT'].' minutes ago', strtotime('now'))) {
        if (isset($_SESSION['screen'])) {
            $sScreen = $_SESSION['screen'];
        }
        session_unset();
        if (isset($sScreen)) {
            $_SESSION['screen'] = $sScreen;
        }
    }
}

$_SESSION['PrevDate'] = strtotime('now');

#------------------------- PREPARE VARIABLES -------------------------

// Get repository information
$aRepository = array();
$sRepositoryName = str_replace('zip', 'xml', substr($aSettings['REPOSITORY_URL'], strrpos($aSettings['REPOSITORY_URL'], '/') + 1));
download_file($aSettings['REPOSITORY_URL'], $aSettings, TRUE);
$aRepository = get_repository($aSettings['TEMP_DIR'].$sRepositoryName, $aRepository, $aSettings);

// Screen switch detected
if (isset($_POST['toscreen'])) {
    $_SESSION['screen'] = $_POST['toscreen'];
}

// Previous selected App
if (isset($_POST['SelectedItem'])) {
    $_SESSION['SelectedItem'] = $_POST['SelectedItem'];
}
if (isset($_SESSION['SelectedItem'])) {
    $sSelectedName = $_SESSION['SelectedItem'];
}

// Get selected screen
if (isset($_SESSION['screen'])) {
    $sScreen = $_SESSION['screen'];
}
else {
    $sScreen = 'apps';
}

// Action variable
if (isset($_POST['action'])) {
    $sAction = $_POST['action'];
}

#------------------------- PROCESS ACTION -------------------------
if (isset($sAction)) {

    switch($sScreen) {
        case 'updates':
            $aList = get_application_updates($aRepository);
            break;
        case 'apps':
            $aList = get_category($aRepository, 'applications');
            break;
        case 'installed':
            $aList = get_installed_applications();
            break;
        case 'themes':
            $aList = get_category($aRepository, 'themes');
            break;
        case 'wait_images':
            $aList = get_category($aRepository, 'waitimagesets');
            break;
        case 'menus':
            $aList = get_category($aRepository, 'custommenus');
            break;
        case 'webservices':
            $aList = get_category($aRepository, 'webservices');
            break;
    }

    foreach($aList as $oInfo) {
        if (!isset($sSelectedName)) {
            $sSelectedName = $oInfo->Name.' '.$oInfo->Version;
        }
        if (!isset($oSelectedItem)) {
            $oSelectedItem = $oInfo;
        }
        if ($sSelectedName == $oInfo->Name.' '.$oInfo->Version) {
            $oSelectedItem = $oInfo;
            break;
        }
    }

    if ($sAction == 'install') {
        switch ($sScreen) {
            case 'apps':
            case 'updates':
                $sInstallResult = install_application($oSelectedItem, $aSettings);
                break;
            case 'themes':
                $sInstallResult = install_theme($oSelectedItem->DownloadURL, $aSettings);
                break;
            case 'wait_images':
                $sInstallResult = install_waitimageset($oSelectedItem->DownloadURL, $aSettings);
                break;
            case 'menus':
                $sInstallResult = install_custommenu($oSelectedItem->DownloadURL, $aSettings);
                break;
            case 'webservices':
                $sInstallResult = install_webservice($oSelectedItem->Name, $oSelectedItem->WebserviceUrl, $aSettings);
                break;
        }
    }

    if ($sAction == 'uninstall') {
        switch ($sScreen) {
            case 'themes':
                uninstall_theme();
                break;
            case 'menus':
                uninstall_custommenu();
                break;
            case 'wait_images':
                uninstall_waitimageset();
                break;
        }
    }

    if ($sScreen == 'installed') {
        $sAppName = substr($sSelectedName, 0, strrpos($sSelectedName, ' '));
        switch ($sAction) {
            case 'start':
                application_action($sAppName, 'start', $aSettings);
                break;
            case 'stop':
                application_action($sAppName, 'stop', $aSettings);
                break;
            case 'restart':
                application_action($sAppName, 'restart', $aSettings);
                break;
            case 'uninstall':
                application_action($sAppName, 'uninstall', $aSettings);
                break;
            case 'enable':
                application_action($sAppName, 'enable', $aSettings);
                break;
            case 'disable':
                application_action($sAppName, 'disable', $aSettings);
                break;
        }
    }
}

#------------------------- GET OUTPUT INFORMATION -------------------------
$aUpdates = get_application_updates($aRepository);
if (($sScreen == 'updates') && (count($aUpdates) == 0)) {
    $sScreen = 'apps';
    $_SESSION['screen'] = $sScreen;
}

switch($sScreen) {
    case 'updates':
        $aList = $aUpdates;
        break;
    case 'apps':
        $aList = get_category($aRepository, 'applications');
        break;
    case 'installed':
        $aList = get_installed_applications();
        break;
    case 'themes':
        $aList = get_category($aRepository, 'themes');
        break;
    case 'wait_images':
        $aList = get_category($aRepository, 'waitimagesets');
        break;
    case 'menus':
        $aList = get_category($aRepository, 'custommenus');
        break;
    case 'webservices':
        $aList = get_category($aRepository, 'webservices');
        break;
}

// Find currently selected item
foreach($aList as $oInfo) {
    if (!isset($sSelectedName)) {
        $sSelectedName = $oInfo->Name.' '.$oInfo->Version;
    }
    if (!isset($oSelectedItem)) {
        $oSelectedItem = $oInfo;
    }
    if ($sSelectedName == $oInfo->Name.' '.$oInfo->Version) {
        $oSelectedItem = $oInfo;
        break;
    }
}

#------------------------- COMPILE OUTPUT -------------------------
// Options
$sOptions = '';
foreach($aList as $oInfo) {
    if ($sSelectedName == $oInfo->Name.' '.$oInfo->Version) {
        $sOptions .= '<option value="'.$oInfo->Name.' '.$oInfo->Version.'" SELECTED>'.$oInfo->Name.' '.$oInfo->Version.'</option>';
    }
    else {
        $sOptions .= '<option value="'.$oInfo->Name.' '.$oInfo->Version.'">'.$oInfo->Name.' '.$oInfo->Version.'</option>';
    }
}

// Screenshots
$sScreenshots = '';
if (isset($oSelectedItem) && is_object($oSelectedItem)) {
    if (isset($oSelectedItem->Screenshots)) {
        $oScreenshots = $oSelectedItem->Screenshots;
        if (is_string($oScreenshots->URL)) {
            $sScreenshots .= '<a href="screenshot.php?ScreenshotURL='.$oScreenshots->URL.'"><img width="100" src="'.$oScreenshots->URL.'" border="0"></a>&nbsp;&nbsp;';
        }
        else {
            foreach($oScreenshots as $oScreenshot) {
                foreach($oScreenshot as $sUrl) {
                    $sScreenshots .= '<a href="screenshot.php?ScreenshotURL='.$sUrl.'"><img width="100" src="'.$sUrl.'" border="0"></a>&nbsp;&nbsp;';
                }
            }
        }
    }
} else {
    $oSelectedItem = new stdClass();
    $oSelectedItem->Name = '';
    $oSelectedItem->Version = '';
    $oSelectedItem->Author = '';
    $oSelectedItem->Description = '';
    $oSelectedItem->Started = 0;
    $oSelectedItem->Enabled = 0;
}

include 'header.php';

switch ($sScreen) {
    case 'updates':
        include 'templates/updates.php';
        break;
    case 'apps':
        include 'templates/applications.php';
        break;
    case 'installed':
        include 'templates/installed.php';
        break;
    case 'themes':
        include 'templates/themes.php';
        break;
    case 'wait_images':
        include 'templates/waitimages.php';
        break;
    case 'menus':
        include 'templates/custommenus.php';
        break;
    case 'webservices':
        include 'templates/webservices.php';
        break;
    default:
        include 'templates/applications.php';
}

include 'footer.php';
