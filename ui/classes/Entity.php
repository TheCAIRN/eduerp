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
		$mb = new MessageBar();
		if (count($this->recordSet)==0) {
			$mb->addWarning('No records found.');
			$this->searchPage();
		} else {
			$mb->addInfo(count($this->recordSet).' record'.(count($this->recordSet)==1?'':'s').' found.');
			$html = '<DIV id="searchResultsDiv"><TABLE id="searchResultsList" class="recordList">';
			$recordNumber = 0;
			foreach ($this->recordSet as $id=>$data) {
				if ($recordNumber==0) {
					$headers = array_keys($data);
					$html .= '<TR><TH>ID</TH>';
					foreach ($headers as $cname) $html .= '<TH>'.$cname.'</TH>';
					$html .= '</TR>';
				}
				$html .= '<TR><TD><BUTTON class="idButton" onClick="viewEntityRecord('.$id.');">'.$id.'</BUTTON></TD>';
				foreach ($data as $field) $html .= '<TD>'.$field.'</TD>';
				$html .= '</TR>';
				$recordNumber++;
			}
			$html .= '</TABLE></DIV>';
			echo $html;
		}
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
	public function executeSearch($criteria) {
		$q = "SELECT entity_id,entity_name,entity_type_description,entity_class_description,entity_status,city,spc_abbrev,country ".
			"FROM ent_entities e ".
			"LEFT OUTER JOIN cx_addresses a ON a.address_id=e.primary_address ".
			"LEFT OUTER JOIN ent_entity_types t ON e.entity_type=t.entity_type ".
			"LEFT OUTER JOIN ent_classes c ON c.entity_class_id=e.entity_class_id ";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['entity_id']] = array('name'=>$row['entity_name'],'type'=>$row['entity_type_description'],
					'class'=>$row['entity_class_description'],'status'=>$row['entity_status'],'city'=>$row['city'],
					'spc'=>$row['spc_abbrev'],'country'=>$row['country']);
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 102;
	} // executeSearch()
	public function display() {
	
	} // function display()
} // class Entity
?>