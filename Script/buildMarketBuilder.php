<?php

$sRootPathModule='Builder/module';
$sRootPages='Builder/pages';

$tLang=array('fr','en');

$tNavBrut=array(
	'index' => array('Accueil','Home'),
	'normal_list_1' => array('Compatibles Applications "Normales"','Normal compatibles extensions'),
	'bootstrap_list_1' => array('Compatibles Applications "Bootstrap"','Bootstrap compatible extensions'),
	'builder_list_1'=>array('Pour le Builder','Builder extensions'),
);

$tNav=array();

foreach($tNavBrut as $key => $tLabel){
	$tNav['fr'][$key]=$tLabel[0];
	$tNav['en'][$key]=$tLabel[1];
}

//page accueil
$tContent=array(
	'fr'=>'Bienvenue sur le market mkframework, ici vous pourrez ajouter de nouvelles extensions.',
	'en'=>'welcome to the mkframework market. There you should install new builder extensions.',
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

$tType=array('all','normal','bootstrap','builder');

$tDetail=array();

foreach($tType as $sType){
	$sPathModule=$sRootPathModule.'/mods/'.$sType;
	
	$tData=array();

	$tModulesAll=scandir($sPathModule);
	foreach($tModulesAll as $sModule){

		if(file_exists($sPathModule.'/'.$sModule.'/info.ini') ){//and file_exists($sPathModule.'/'.$sModule.'/market.xml')){

			$tIni=parse_ini_file($sPathModule.'/'.$sModule.'/info.ini');
			//$oXml=simplexml_load_file($sPathModule.'/'.$sModule.'/market.xml');

			foreach($tLang as $lang){
				$oData=new stdclass;
				$oData->title=$tIni['title.'.$lang];
				$oData->id='mods_'.$sType.'_'.$sModule;
				$oData->author=$tIni['author'];
				$oData->version=$tIni['version'];
				$tData[$lang][]=$oData;

				
				//fr
				$oPage=new Page;
				$oPage->name='detail_'.$oData->id;
				$oPage->type='detail';
				$oPage->title=$tIni['title.'.$lang];
				$oPage->id=(string)$oData->id;
				$oPage->version=$tIni['version'];

				$oPage->content=null;
				$oPage->tNav=$tNav[$lang];
				$oPage->save($lang);
			}

			

		}


	}
	foreach($tLang as $lang){
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

function createPage($oPage){
	file_put_contents($sRootPages.'fr/'.$oPage->name.'.xml', $oPage->build() );
}
class Page{

	public $name;

	public $type=null;
	public $title=null;
	public $content=null;
	public $id=null;
	public $version=null;

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

			$this->add('id',$this->id);
			$this->add('version',$this->version);

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