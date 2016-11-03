<?php
class module_googleMap extends abstract_moduleembedded{

	protected $tPosition;
	protected $tPositionWithContent;
	protected $iWidth;
	protected $iHeight;
	protected $iZoom;
	protected $sKey;

	public function __construct(){
		$this->tPosition=array();
		$this->iWidth=500;
		$this->iHeight=500;
		$this->iZoom=1;
	}

	public function setKey($sKey_){
		$this->sKey=$sKey_;
	}

	public function getMap(){

		$oView=new _view('googleMap::map');
		$oView->tPosition=$this->tPosition;
		$oView->tPositionWithContent=$this->tPositionWithContent;

		$oView->iWidth=$this->iWidth;
		$oView->iHeight=$this->iHeight;
		$oView->iZoom=$this->iZoom;
		$oView->sKey=$this->sKey;

		return $oView;

	}

	public function addPosition($sAdresse,$sTitle=null,$sLink=null){
		$this->tPosition[]=array($sAdresse,$sTitle,$sLink);
	}
	public function addPositionWithContent($sAdresse,$sTitle=null,$tContent=null){
		$this->tPositionWithContent[]=array($sAdresse,$sTitle,$tContent);
	}

	public function setWidth($iWidth){
		$this->iWidth=$iWidth;
	}
	public function setHeight($iHeight){
		$this->iHeight=$iHeight;
	}
	public function setZoom($iZoom){
		$this->iZoom=$iZoom;
	}
}
