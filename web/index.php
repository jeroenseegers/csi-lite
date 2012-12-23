<?php

include_once 'includes/repository.php';
include_once 'includes/settings.php';
include_once 'includes/functions.php';

header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
header('Expires: Thu, 14 Sep 1978 05:00:00 GMT'); // Date in the past
session_start();

#------------------------- SESSION INVALIDATOR -------------------------
if (isset($_SESSION['PrevDate'])) {
    if($_SESSION['PrevDate'] < strtotime($GLOBALS['SESSION_TIMEOUT_MINUTES'].' minutes ago',strtotime('now'))) {
        if (isset($_SESSION['screen'])) {
            $screen=$_SESSION['screen'];
        }
        session_unset();
        if (isset($screen)) {
            $_SESSION['screen']=$screen;
        }
    }
}

$_SESSION['PrevDate']=strtotime('now');

#------------------------- PREPARE VARIABLES -------------------------
#Get repository information
$repository = array();
$repository = GetRepository($GLOBALS['REPOSITORY_URL'], $repository);

#screen switch detected
if (isset($_POST['toscreen'])) {
    $_SESSION['screen'] = $_POST['toscreen'];
}

#Previous selected App
if (isset($_POST['SelectedItem'])) {
    $_SESSION['SelectedItem']=$_POST['SelectedItem'];
}
if (isset($_SESSION['SelectedItem'])) {
    $selectedname=$_SESSION['SelectedItem'];
}

#get selected screen
if (isset($_SESSION['screen'])) {
    $screen=$_SESSION['screen'];
}
else {
    $screen='apps';
}

#action variable
if (isset($_POST['action'])) {
    $action=$_POST['action'];
}

#------------------------- PROCESS ACTION -------------------------
if (isset($action)) {

    switch($screen) {
        case 'updates':
            $list = GetApplicationUpdates($repository);
            break;
        case 'apps':
            $list = GetApplications($repository);
            break;
        case 'installed':
            $list = GetInstalledApplications();
            break;
        case 'themes':
            $list = GetThemes($repository);
            break;
        case 'wait_images':
            $list = GetWaitImageSets($repository);
            break;
        case 'menus':
            $list = GetCustomMenus($repository);
            break;
        case 'webservices':
            $list = GetWebservices($repository);
            break;
    }

    foreach($list as $o) {
        if (!isset($selectedname)) {
            $selectedname=$o['Name'].' '.$o['Version'];
        }
        if (!isset($selecteditem)) {
            $selecteditem=$o;
        }
        if ($selectedname==$o['Name'].' '.$o['Version']) {
            $selecteditem=$o;
            break;
        }
    }

    if ($action == 'install') {
        switch ($screen) {
            case 'apps':
            case 'updates':
                $installresult=InstallApplication($selecteditem);
                break;
            case 'themes':
                $installresult=InstallTheme($selecteditem['DownloadURL']);
                break;
            case 'wait_images':
                $installresult=InstallWaitImageSet($selecteditem['DownloadURL']);
                break;
            case 'menus':
                $installresult=InstallCustomMenu($selecteditem['DownloadURL']);
                break;
            case 'webservices':
                $installresult=InstallWebservice($selecteditem['Name'], $selecteditem['WebserviceUrl']);
                break;
        }
    }

    if ($action=='Uninstall') {
        switch ($screen) {
        case 'themes':
            UninstallTheme();
            break;
        case 'menus':
            UninstallCustomMenu();
            break;
        case 'wait_images':
            UninstallWaitImageSet();
            break;
        }
    }

    if ($screen == 'installed') {
        $AppName = substr($selectedname,0,strrpos($selectedname,' '));
        switch ($action) {
            case 'start':
                ApplicationAction($AppName, 'start');
                break;
            case 'stop':
                ApplicationAction($AppName, 'stop');
                break;
            case 'restart':
                ApplicationAction($AppName, 'restart');
                break;
            case 'uninstall':
                ApplicationAction($AppName, 'uninstall');
                break;
            case 'enable':
                ApplicationAction($AppName, 'enable');
                break;
            case 'disable':
                ApplicationAction($AppName, 'disable');
                break;
        }
    }
}

#------------------------- GET OUTPUT INFORMATION -------------------------
$updates=GetApplicationUpdates($repository);
if (($screen=='updates') && (count($updates)==0)) {
    $screen='apps';
    $_SESSION['screen']=$screen;
}

switch($screen) {
    case 'updates':
        $list = $updates;
        break;
    case 'apps':
        $list = GetApplications($repository);
        break;
    case 'installed':
        $list = GetInstalledApplications();
        break;
    case 'themes':
        $list = GetThemes($repository);
        break;
    case 'wait_images':
        $list = GetWaitImageSets($repository);
        break;
    case 'menus':
        $list = GetCustomMenus($repository);
        break;
    case 'webservices':
        $list = GetWebservices($repository);
        break;
}

#Find currently selected Item
foreach($list as $o) {
    if (!isset($selectedname)) {
        $selectedname=$o['Name'].' '.$o['Version'];
    }
    if (!isset($selecteditem)) {
        $selecteditem=$o;
    }
    if ($selectedname==$o['Name'].' '.$o['Version']) {
        $selecteditem=$o;
        break;
    }
}

#------------------------- COMPILE OUTPUT -------------------------
#options
$options='';
foreach($list as $o) {
    if ($selectedname==$o['Name'].' '.$o['Version']) {
        $options.='<option value="'.$o['Name'].' '.$o['Version'].'" SELECTED>'.$o['Name'].' '.$o['Version'].'</option>';
    }
    else {
        $options.='<option value="'.$o['Name'].' '.$o['Version'].'">'.$o['Name'].' '.$o['Version'].'</option>';
    }
}

#Screenshots
$screenshots='';
$arr = (array)$selecteditem['Screenshots'];
if (is_string($arr['URL'])) {
    $screenshots.='<a href="screenshot.php?ScreenshotURL='.$arr['URL'].'"><img width="100" src="'.$arr['URL'].'" border="0"></a>&nbsp;&nbsp;';
}
else {
    foreach($arr as $key => $value) {
        foreach($value as $url) {
            $screenshots.='<a href="screenshot.php?ScreenshotURL='.$url.'"><img width="100" src="'.$url.'" border="0"></a>&nbsp;&nbsp;';
        }
    }
}

include 'header.php';

switch ($screen) {
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
