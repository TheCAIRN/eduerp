<?php
class EntityResource extends ERPBase {
	public function __construct($link=null) {
		parent::__construct($link);
		$this->searchFields[] = array('ent_resources','entity_id','Entity','integer');
		$this->searchFields[] = array('ent_resources','resource_id','ID','integer');
		$this->searchFields[] = array('ent_resources','resource_name','Name','textbox');
		
		$this->entryFields[] = array('ent_resources','','Entity Resources','fieldset');
		$this->entryFields[] = array('ent_resources','entity_id','Entity','integerid');
		$this->entryFields[] = array('ent_resources','resource_id','ID','integerid');
		$this->entryFields[] = array('ent_resources','resource_name','Name','textbox');
		$this->entryFields[] = array('ent_resources','lwh_uom','LWH UOM','dropdown','aa_uom',array('uom_code','uom_description',1));
		$this->entryFields[] = array('ent_resources','length','Length','decimal',11,5);
		$this->entryFields[] = array('ent_resources','width','Width','decimal',11,5);
		$this->entryFields[] = array('ent_resources','height','Height','decimal',11,5);
		$this->entryFields[] = array('ent_resources','liquid_volume','Liquid Volume','decimal',11,5);
		$this->entryFields[] = array('ent_resources','lvol_uom','Liquid UOM','dropdown','aa_uom',array('uom_code','uom_description',7));
		$this->entryFields[] = array('ent_resources','person_count','Human Capacity','integer');
		$this->entryFields[] = array('ent_resources','unit_count','Production Capacity','integer');
		$this->entryFields[] = array('ent_resources','unit_uom','Item UOM','dropdown','aa_uom',array('uom_code','uom_description',2));
		$this->entryFields[] = array('ent_resources','production_uom','Production Time UOM','dropdown','aa_uom',array('uom_code','uom_description',9));
		$this->entryFields[] = array('ent_resources','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('ent_resources','rev_number','Revision number','integer');
		$this->entryFields[] = array('ent_resources','','','endfieldset');
	} // constructor
	public function listRecords() {
		parent::abstractListRecords('EntityResource');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('EntityResourceSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		$q = "SELECT entity_id,resource_id,resource_name,lwh_uom,length,width,height,liquid_volume,lvol_uom,
			person_count,unit_count,unit_uom,production_uom
			FROM ent_resources";
		// TODO: Add $criteria
		if (isset($_SESSION['currentEntity']))
			$q .= " WHERE entity_id={$_SESSION['currentEntity']}";
		// TODO: Convert to prepared statements
		$q .= " ORDER BY entity_id,resource_id;";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['entity_id'].'-'.$row['resource_id']] = 
					array('name'=>$row['resource_name'],
					'L/W/H'=>(float)$row['length'].'/'.(float)$row['width'].'/'.(float)$row['height'].' '.$row['lwh_uom'],
					'Liquid Volume'=>(float)$row['liquid_volume'].' '.$row['lvol_uom'],
					'Human capacity'=>$row['person_count'],
					'Production capacity'=>$row['unit_count'].' '.$row['unit_uom'],
					'Production time UOM'=>$row['production_uom']
				);
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 121;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['EntityResource'] = array_keys($this->recordSet);		
	} // executeSearch()
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		if ($id<1) return false;
		if (is_integer($id)) return true;
		if (ctype_digit($id)) return true;
		return false;
	} // isIDValid()
	public function display($id) {
	
		$stmt->close();
		echo $html;
		$_SESSION['currentScreen'] = 221;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['EntityResource']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['EntityResource'],false);
			$f = $_SESSION['searchResults']['EntityResource'][0];
			$l = $_SESSION['searchResults']['EntityResource'][] = array_pop($_SESSION['searchResults']['EntityResource']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['EntityResource'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['EntityResource'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}
	} // display()
	public function newRecord() {
		echo parent::abstractNewRecord('BOM');
		$_SESSION['currentScreen'] = 321;	
	} // newRecord()
	public function insertRecord() {
		
	} // insertRecord()
	public function updateRecord() {
		
	} // updateRecord()
	public function saveRecord() {
		
	} // saveRecord()
} // class EntityResource
?>