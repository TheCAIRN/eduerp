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
	} // function abstractSearchPage
	protected function abstractNewRecord($module) {
		// TODO: Make sure the user has the rights to create a new record for this module.
		$html = '';
		foreach ($this->entryFields as $field) {
			if (count($field)>=4) {
				if ($field[3]=='fieldset') {
					$html .= '<FIELDSET class="RecordEdit" id="'.$field[0].'_edit">';
					$html .= '<LEGEND onClick="$(this).siblings().toggle();">'.$field[2].'</LEGEND>';
				} elseif ($field[3]=='endfieldset') {
					$html .= '</FIELDSET>';
				} elseif ($field[3]=='fieldtable') {
					
				
				} elseif ($field[3]=='dropdown' && count($field)>=6 && is_array($field[5])) {
					$q = 'SELECT '.$field[5][0].','.$field[5][1].' FROM '.$field[4].';';
					$result = $this->dbconn->query($q);
					if ($result!==false) {
						$html .= '<DIV class="labeldiv">';
						$html .= '<LABEL for="'.$field[1].'">'.$field[2].'</LABEL>';
						$html .= '<SELECT id="'.$field[1].'"><OPTION value="">&nbsp;</OPTION>';
						while ($option = $result->fetch_row()) {
							$html .= '<OPTION value="'.$option[0].'">'.$option[1].'</OPTION>';
						}
						$html .= '</SELECT>';
						$html .= '</DIV>';
					}
					$result->free();
				}
				if ($field[3]=='textbox') $html .= '<INPUT type="text" id="'.$field[1].'" />';
				if ($field[3]=='integer') $html .= '<INPUT type="number" id="'.$field[1].'" min="0" step="1" />';
				if ($field[3]=='checkbox') $html .= '<INPUT type="checkbox" id="'.$field[1].'" indeterminate="true" />';
			} // else, if there are fewer than 4 entries for the field, it is malformed.
		}
		return $html;  // This one is returning instead of echoing, because the calling function may need to add some module-specific scripting.
	} // function abstractNewRecord()
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
	} // function abstractListRecords
	protected function abstractSelect($id=0,$readonly=false,$table='',$idfield='',$idnamefield='',$idlabel='') {
		$table = str_replace(array("'",";","/","\\","-"),"",$table);
		$idfield = str_replace(array("'",";","/","\\","-"),"",$idfield);
		$idnamefield = str_replace(array("'",";","/","\\","-"),"",$idnamefield);
		$idlabel = str_replace(array("'",";","/","\\","-"),"",$idlabel);
		$html = '<LABEL for="'.$idlabel.'Select">'.ucwords($idlabel).':</LABEL><SELECT id="'.$idlabel.'Select">';
		$q = "SELECT $idfield,$idnamefield FROM $table ORDER BY $idnamefield;";
		$result = $this->dbconn->query($q);
		if ($result!==false) while ($row = $result->fetch_assoc()) {
			if ($row[$idfield]==$id || !$readonly) 
				$html .= '<OPTION value="'.$row[$idfield].'"'.($id==$row[$idfield]?' selected="selected">':'>').$row[$idnamefield].'</OPTION>';
		} else {
			$html .= '<OPTION>'.$this->dbconn->error.'</OPTION>';
		}
		$html .= '</SELECT>';
		return $html;		
	} // function abstractSelect
	protected function displayRecordAudit($inputtextro,$revyn,$revnumber,$user_creation,$date_creation,$user_modify,$date_modify) {
		$html = '';
		$html .= '<DIV id="RecordAudit">';
		$html .= '<LABEL for="revenabled">Revision Enabled:</LABEL><INPUT type="checkbox" id="revenabled" '.$inputtextro.' '.($revyn=='Y'?'checked="checked" />':'/>');
		$html .= '<LABEL for="revnumber">Revision Number:</LABEL><INPUT type="number" id="revnumber" value="'.$revnumber.'"'.$inputtextro.' />';
		$html .= '<LABEL for="createdby">Created By:</LABEL><INPUT type="text" id="createdby" value="'.$user_creation.'" readonly="readonly" />';	// These 4 fields can only ever be modified by the system
		$html .= '<LABEL for="createdon">Created On:</LABEL><INPUT type="datetime-local" id="createdon" value="'.$date_creation.'" readonly="readonly" />';
		$html .= '<LABEL for="modifiedby">Modified By:</LABEL><INPUT type="text" id="modifiedby" value="'.$user_modify.'" readonly="readonly" />';
		$html .= '<LABEL for="modifiedon">Modified On:</LABEL><INPUT type="datetime-local" id="modifiedon" value="'.$date_modify.'" readonly="readonly" />';			
		$html .= '</DIV>';
		return $html;
	}
} // class ERPBase
?>