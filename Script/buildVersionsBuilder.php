<?php
//config
$sRootPathModule='Builder/module';
$sVersionFilename='Builder/versions.ini';

$tType=array('all','normal','bootstrap','builder');
$tLinkModule=array();

$tDetail=array();

foreach($tType as $sType){
	$sPathModule=$sRootPathModule.'/mods/'.$sType;
	
	$tModulesAll=scandir($sPathModule);
	foreach($tModulesAll as $sModule){
		if(file_exists($sPathModule.'/'.$sModule.'/info.ini')){

			$tIni=parse_ini_file($sPathModule.'/'.$sModule.'/info.ini');
			
			$tDetail['mods_'.$sType.'_'.$sModule]=$tIni['version'];
		}
	}

}

$tIniFile=array();
$tIniFile[]="updateDate:'".date('Y-m-d H:i:s')."'";
foreach($tDetail as $id => $version){
	$tIniFile[]="$id='$version'";
}
file_put_contents($sVersionFilename,implode("\n",$tIniFile));
