<?php
//config
$sRootPathModule='Builder/module';
$sVersionFilename='Builder/versions.ini';

$sRootPackages='Packages/Builder/module';


$tType=array('all','normal','bootstrap','builder');
$tLinkModule=array();

$tDetail=array();

foreach($tType as $sType){
	$sPathModule=$sRootPathModule.'/mods/'.$sType;
	
	$tModulesAll=scandir($sPathModule);
	foreach($tModulesAll as $sModule){
		if(file_exists($sPathModule.'/'.$sModule.'/info.ini')){

			$tIni=parse_ini_file($sPathModule.'/'.$sModule.'/info.ini');
			
			
			//zip
			//from, to, filename.zip
			archiveZip($sPathModule.'/'.$sModule,$sRootPackages,'mods_'.$sType.'_'.$sModule.$tIni['version']);
		}
	}

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
