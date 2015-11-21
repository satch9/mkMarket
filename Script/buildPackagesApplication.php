<?php
//config
$sRootPathModule='Application/module';
$sRootPathPlugin='Application/plugin';


//generation
$sVersionFilename='Public/Application/versions.ini';
$sRootPackages='Public/Application/module';
$sRootPackagesPlugin='Public/Application/plugin';


//modules
$tType=array('all','normal','bootstrap');
$tLinkModule=array();

$tDetail=array();

foreach($tType as $sType){
	$sPathModule=$sRootPathModule.'/'.$sType;
	
	$tModulesAll=scandir($sPathModule);
	foreach($tModulesAll as $sModule){
		if(file_exists($sPathModule.'/'.$sModule.'/info.ini')){

			$tIni=parse_ini_file($sPathModule.'/'.$sModule.'/info.ini');
			
			$tDetail[ $tIni['id'] ]=$tIni['version'];
			
			//zip
			//from, to, filename.zip
			archiveZip($sPathModule.'/'.$sModule,$sRootPackages,$tIni['id'].$tIni['version']);
		}
	}

}

//version
$tIniFile=array();
$tIniFile[]="updateDate:'".date('Y-m-d H:i:s')."'";
foreach($tDetail as $id => $version){
	$tIniFile[]="$id='$version'";
}
file_put_contents($sVersionFilename,implode("\n",$tIniFile));



$sPathPlugin=$sRootPathPlugin;

//plugins
$tPluginAll=scandir($sPathPlugin);
foreach($tPluginAll as $sPlugin){
	if(substr($sPlugin,0,1)=='.'){ continue; }
	print " $sPlugin ".$sRootPackagesPlugin.'/'.$sPlugin.'.down'." \n";
	file_put_contents($sRootPackagesPlugin.'/'.$sPlugin.'.down', file_get_contents($sPathPlugin.'/'.$sPlugin));
}






function archiveZip($sFrom,$sTo,$sFilename,$zip=null,$racine=null){
	$bNew=0;
	
	if(!$zip){
		print "creation zip : $sTo/$sFilename.zip \n";
		$zip = new ZipArchive();
		$zip->open($sTo.'/'.$sFilename.'.zip', ZipArchive::CREATE);
		$bNew=1;
	}
	
	print " $sFrom \n";
	if(is_dir($sFrom))
	{
		print "  is_dir \n";
		
		print "   scandir $sFrom \n";
		$fichiers = scandir($sFrom);
		// On enleve . et ..
		unset($fichiers[0], $fichiers[1]);

		foreach($fichiers as $f)
		{
			if(is_dir($sFrom.'/'.$f)){
				$zip->addEmptyDir($racine.$f);
				archiveZip($sFrom.'/'.$f,$sTo,$sFilename,$zip,$racine.$f.'/');
			}else{
				print "    addFile $sFrom/$f\n";
				if(!$zip->addFile($sFrom.'/'.$f, $racine.$f)){
					print "    ! erreur \n";
				}
			}
		}

		//on ferme
		if($bNew){
			print "close zip $sTo/$sFilename.zip \n";
			if(!$zip->close()){
				print "    ! close \n";
			}
		}
		
	}
}
