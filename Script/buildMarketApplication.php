<?php

$sRootPathModule='Application/module';
$sRootPathPlugin='Application/plugin';

//generation
$sRootPages='Public/Application/pages';


$tLang=array('fr','en');

$tNavBrut=array(
	'index' => array('Accueil','Home'),
	'normal_list_1' => array('Modules pour Applications "Normales"','Normal compatibles extensions'),
	//'bootstrap_list_1' => array('Modules pour Applications "Bootstrap"','Bootstrap compatible extensions'),
	'plugin_list_1'=> array('Plugins','Plugins'),
);	

$tNav=array();

foreach($tNavBrut as $key => $tLabel){
	$tNav['fr'][$key]=$tLabel[0];
	$tNav['en'][$key]=$tLabel[1];
}

//page accueil
$tContent=array(
	'fr'=>'Bienvenue sur le market mkframework, ici vous pourrez ajouter de nouveaux modules &agrave; votre application.',
	'en'=>'welcome to the mkframework market. There you should install new module for your application.',
);

Page::$sRootPages=$sRootPages;

foreach($tLang as $lang){
	$oPageAccueil=new Page;
	$oPageAccueil->name='index';
	$oPageAccueil->type='content';
	$oPageAccueil->content=$tContent[$lang];
	$oPageAccueil->tNav=$tNav[$lang];
	$oPageAccueil->save($lang);
}

//---modules

$tType=array('all','normal','bootstrap');
foreach($tType as $sType){
	$sPathModule=$sRootPathModule.'/'.$sType;
	
	$tData=array();

	$tModulesAll=scandir($sPathModule);
	foreach($tModulesAll as $sModule){

		if(file_exists($sPathModule.'/'.$sModule.'/info.ini') ){//and file_exists($sPathModule.'/'.$sModule.'/market.xml')){

			$tIni=parse_ini_file($sPathModule.'/'.$sModule.'/info.ini');
			//$oXml=simplexml_load_file($sPathModule.'/'.$sModule.'/market.xml');

			foreach($tLang as $lang){
				$oData=new stdclass;
				$oData->title=$tIni['title.'.$lang];
				$oData->id=$tIni['id'];;
				$oData->author=$tIni['author'];
				$oData->version=$tIni['version'];
				$tData[$lang][]=$oData;

				
				//fr
				$oPage=new Page;
				$oPage->name='detail_'.$oData->id;
				$oPage->type='detail_module';
				$oPage->author=$tIni['author'];
				$oPage->title=$tIni['title.'.$lang];
				$oPage->id=(string)$oData->id;
				$oPage->version=$tIni['version'];

				$sDescFile=$sPathModule.'/'.$sModule.'/market/desc_'.$lang.'.xml';
				if(file_exists($sDescFile)){
					$oXml=  simplexml_load_file($sDescFile);
					
					$oPage->presentation=formate( (string)$oXml->presentation);
					$oPage->actualites=formate( (string)$oXml->actualites);
					$oPage->utilisation=formate( (string)$oXml->utilisation);
				}
								
				$oPage->content=null;
				
								
				$oPage->tNav=$tNav[$lang];
				$oPage->save($lang);
			}

			

		}


	}
	foreach($tLang as $lang){
		if(isset($tData[$lang])){
			$oPage=new Page;
			$oPage->name=$sType.'_list_1';
			$oPage->type='list';
			$oPage->title='Liste '.$sType;
			$oPage->content='';
			$oPage->tNav=$tNav[$lang];
			$oPage->tData=$tData[$lang];
			$oPage->save($lang);
		}
	}


}
//---fin modules


//---plugins
$sPathPlugin=$sRootPathPlugin;

$tData=array();

