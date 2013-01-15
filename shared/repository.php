<?php

/**
 * Get all available repository information
 *
 * @access  public
 * @param   $url          string  Contains a URL to read
 * @param   $repository   array   Contains the collection of repositories
 * @return  array
 */
function get_repository($sUrl, $aRepositories) {

    if (substr($sUrl, -3) == 'xml') {
        // Check if we have the repository in "cache"
        if (!isset($_SESSION[sha1($sUrl)])) {
            $_SESSION[sha1($sUrl)] = file_get_contents($sUrl);
        }

        $sContent = $_SESSION[sha1($sUrl)];

        $oXml = simplexml_load_string($sContent);
        array_push($aRepositories, $oXml);

        if (is_object($oXml->DistributedRepositories->Repository)) {
            foreach($oXml->DistributedRepositories->Repository as $repository) {
                $aRepositories = get_repository($repository->URL, $aRepositories);
            }
        }
    }

    return $aRepositories;
}

/**
 * Get all available information of the given category
 *
 * @access  public
 * @param   $aInformation   array   Contains a collection of information for $sType
 * @param   $sType          string  Contains the type of information to process
 * @return  array
 */
function get_category($aInformation, $sType) {
    $aResults = array();

    switch ($sType) {
        case 'applications':
            $sParent    = 'Applications';
            $sContainer = 'Application';
            break;

        case 'themes':
            $sParent    = 'Themes';
            $sContainer = 'Theme';
            break;

        case 'waitimagesets':
            $sParent    = 'WaitImageSets';
            $sContainer = 'WaitImageSet';
            break;

        case 'custommenus':
            $sParent    = 'Indexes';
            $sContainer = 'Index';
            break;

        case 'webservices':
            $sParent    = 'Webservices';
            $sContainer = 'Webservice';
            break;
    }

    foreach($aInformation as $oEntry) {
        if (is_object($oEntry->$sParent->$sContainer)) {
            foreach($oEntry->$sParent->$sContainer as $oData) {
                $sDescription = $oData->Name.' '.$oData->Version;
                $aResults[$sDescription] = $oData;
            }
        }
    }

    uksort($aResults, 'strcasecmp');
    return $aResults;
}

/**
 * Get application update information
 *
 * @access  public
 * @param   array   $aInformation   Contains application information
 * @return  array
 */
function get_application_updates($aInformation) {
    if (!isset($_SESSION['appupdates'])) {
        $aUpdates               = array();
        $aApplications          = get_category($aInformation, 'applications');
        $aInstalledApplications = get_installed_applications();

        foreach($aApplications as $aApplication) {
            foreach($aInstalledApplications as $aInstalled) {
                if (($aInstalled->Name == $aApplication['Name']) && (version_compare($aApplication['Version'], $aInstalled->Version) > 0)) {
                    array_push($aUpdates, $aApplication);
                }
            }
        }

        $_SESSION['appupdates'] = $aUpdates;
    }

    return $_SESSION['appupdates'];
}

/**
 * Get a list of installed applications
 *
 * @access public
 * @return array
 */
function get_installed_applications() {
    $aResults = array();

    exec("/share/Apps/AppInit/appinit.cgi info", $aInfo);

    $sOutput = '';
    foreach($aInfo as $sInfo) {
        $sItem       = preg_replace('/(\s+)\"?([^\",]+)\"?\s*[=:]\s*(.*)\s*/', '$1"$2":$3', $sInfo);
        $sItem       = preg_replace('/(.*)[=:]\"?([^\",}]+)\"?([^\"]*)/', '$1:"$2"$3', $sItem);
        $sOutput    .= $sItem;
    }
    $sOutput = substr($sOutput, strpos($sOutput, '{'), (strrpos($sOutput, '}') + 1) - strpos($sOutput, '{'));

    $bFound = TRUE;
    while ($bFound) {
        $bFound     = FALSE;
        $sSection   = substr($sOutput, strpos($sOutput, '{'), (strpos($sOutput, '}') + 1) - strpos($sOutput, '{'));
        $sOutput    = substr($sOutput, strpos($sOutput, '}') + 1);

        if (strlen($sSection) > 0) {
            $bFound  = TRUE;
            $sJson   = json_decode($sSection, true);

            if (!empty($sJson['name'])) {
                $oItem          = new stdClass();
                $oItem->Name    = $sJson['name'];
                $oItem->Version = $sJson['version'];
                $oItem->Enabled = $sJson['enabled'];
                $oItem->Started = $sJson['started'];

                $aResults[$oItem->Name] = $oItem;
            }
        }
    }

    uksort($aResults, 'strcasecmp');
    return $aResults;
}
