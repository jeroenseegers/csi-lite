<?php
    $GLOBALS['MAINDIR']="/share/Apps/CSI Gaya/";
    $GLOBALS['TEMPDIR']=$GLOBALS['MAINDIR']."temp/";
    $GLOBALS['INSTALLPREPARE_URL']="http://repository.nmtinstaller.com/installprepare.cgi";
    $GLOBALS['APPINIT_URL']="http://repository.nmtinstaller.com/appinit.cgi";
    $GLOBALS['VERSION']="1.0.1";
    $GLOBALS['SESSION_TIMEOUT_MINUTES']="5";

    if (is_dir("/opt/sybhttpd/localhost.drives/HARD_DISK/"))
    {
        $GLOBALS['DRIVE']="HARD_DISK";
    }
    else
    {
        $GLOBALS['DRIVE']="SATA_DISK";
    }
    
    if (is_dir("/nmt/apps/"))
    {
        $GLOBALS['REPOSITORY_URL']="http://repository.nmtinstaller.com/RepositoryInfo_C200.xml";
    }
    else
    {
        $GLOBALS['REPOSITORY_URL']="http://repository.nmtinstaller.com/RepositoryInfo.xml";
    }

?>