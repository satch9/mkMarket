<?php class module_datatables extends abstract_moduleembedded{

	private $iLimit=0;
	private $sJsonLink=null;
	private $tHeader;
	
	private $iHeight=100;
	private $iWidth=600;
	private $tRowList;
	
	private $defaultSortField;
	
	private $idTable='grid';
	
	private $bEnableLoading=1;
    
	private $sDefaultSide='asc';
	
	private $bAltRows=false;
	private $bSortable=false;
		
	private $sUrlEdit=null;
	private $bEditEnable=0;
	private $bAddEnable=0;
	private $bDeleteEnable=0;
	private $bShowEnable=0;


	public function setPaginationLimit($iLimit){
		$this->iLimit=$iLimit;
	}
	public function setJsonLink($sJsonLink){
		$this->sJsonLink=$sJsonLink;
	}
	
	public function setListLimit($tList){
		$this->tRowList=$tList;
	}
        
	public function enableAltRows(){
		$this->bAltRows=true;
	}
	public function enableSortable(){
		$this->bSortable=true;
	}
	public function setDefaultSort($sSide){
		$this->sDefaultSide=$sSide;
	}

	public function addColumn($sLabel,$sIndex,$tOption=null){
		if($tOption==null){
			$tOption=array();
		}
		$tOption['label']=$sLabel;
		$tOption['name']=$sIndex;
		
		$this->tHeader[]=$tOption;
	}

	public function setWidth($iWidth){
		$this->iWidth=$iWidth;
	}
	public function setHeight($iHeight){
		$this->iHeight=$iHeight;
	}

	public function setId($idTable){
		$this->idTable=$idTable;

	}

	public function setDefaultSortField($sField){
		$this->defaultSortField=$sField;
	}


	public function build(){
		$oView=new _view('datatables::list');
		$oView->idTable=$this->idTable;

		$oView->iLimit=$this->iLimit;
		$oView->tLimit=$this->tRowList;
		$oView->sJsonLink=$this->sJsonLink;
		$oView->tHeader=$this->tHeader;

		$oView->iHeight=$this->iHeight;
		$oView->iWidth=$this->iWidth;

		return $oView;
	}

	public static function getJson(){
		return new module_datablesJson();
	}

}
class module_datablesJson{

	private $iTotal;
	private $tData;
	private $id;
	private $tColumn;
	
	private $iPaginationStart;
	private $iPaginationLimit;
	private $sPaginationSortField;
	private $sPaginationSortSide;
	
	private $sParamPage;
	private $sParamRows;
	private $sParamSidx;
	private $sParamSord;
	
	private $iTotalPage;
	
	private $tFilter=null;
	
	private $tSortAllowed=array('asc','desc');
	private $tSortFieldAllowed=array();
	
	public function __construct(){
		$this->tColumn=array();

		if(_root::getParam('filters')){
			$tFilter=json_decode(html_entity_decode(_root::getParam('filters')));
			foreach($tFilter->rules as $oFilter){
				$this->tFilter[$oFilter->field]=$oFilter->data;
			}
		} 
	}
	
	public function setSortAllowed($tSortAllowed){
		$this->tSortAllowed=$tSortAllowed;
	}
	public function setSortFieldAllowed($tSortFieldAllowed){
		$this->tSortFieldAllowed=$tSortFieldAllowed;
	}
	
	
	public function setTotal($iTotal){
		$this->iTotal=$iTotal;
		
		$this->iPaginationStart = _root::getParam('start'); 
		$this->sParamRows = _root::getParam('length'); 
		


		$tOrder = _root::getParam('order');

		
		$this->sPaginationSortField=$this->tColumn[$tOrder[0]['column']];
		$this->sPaginationSortSide=$tOrder[0]['dir'];
		
		 
		
		$this->iPaginationLimit=$this->sParamRows;
	}
	public function setData($tData){
		$this->tData=$tData;
	}
	public function addData($oRow){
		$this->tData[]=$oRow;
	}
	
	public function setId($id){
		$this->id=$id;
	}
	public function addColumn($sColumn){
		$this->tColumn[]=$sColumn;
	}
	
	public function getStart(){
		return (int)$this->iPaginationStart;
	}
	public function getLimit(){
		return (int)$this->iPaginationLimit;
	}
	public function getSortField(){
		if(!in_array($this->sPaginationSortField,$this->tSortFieldAllowed)){
			throw new Exception('Field sort not Allowed: "'.$this->sPaginationSortField.'" not in '.implode(',',$this->tSortFieldAllowed));
		}
		return $this->sPaginationSortField;
	}
	public function getSortSide(){
		if(!in_array($this->sPaginationSortSide,$this->tSortAllowed)){
			throw new Exception('Side not Allowed: not in '.implode(',',$this->tSortAllowed));
		}
		return $this->sPaginationSortSide;
	}
	public function hasFilter(){
		
		if($this->tFilter){
			return true;
		}else{
			return false;
		}
	}
	public function getListFilter(){
		return $this->tFilter;
	}

	public function show(){

		$oJson = new stdclass;
		$oJson->draw=time();
		$oJson->recordsTotal=$this->iTotal;
		$oJson->recordsFiltered=$this->iTotal;
		
		$tData=array();
		foreach($this->tData as $oData){
			$tRow=array();
			foreach($this->tColumn as $sColumn){
				
				$tRow[]=$oData->$sColumn;

				
			}
			$tData[]=$tRow;
		}

		$oJson->data=$tData;

		echo json_encode($oJson);exit;
	}

}