$tPluginAll=scandir($sPathPlugin);
foreach($tPluginAll as $sPlugin){

	$sIniFile=$sPathPlugin.'/'.$sPlugin.'.ini';
	if(file_exists($sIniFile) ){//and file_exists($sPathModule.'/'.$sModule.'/market.xml')){

		$tIni=parse_ini_file($sIniFile);

		foreach($tLang as $lang){
			$oData=new stdclass;
			$oData->title=$tIni['title.'.$lang];
			$oData->id=$sPlugin;;
			$oData->author=$tIni['author'];
			$oData->version=$tIni['version'];
			$tData[$lang][]=$oData;

			
			//fr
			$oPage=new Page;
			$oPage->name='detail_'.$oData->id;
			$oPage->type='detail_plugin';
			$oPage->author=$tIni['author'];
			$oPage->title=$tIni['title.'.$lang];
			$oPage->id=(string)$oData->id;
			$oPage->version=$tIni['version'];

			$sDescFile=$sPathPlugin.'/'.$sPlugin.'.desc_'.$lang.'.xml';
			if(file_exists($sDescFile)){
				$oXml=  simplexml_load_file($sDescFile);
				
				$oPage->presentation=formate( (string)$oXml->presentation);
				$oPage->actualites=formate( (string)$oXml->actualites);
				$oPage->utilisation=formate( (string)$oXml->utilisation);
			}
							
			$oPage->content=null;
			
							
			$oPage->tNav=$tNav[$lang];
			$oPage->save($lang);
		}

	}

}
foreach($tLang as $lang){
	if(isset($tData[$lang])){
		$oPage=new Page;
		$oPage->name='plugin_list_1';
		$oPage->type='list';
		$oPage->title='Liste plugin';
		$oPage->content='';
		$oPage->tNav=$tNav[$lang];
		$oPage->tData=$tData[$lang];
		$oPage->save($lang);
	}
}

//---fin plugins





function createPage($oPage){
	file_put_contents($sRootPages.'fr/'.$oPage->name.'.xml', $oPage->build() );
}
function formate($sText){
	$sText2=null;
	$tLine=explode("\n",$sText);
	
	$bStartCode=false;
	$sCode=null;
	$bEmptyLine=0;
	if($tLine){
		foreach($tLine as $line){
			$line=trim($line);
			
			if(preg_match('/##titre/',$line)){
				$line='<h2>'.str_replace('##titre ','',$line).'</h2>';
			}
			else if(preg_match('/##image/',$line)){
				$line='<img src="'.str_replace('##image ','',$line).'"/>';
			}
			
			if(preg_match('/##debut_code/',$line)){
				$bStartCode=true;
				$line=null;
			}else if(preg_match('/##fin_code/',$line)){
				$line='<div class="code">'.highlight_string($sCode,1).'</div>';
				
				$bStartCode=false;
				$sCode=null;
			}else if($bStartCode){
				$sCode.=$line."\n";
				$line=null;
			}
			
			
			
			$sText2.=$line;
			
			if(!$bEmptyLine){
				$sText2.='<br/>';
			}
			if($line==''){
				$bEmptyLine=1;
			}else{
				$bEmptyLine=0;
			}
		}
	}
	
	return $sText2;
	
}
class Page{

	public $name;

	public $type=null;
	public $title=null;
		
	public $content=null;
		
		public $presentation=null;
		public $actualites=null;
		public $utilisation=null;
		
		
	public $id=null;
	public $version=null;
	public $author=null;

	public $tNav=array();

	public $tData=array();

	private $sXml=null;

	public static $sRootPages=null;

	protected $ret="\n";

	public function save($sLang){

		$this->sXml='<?xml version="1.0" ?>';
		$this->open('page');
			$this->add('type',$this->type);
			$this->add('title',$this->title);
						
			$this->add('content',$this->content);
								
						$this->add('presentation',$this->presentation);
						$this->add('actualites',$this->actualites);
						$this->add('utilisation',$this->utilisation);

			$this->add('id',$this->id);
			$this->add('version',$this->version);
			$this->add('author',$this->author);

			$this->open('nav');
			foreach($this->tNav as $href => $label){
				$this->add('link',$label,array('href'=>$href));

			}
			$this->close('nav');

			if($this->tData){
				$this->open('data');
				foreach($this->tData as $oData){
					$this->open('bloc');
						$this->add('title',$oData->title);
						$this->add('author',$oData->author);
						$this->add('id',$oData->id);
						$this->add('version',$oData->version);

					$this->close('bloc');				
				}
				$this->close('data');
			}
		$this->close('page');

		file_put_contents(self::$sRootPages.'/'.$sLang.'/'.$this->name.'.xml', $this->sXml );

	}

	private function add($tag,$value,$tOption=null){
		$this->open($tag,$tOption);
		$this->sXml.='<![CDATA['.$value.']]>';
		$this->close($tag);
	}
	private function open($tag,$tOption=null){
		$this->sXml.='<'.$tag.$this->getOption($tOption).'>';
	}
	private function close($tag){
		$this->sXml.='</'.$tag.'>'.$this->ret;
	}
	private function getOption($tOption=null){
		if($tOption){
			$sOption=null;
			foreach($tOption as $key => $val){
				$sOption.=' '.$key.'="'.$val.'"';
				return $sOption;
			}
		}
		return null;
	}

}