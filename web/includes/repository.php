<?php

function GetRepository($url, $repository)
{
    if (isset($_SESSION[sha1($url)]))
    {
        $content = $_SESSION[sha1($url)];
    }
    else
    {
        $content = file_get_contents($url);
        $_SESSION[sha1($url)]=$content;
    }

    $doc = simplexml_load_string($content);
    array_push($repository, $doc);

    foreach($doc->DistributedRepositories->Repository as $o)
    {
        $repository = GetRepository($o->URL, $repository);
    }

    return $repository;
}


function GetApplications($docArray)
{
    foreach($docArray as $doc)
    {
        foreach($doc->Applications->Application as $o)
        {
            $results[(string)$o->Name." ".(string)$o->Version]=(array)$o;
        }
    }
    uksort($results, 'strcasecmp');
    return $results;
}

function GetThemes($docArray)
{
    foreach($docArray as $doc)
    {
        foreach($doc->Themes->Theme as $o)
        {
            $results[(string)$o->Name." ".(string)$o->Version]=(array)$o;
        }
    }

    uksort($results, 'strcasecmp');
    return $results;
}

function GetWaitImageSets($docArray)
{
    foreach($docArray as $doc)
    {
        foreach($doc->WaitImageSets->WaitImageSet as $o)
        {
            $results[(string)$o->Name." ".(string)$o->Version]=(array)$o;
        }
    }

    uksort($results, 'strcasecmp');
    return $results;
}

function GetCustomMenus($docArray)
{
    foreach($docArray as $doc)
    {
        foreach($doc->Indexes->Index as $o)
        {
            $results[(string)$o->Name." ".(string)$o->Version]=(array)$o;
        }
    }

    uksort($results, 'strcasecmp');
    return $results;
}

function GetWebservices($docArray)
{
    foreach($docArray as $doc)
    {
        foreach($doc->Webservices->Webservice as $o)
        {
            $results[(string)$o->Name." ".(string)$o->Version]=(array)$o;
        }
    }

    uksort($results, 'strcasecmp');
    return $results;
}

function GetApplicationUpdates($docArray)
{
    if (isset($_SESSION["appupdates"]))
    {
        return $_SESSION["appupdates"];
    }
    else
    {
        $updates=array();
        $applications=GetApplications($docArray);
        $installedapplications=GetInstalledApplications();

        foreach($applications as $app)
        {
            foreach($installedapplications as $installed)
            {
                if (($installed['Name']==$app['Name']) && (version_compare($app['Version'],$installed['Version'])>0))
                {
                    array_push($updates,$app);
                }
            }
        }

        $_SESSION["appupdates"] = $updates;
        return $updates;
    }
}

function GetInstalledApplications()
{
    exec("/share/Apps/AppInit/appinit.cgi info", $outputArray);

    $output="";
    foreach($outputArray as $item) {
        $item=preg_replace('/(\s+)\"?([^\",]+)\"?\s*[=:]\s*(.*)\s*/','$1"$2":$3',$item);
        $item=preg_replace('/(.*)[=:]\"?([^\",}]+)\"?([^\"]*)/','$1:"$2"$3',$item);
        $output.=$item;
    }

    $output=substr($output, strpos($output,'{'),(strrpos($output,'}')+1)-strpos($output,'{'));

    $found = true;
    while ($found) {
        $found=false;
        $section=substr($output, strpos($output,'{'),(strpos($output,'}')+1)-strpos($output,'{'));
        $output=substr($output,strpos($output,'}')+1);

        if (strlen($section)>0) {
            $found=true;
            $json=json_decode( $section, true );

            $item = array();
            $item['Name']=$json['name'];
            $item['Version']=$json['version'];
            $item['Enabled']=$json['enabled'];
            $item['Started']=$json['started'];

            $results[$item['Name']]=$item;
        }
    }

    uksort($results, 'strcasecmp');
    return $results;
}

?>
