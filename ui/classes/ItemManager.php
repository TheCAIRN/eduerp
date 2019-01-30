<?php
class ItemManager extends ERPBase {
	public function __construct($link=null) {
		parent::__construct($link);
		$this->searchFields[] = array('item_master','product_id','ID','integer');
		$this->searchFields[] = array('item_master','product_code','Code','textbox');
		$this->searchFields[] = array('item_master','product_description','Description','textbox');
		$this->searchFields[] = array('item_master','gtin','GTIN','textbox');
		$this->searchFields[] = array('ent_entities',array('entity_id','entity_name'),'Entity','dropdown');
		$this->searchFields[] = array('ent_division_master',array('division_id','division_name'),'Division','dropdown');
		$this->searchFields[] = array('ent_department_master',array('department_id','department_name'),'Department','dropdown');
		$this->searchFields[] = array('item_types',array('item_type_code','item_type_description'),'Type','dropdown');
		$this->searchFields[] = array('item_categories',array('item_category_id','item_category_description'),'Category','dropdown');
		$this->searchFields[] = array('item_master','product_catalog_title','Catalog','textbox');
		$this->searchFields[] = array('item_master','visible','Visible','yn');
	} // constructor
	public function searchPage() {
		parent::abstractSearchPage('ItemSearch');
	} // function searchPage()
	public function listRecords() {
		parent::abstractListRecords('ItemManager');
	} // function listRecords()
	public function executeSearch($criteria) {
		$q = "SELECT product_id,product_code,product_description,gtin,item_type_description,item_category_description,product_catalog_title,i.visible 
			FROM item_master i
			LEFT OUTER JOIN item_types t ON i.item_type_code=t.item_type_code
			LEFT OUTER JOIN item_categories c ON i.item_category_id=c.item_category_id";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['product_id']] = array('code'=>$row['product_code'],'description'=>$row['product_description'],'gtin'=>$row['gtin'],
					'type'=>$row['item_type_description'],'category'=>$row['item_category_description'],'catalog'=>$row['product_catalog_title'],
					'visible'=>$row['visible']==0?'N':'Y');
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 110;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['Item'] = array_keys($this->recordSet);
			
	}
	public function display($id) {
		
	}
} // class ItemManager
?>