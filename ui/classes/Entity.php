<?php
class Entity {
	private $dbconn;
	private $currentRecord;
	private $recordSet;
	private $searchFields;
	public function __construct($link=null) {
		$this->dbconn = $link;
		$this->currentRecord = -1;
		$this->recordSet = array();
		$this->searchFields = array();
		$this->searchFields[] = array('ent_entities','entity_id','ID','integer');
		$this->searchFields[] = array('ent_entity_types',array('entity_type','entity_type_description'),'Type','dropdown');
		$this->searchFields[] = array('ent_classes',array('entity_class_id','entity_class_description'),'Class','dropdown');
		$this->searchFields[] = array('ent_entities','entity_name','Name','textbox');
		$this->searchFields[] = array('ent_entities','status','Active Flag','dropdown',array(array('A','Active'),array('B','Bankrupt'),array('D','Defunct'),
			array('I','Temporarily Inactive'),array('S','Seasonally Inactive')));
	} // function __construct()
	public function setDbConn($link) {
		$this->dbconn = $link;
	} // function setDbConn()
	public function listRecords() {
	
	} // function listRecords()
	public function searchPage() {
		$html = '<FIELDSET class="searchPage" id="EntitySearch">';
		foreach ($this->searchFields as $field) {
			if (is_array($field[1])) {
				$q = 'SELECT '.$field[1][0].','.$field[1][1].' FROM '.$field[0].';';
				$result = $this->dbconn->query($q);
				if ($result!==false) {
					$html .= '<LABEL for="'.$field[1][0].'">'.$field[2].'</LABEL>';
					$html .= '<SELECT id="'.$field[1][0].'"><OPTION value="">&nbsp;</OPTION>';
					while ($option = $result->fetch_row()) {
						$html .= '<OPTION value="'.$option[0].'">'.$option[1].'</OPTION>';
					}
					$html .= '</SELECT>';
				}
				$result->free();
			} else {
				$html .= '<LABEL for="'.$field[1].'">'.$field[2].'</LABEL>';
				if ($field[3]=='textbox') $html .= '<INPUT type="text" id="'.$field[1].'" />';
				if ($field[3]=='integer') $html .= '<INPUT type="number" id="'.$field[1].'" min="0" step="1" />';
				if ($field[3]=='checkbox') $html .= '<INPUT type="checkbox" id="'.$field[1].'" indeterminate="true" />';
				if ($field[3]=='dropdown') {
					$html .= '<SELECT id="'.$field[1].'"><OPTION value="">&nbsp;</OPTION>';
					foreach ($field[4] as $option) {
						$html .= '<OPTION value="'.$option[0].'">'.$option[1].'</OPTION>';
					}
					$html .= '</SELECT>';
				}
			}
		} // foreach search field
		$html .= "<BUTTON id=\"executeSearchButton\" onClick=\"executeSearch('EntitySearch');\">Search</BUTTON>";
		$html .= '</FIELDSET>';
		echo $html;
	} // function searchPage()
	public function display() {
	
	} // function display()
} // class Entity
?>