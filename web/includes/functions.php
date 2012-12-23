<?php

function CleanTemp()
{
    exec("rm -Rf \"".$GLOBALS['TEMPDIR']."\"");
    exec("mkdir -p \"".$GLOBALS['TEMPDIR']."\"");
}

function recurse_copy($src,$dst) {
    $dir = opendir($src);
    if (!file_exists($dst))
    {
        mkdir($dst);
    }

    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . $file) ) {
                recurse_copy($src . $file."/",$dst . $file."/");
            }
            else {
                 #if (file_exists($dst . $file)) {
                 #   unlink($dst . $file);
                 #}

                copy($src . $file,$dst . $file);
                exec("\"".$GLOBALS['MAINDIR']."bin/busybox\" chmod 777 \"".$dst . $file."\"");
            }
        }
    }
    closedir($dir);
}

function Cleanup($src)
{
    $dir = opendir($src);

    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . $file) ) {
                Cleanup($src . $file,$dst . $file);
            }
            else {
                unlink("/share/". substr($src,strlen($GLOBALS['TEMPDIR'])) . $file);
            }
        }
    }
    closedir($dir);
}

function DownloadFile($url, $clean)
{
    if ($clean)
    {
        CleanTemp();
    }

    $handle = fopen($url, "r");

    $filename=substr($url, strrpos($url,'/')+1);
    $write = fopen($GLOBALS['TEMPDIR'].$filename, "w+");

    if ($handle && $write)
    {
        while (!feof($handle))
        {
            $buffer = fread($handle, 4096);
            fwrite($write, $buffer);
        }
        fclose($handle);
        fclose($write);
    }

    if (substr($filename,strrpos($filename,'.')+1)=="zip")
    {
        UnZip($filename);
    }

    exec("chmod -R 777 \"".$GLOBALS['TEMPDIR']."\"");
}

function UnZip($filename)
{
    exec("\"".$GLOBALS['MAINDIR']."bin/busybox\" unzip -o \"".$GLOBALS['TEMPDIR']."$filename\" -d \"".$GLOBALS['TEMPDIR']."\"");
    unlink($GLOBALS['TEMPDIR']."$filename");
}

function InstallApplication($selecteditem)
{
    #make sure the appupdate check is fired again
    unset($_SESSION["appupdates"]);

    DownloadFile($selecteditem['DownloadURL'], true);
    DownloadFile($GLOBALS['APPINIT_URL'], false);
    DownloadFile($GLOBALS['INSTALLPREPARE_URL'], false);
    recurse_copy($GLOBALS['TEMPDIR'],"/share/");


    #install appinit
    file("http://localhost.drives:8883/".$GLOBALS['DRIVE']."/appinit.cgi");


    #install prepare
    file("http://localhost.drives:8883/".$GLOBALS['DRIVE']."/installprepare.cgi");


    #install application
    $output="";
    $handle=fopen("http://localhost.drives:8883/".$GLOBALS['DRIVE']."/".$selecteditem['InstallScript'],"r");
    while (!feof($handle) && !strpos($output,"</html>") && !strpos($output,chr(4))) {
          $output.=fread($handle, 4096);
    }
    fclose($handle);

    Cleanup($GLOBALS['TEMPDIR']);
    CleanTemp();

    $instructions="";
    if (!empty($selecteditem['WebInterfaceURL']))
    {
        $instructions.="Open application via webbrowser on: ".str_replace("[NMT_IP]",$_SERVER['SERVER_ADDR'],$selecteditem['WebInterfaceURL'])."\n";
    }
    if (!empty($selecteditem['GayaInterfaceURL']))
    {
        $instructions.="Open application on NMT via webservice: ".$selecteditem['Name']."\n";
    }
    if (!empty($selecteditem['UsageInstructions']))
    {
        $instructions.="Additional usage instructions: ".$selecteditem['UsageInstructions']."\n";
    }

    return $instructions."\n".$output;
}

function InstallCustomMenu($downloadurl)
{
    DownloadFile($downloadurl, true);
    UninstallCustomMenu();

    recurse_copy($GLOBALS['TEMPDIR'],"/share/");
    CleanTemp();
    return "Done, please use the Select Source button on your remote control\nthen select HARD_DISK.";
}

function InstallTheme($downloadurl)
{
    DownloadFile($downloadurl, true);
    UninstallTheme();
    exec("mkdir /share/Photo/_theme_");

    if (is_dir($GLOBALS['TEMPDIR']."_theme_"))
    {
        recurse_copy($GLOBALS['TEMPDIR']."_theme_/","/share/Photo/_theme_/");
    }
    else
    {
        recurse_copy($GLOBALS['TEMPDIR'],"/share/Photo/_theme_/");
    }

    CleanTemp();
    return "Done, please restart the NMT.";
}

function InstallWaitImageSet($downloadurl)
{
    DownloadFile($downloadurl, true);
    UninstallWaitImageSet();
    exec("mkdir /share/Photo/_waitimages_");

    if (is_dir($GLOBALS['TEMPDIR']."_waitimages_"))
    {
        recurse_copy($GLOBALS['TEMPDIR']."_waitimages_/","/share/Photo/_waitimages_/");
    }
    else
    {
        recurse_copy($GLOBALS['TEMPDIR'],"/share/Photo/_waitimages_/");
    }

    DownloadFile($GLOBALS['INSTALLPREPARE_URL'], false);
    copy($GLOBALS['TEMPDIR']."installprepare.cgi", "/share/installprepare.cgi");
    exec("chmod 777 /share/installprepare.cgi");

    $script="installprepare.cgi";
    file("http://localhost.drives:8883/".$GLOBALS['DRIVE']."/".$script);
    unlink("/share/installprepare.cgi");

    CleanTemp();
    return "Done, please restart the NMT.";
}

function InstallWebservice($name, $url)
{
    DownloadFile($GLOBALS['INSTALLPREPARE_URL'], true);
    recurse_copy($GLOBALS['TEMPDIR'],"/share/");

    $script="installprepare.cgi?webservice_name=".rawurlencode($name)."&webservice_url=".rawurlencode($url);
    file("http://localhost.drives:8883/".$GLOBALS['DRIVE']."/".$script);

    unlink("/share/installprepare.cgi");

    CleanTemp();
    return ("Done, webservice should be visible in Webservices menu on the NMT");
}

function UninstallWaitImageSet()
{
    exec("rm -Rf /share/Photo/_waitimages_");
}

function UninstallTheme()
{
    exec("rm -Rf /share/Photo/_theme_");
}

function UninstallCustomMenu()
{
    exec("rm -Rf /share/Photo/_index_");
    unlink("/share/index.htm");
    unlink("/share/index.html");
}

function ApplicationAction($name, $action) {
    $script="Apps/AppInit/appinit.cgi?".$action."&".rawurlencode($name);

    $output="";
    $handle=fopen("http://localhost.drives:8883/".$GLOBALS['DRIVE']."/".$script,"r");
    while (!feof($handle) && !strpos($output,"</html>") && !strpos($output,chr(4))) {
          $output.=fread($handle, 4096);
    }
    fclose($handle);
}
?>
