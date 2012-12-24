<?php

/**
 * Clear out the $aSettings['TEMP_DIR'] directory.
 *
 * @access public
 * @return void
 */
function clean_temp() {
    exec('rm -Rf "'. $aSettings['TEMP_DIR'] .'"');
    exec('mkdir -p "'. $aSettings['TEMP_DIR'] .'"');
}

/**
 * Recursively copy all contents from $sSource to $sDestination
 *
 * @access  public
 * @param   $sSource        string The source directory
 * @param   $sDestination   string The destination directory
 * @return  void
 */
function recurse_copy($sSource, $sDestination) {
    $rDirectory = opendir($sSource);

    // Create destination directory if it doesn't exist
    if (!file_exists($sDestination)) {
        mkdir($sDestination);
    }

    while (false !== ($sFilename = readdir($rDirectory))) {
        if (($sFilename != '.') && ($sFilename != '..')) {
            if (is_dir($sSource.$sFilename)) {
                recurse_copy($sSource.$sFilename .'/', $sDestination.$sFilename .'/');
            }
            else {
                copy($sSource.$sFilename , $sDestination.$sFilename);
                exec("\"". $aSettings['MAIN_DIR'] ."bin/busybox\" chmod 777 \"". $sDestination.$sFilename ."\"");
            }
        }
    }

    closedir($rDirectory);
}

/**
 * Clean up the $sSource directory
 *
 * @access  public
 * @param   $sSource    string The source directory
 * @return  void
 */
function cleanup($sSource) {
    $rDirectory = opendir($sSource);

    while (false !== ($sFilename = readdir($rDirectory))) {
        if (($sFilename != '.') && ($sFilename != '..')) {
            if (is_dir($sSource.$sFilename)) {
                cleanup($sSource.$sFilename .'/');
            }
            else {
                unlink('/share/'. substr($sSource, strlen($aSettings['TEMP_DIR'])) . $sFilename);
            }
        }
    }

    closedir($rDirectory);
}

/**
 * Download the file at $sUrl
 *
 * @access  public
 * @param   $sUrl   string  The URL to download the file at
 * @param   $bClean boolean Boolean indicating whether the temp dir should be cleaned
 * @return  void
 */
function download_file($sUrl, $bClean = FALSE) {
    if ($bClean) {
        clean_temp();
    }

    $rHandle = fopen($sUrl, 'r');

    $sFilename = substr($sUrl, strrpos($sUrl, '/') + 1);
    $rWrite = fopen($aSettings['TEMP_DIR'].$sFilename, 'w+');

    if ($rHandle && $rWrite) {
        while (!feof($rHandle)) {
            $sBuffer = fread($rHandle, 4096);
            fwrite($rWrite, $sBuffer);
        }
        fclose($rHandle);
        fclose($rWrite);
    }

    if (substr($sFilename, strrpos($sFilename, '.') + 1) == 'zip') {
        unzip($sFilename);
    }

    exec('chmod -R 777 "'. $aSettings['TEMP_DIR'] .'"');
}

/**
 * Unzip $sFilename to $aSettings['TEMP_DIR']. After unzippig, $sFilename will be removed.
 *
 * @access  public
 * @param   $sFilename  string  The file to unzip
 * @return  void
 */
function unzip($sFilename){
    exec('"'. $aSettings['MAIN_DIR'] .'bin/busybox" unzip -o "'. $aSettings['TEMP_DIR'].$sFilename .'" -d "'. $aSettings['TEMP_DIR']. '"');
    unlink($aSettings['TEMP_DIR'].$sFilename);
}

/**
 * Install the given $oApplication
 *
 * @access  public
 * @param   $oApplication   object  Contains the information of the application to install
 * @return  string
 */
function install_application($oApplication) {
    // Make sure the appupdate check is fired again
    unset($_SESSION["appupdates"]);

    download_file($oApplication->DownloadURL, true);
    download_file($aSettings['APPINIT_URL'], false);
    download_file($aSettings['INSTALLPREPARE_URL'], false);
    recurse_copy($aSettings['TEMP_DIR'], '/share/');

    // Install appinit
    file('http://localhost.drives:8883/'. $aSettings['DRIVE'] .'/appinit.cgi');

    // Install prepare
    file('http://localhost.drives:8883/'. $aSettings['DRIVE'] .'/installprepare.cgi');

    // Install application
    $sOutput = '';
    $rHandle = fopen('http://localhost.drives:8883/'. $aSettings['DRIVE'] .'/'. $oApplication->InstallScript, 'r');
    while (!feof($rHandle) && !strpos($output, '</html>') && !strpos($output, chr(4))) {
          $sOutput .= fread($rHandle, 4096);
    }
    fclose($rHandle);

    cleanup($aSettings['TEMP_DIR']);
    clean_temp();

    $sInstructions = '';
    if (!empty($oApplication->WebInterfaceURL)) {
        $sInstructions .= 'Open application via webbrowser on: '. str_replace('[NMT_IP]', $_SERVER['SERVER_ADDR'], $oApplication->WebInterfaceURL) . PHP_EOL;
    }

    if (!empty($oApplication->GayaInterfaceURL)) {
        $sInstructions .= 'Open application on NMT via webservice: '. $oApplication->Name . PHP_EOL;
    }

    if (!empty($oApplication->UsageInstructions)) {
        $sInstructions .= 'Additional usage instructions: '. $oApplication->UsageInstructions . PHP_EOL;
    }

    return $sInstructions . PHP_EOL . $sOutput;
}

