<?php
class ERPBase {
	protected $dbconn;
	protected $currentRecord;
	protected $recordSet;
	protected $searchFields;
	protected $entryFields;
	public function __construct($link=null) {
		$this->dbconn = $link;
		$this->currentRecord = -1;
		$this->recordSet = array();
		$this->searchFields = array();
		$this->entryFields = array();
	} // constructor
	public function setDbConn($link) {
		$this->dbconn = $link;
	} // function setDbConn()
	protected function abstractSearchPage($module) {
		$html = '<FIELDSET class="searchPage" id="'.$module.'">';
		foreach ($this->searchFields as $field) {
			if ($field[3]=='br') $html .= '<BR />';
			elseif ($field[3]=='hr') $html .= '<HR />';
			elseif (is_array($field[1])) {
				$q = 'SELECT '.$field[1][0].','.$field[1][1].' FROM '.$field[0].';';
				$result = $this->dbconn->query($q);
				if ($result!==false) {
					$html .= '<DIV class="labeldiv">';
					$html .= '<LABEL for="'.$field[1][0].'">'.$field[2].'</LABEL>';
					$html .= '<SELECT id="'.$field[1][0].'"><OPTION value="">&nbsp;</OPTION>';
					while ($option = $result->fetch_row()) {
						$html .= '<OPTION value="'.$option[0].'">'.$option[1].'</OPTION>';
					}
					$html .= '</SELECT>';
					$html .= '</DIV>';
				}
				$result->free();
			} else {
				$html .= '<DIV class="labeldiv">';
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
				if ($field[3]=='yn') {
					$html .= '<SELECT id="'.$field[1].'"><OPTION value="">&nbsp;</OPTION>';
					$html .= '<OPTION value="Y">Yes</OPTION><OPTION value="N">No</OPTION></SELECT>';
				}
				$html .= '</DIV>';
			}
		} // foreach search field
		$html .= "<BUTTON id=\"executeSearchButton\" onClick=\"executeSearch('$module');\">Search</BUTTON>";
		$html .= '</FIELDSET>';
		echo $html;
	}
	protected function abstractListRecords($module) {
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
				$html .= "<TR><TD><BUTTON class=\"idButton\" onClick=\"viewRecord('$module',$id);\">$id</BUTTON></TD>";
				foreach ($data as $field) $html .= '<TD>'.$field.'</TD>';
				$html .= '</TR>';
				$recordNumber++;
			}
			$html .= '</TABLE></DIV>';
			echo $html;
		}		
	}
} // class ERPBase
?>