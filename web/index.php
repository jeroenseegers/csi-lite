<?php
    include_once "Includes/repository.php";
    include_once "Includes/settings.php";
    include_once "Includes/functions.php";
    
    
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Thu, 14 Sep 1978 05:00:00 GMT"); // Date in the past
    session_start();
    
    


    #------------------------- SESSION INVALIDATOR -------------------------
    if (isset($_SESSION['PrevDate']))
    {
        if($_SESSION['PrevDate'] < strtotime($GLOBALS['SESSION_TIMEOUT_MINUTES']." minutes ago",strtotime('now'))) {
            if (isset($_SESSION["screen"]))
            {
                $screen=$_SESSION["screen"];
            }
            session_unset();
            if (isset($screen))
            {
                $_SESSION["screen"]=$screen;
            }
        } 
    }
    $_SESSION['PrevDate']=strtotime('now');



    #------------------------- PREPARE VARIABLES -------------------------
    #Get repository information
    $repository = array();
    $repository = GetRepository($GLOBALS['REPOSITORY_URL'], $repository);

    #screen switch detected
    if (isset($_POST["toscreen"])) {
        $_SESSION['screen'] = $_POST["toscreen"];
    }

    #Previous selected App
    if (isset($_POST["SelectedItem"])) {
        $_SESSION['SelectedItem']=$_POST["SelectedItem"];
    }
    if (isset($_SESSION['SelectedItem']))
    {
        $selectedname=$_SESSION['SelectedItem'];
    }
    
    #get selected screen
    if (isset($_SESSION['screen'])) {
        $screen=$_SESSION['screen'];
    } else {
        $screen="Apps";
    }
    
    #action variable
    if (isset($_POST["action"]))
    {
        $action=$_POST["action"];
    }
    
    
    
    #------------------------- PROCESS ACTION -------------------------
    if (isset($action))
    {
    
        switch($screen) {
            case "Updates":
                $list = GetApplicationUpdates($repository);
                break;
            case "Apps":
                $list = GetApplications($repository);
                break;
            case "Installed":
                $list = GetInstalledApplications();
                break;
            case "Themes":
                $list = GetThemes($repository);
                break;
            case "Wait Images":
                $list = GetWaitImageSets($repository);
                break;
            case "Menus":
                $list = GetCustomMenus($repository);
                break;
            case "Webservices":
                $list = GetWebservices($repository);
                break;
        }
        
        foreach($list as $o)
        {
            if (!isset($selectedname))
            {
                $selectedname=$o['Name']." ".$o['Version'];
            }
            if (!isset($selecteditem))
            {
                $selecteditem=$o;
            }
            if ($selectedname==$o['Name']." ".$o['Version'])
            {
                $selecteditem=$o;
                break;
            }
        }
        
        if ($action=="Install")
        {
            if (($screen=="Apps") || ($screen=="Updates"))
            {
                $installresult=InstallApplication($selecteditem);
            }
            else
            if ($screen=="Themes")
            {
                $installresult=InstallTheme($selecteditem['DownloadURL']);
            }
            else
            if ($screen=="Wait Images")
            {
                $installresult=InstallWaitImageSet($selecteditem['DownloadURL']);
            }
            else
            if ($screen=="Menus")
            {
                $installresult=InstallCustomMenu($selecteditem['DownloadURL']);
            }
            else
            if ($screen=="Webservices")
            {
                $installresult=InstallWebservice($selecteditem['Name'], $selecteditem['WebserviceUrl']);
            }
        }
        
        if ($action=="Uninstall")
        {
            if ($screen=="Themes")
            {
                UninstallTheme();
            }
            else
            if ($screen=="Menus")
            {
                UninstallCustomMenu();
            }
            else
            if ($screen=="Wait Images")
            {
                UninstallWaitImageSet();
            }
        }
        
        if ($screen=="Installed")
        {
            $AppName=substr($selectedname,0,strrpos($selectedname,' '));
            switch ($action) {
                case "Start":
                    ApplicationAction($AppName,"start");
                    break;
                case "Stop":
                    ApplicationAction($AppName,"stop");
                    break;
                case "Restart":
                    ApplicationAction($AppName,"restart");
                    break;
                case "Uninstall":
                    ApplicationAction($AppName,"uninstall");
                    break;
                case "Enable":
                    ApplicationAction($AppName,"enable");
                    break;
                case "Disable":
                    ApplicationAction($AppName,"disable");
                    break;
            }
        }
    }
    
    
    
    
    #------------------------- GET OUTPUT INFORMATION -------------------------
    $updates=GetApplicationUpdates($repository);
    if (($screen=="Updates") && (count($updates)==0))
    {
        $screen="Apps";
        $_SESSION['screen']=$screen;
    }
    
    switch($screen) {
        case "Updates":
            $list = $updates;
            break;
        case "Apps":
            $list = GetApplications($repository);
            break;
        case "Installed":
            $list = GetInstalledApplications();
            break;
        case "Themes":
            $list = GetThemes($repository);
            break;
        case "Wait Images":
            $list = GetWaitImageSets($repository);
            break;
        case "Menus":
            $list = GetCustomMenus($repository);
            break;
        case "Webservices":
            $list = GetWebservices($repository);
            break;
    }
   

    #Find currently selected Item
    foreach($list as $o)
    {
        if (!isset($selectedname))
        {
            $selectedname=$o['Name']." ".$o['Version'];
        }
        if (!isset($selecteditem))
        {
            $selecteditem=$o;
        }
        if ($selectedname==$o['Name']." ".$o['Version'])
        {
            $selecteditem=$o;
            break;
        }
    }




    #------------------------- COMPILE OUTPUT -------------------------
    #options
    $options="";
    foreach($list as $o)
    {
        if ($selectedname==$o['Name']." ".$o['Version'])
        {
            $options.="<option value=\"".$o['Name']." ".$o['Version']."\" SELECTED>".$o['Name']." ".$o['Version']."</option>";
        }
        else
        {
            $options.="<option value=\"".$o['Name']." ".$o['Version']."\">".$o['Name']." ".$o['Version']."</option>";
        }
    }

    #Screenshots
    $screenshots="";
    $arr = (array)$selecteditem['Screenshots'];
    if (is_string($arr['URL']))
    {
          $screenshots.="<a href=\"screenshot.php?ScreenshotURL=".$arr['URL']."\"><img width=\"100\" src=".$arr['URL']." border=0></a>&nbsp;&nbsp;"; 
    }
    else
    {
      foreach($arr as $key=>$value) 
      { 
        foreach($value as $url) 
        { 
          $screenshots.="<a href=\"screenshot.php?ScreenshotURL=".$url."\"><img width=\"100\" src=".$url." border=0></a>&nbsp;&nbsp;"; 
        }
      }
    }

    include "header.php";
    switch ($screen) {
        case 'Updates':
            include "Templates/Updates.php";
            break;
        case 'Apps':
            include "Templates/Applications.php";
            break;
        case 'Installed':
            include "Templates/Installed.php";
            break;
        case 'Themes':
            include "Templates/Themes.php";
            break;
        case 'Wait Images':
            include "Templates/WaitImages.php";
            break;
        case 'Menus':
            include "Templates/CustomMenus.php";
            break;
        case 'Webservices':
            include "Templates/Webservices.php";
            break;
        default:
            include "Templates/Applications.php";
    }
    include "footer.php";
?>