/**
 * Install the custom menu from $sDownloadUrl
 *
 * @access  public
 * @param   $sDownloadUrl string The URL to download the custom menu from
 * @return  string
 */
function install_custommenu($sDownloadUrl) {
    download_file($sDownloadUrl, TRUE);
    uninstall_custommenu();

    recurse_copy($aSettings['TEMP_DIR'], '/share/');
    clean_temp();

    return 'Done, please use the Select Source button on your remote control and select HARD_DISK.';
}

/**
 * Install the theme from $sDownloadUrl
 *
 * @access  public
 * @param   $sDownloadUrl string The URL to download the theme from
 * @return  string
 */
function install_theme($sDownloadUrl) {
    download_file($sDownloadUrl, TRUE);
    uninstall_theme();
    exec('mkdir /share/Photo/_theme_');

    if (is_dir($aSettings['TEMP_DIR'].'_theme_')) {
        recurse_copy($aSettings['TEMP_DIR'].'_theme_/', '/share/Photo/_theme_/');
    }
    else {
        recurse_copy($aSettings['TEMP_DIR'], '/share/Photo/_theme_/');
    }

    clean_temp();
    return 'Done, please restart the NMT.';
}

/**
 * Install the waitimageset from $sDownloadUrl
 *
 * @access  public
 * @param   $sDownloadUrl string The URL to download the waitimageset from
 * @return  string
 */
function install_waitimageset($sDownloadUrl) {
    download_file($sDownloadUrl, TRUE);
    uninstall_waitimageset();
    exec('mkdir /share/Photo/_waitimages_');

    if (is_dir($aSettings['TEMP_DIR'].'_waitimages_')) {
        recurse_copy($aSettings['TEMP_DIR'].'_waitimages_/', '/share/Photo/_waitimages_/');
    }
    else {
        recurse_copy($aSettings['TEMP_DIR'], '/share/Photo/_waitimages_/');
    }

    download_file($aSettings['INSTALLPREPARE_URL']);
    copy($aSettings['TEMP_DIR'].'installprepare.cgi', '/share/installprepare.cgi');
    exec('chmod 777 /share/installprepare.cgi');

    file('http://localhost.drives:8883/'. $aSettings['DRIVE'] .'/installprepare.cgi');
    unlink('/share/installprepare.cgi');

    clean_temp();
    return 'Done, please restart the NMT.';
}

/**
 * Install the webservice ($sName) from $sDownloadUrl
 *
 * @access  public
 * @param   $sName          string  The name of the webservice to install
 * @param   $sDownloadUrl   string  The URL to download the waitimageset from
 * @return  string
 */
function install_webservice($sName, $sDownloadUrl) {
    download_file($aSettings['INSTALLPREPARE_URL'], TRUE);
    recurse_copy($aSettings['TEMP_DIR'], '/share/');

    $sScript = 'installprepare.cgi?webservice_name='. rawurlencode($sName) .'&webservice_url='. rawurlencode($sDownloadUrl);
    file('http://localhost.drives:8883/'. $aSettings['DRIVE'] .'/'. $sScript);

    unlink('/share/installprepare.cgi');

    clean_temp();
    return ('Done, webservice should be visible in Webservices menu on the NMT');
}

/**
 * Uninstall the current waitimageset
 *
 * @access public
 * @return void
 */
function uninstall_waitimageset() {
    exec('rm -Rf /share/Photo/_waitimages_');
}

/**
 * Uninstall the current theme
 *
 * @access public
 * @return void
 */
function uninstall_theme() {
    exec('rm -Rf /share/Photo/_theme_');
}

/**
 * Uninstall the current custommenu
 *
 * @access public
 * @return void
 */
function uninstall_custommenu() {
    exec('rm -Rf /share/Photo/_index_');
    unlink('/share/index.htm');
    unlink('/share/index.html');
}

/**
 * Perform $sAction on application $sName
 *
 * @access  public
 * @param   $sName      string  The name of the application to perform the $sAction action on
 * @param   $sAction    string  The action to perform on the $sName application
 * @return void
 */
function application_action($sName, $sAction) {
    $sScript = 'Apps/AppInit/appinit.cgi?'. $sAction .'&'. rawurlencode($sName);

    $sOutput = '';
    $rHandle = fopen('http://localhost.drives:8883/'. $aSettings['DRIVE'] .'/'. $sScript, 'r');
    while (!feof($rHandle) && !strpos($sOutput, '</html>') && !strpos($sOutput, chr(4))) {
          $sOutput .= fread($rHandle, 4096);
    }
    fclose($rHandle);
}
