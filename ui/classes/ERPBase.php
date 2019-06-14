<?php
class ERPBase {
	protected $dbconn;
	protected $currentRecord;
	protected $recordSet;
	protected $searchFields;
	protected $entryFields;
	protected $mb;
	protected $supportsAttachments;
	protected $supportsNotes;
	public function __construct($link=null) {
		$this->dbconn = $link;
		$this->currentRecord = -1;
		$this->recordSet = array();
		$this->searchFields = array();
		$this->entryFields = array();
		$this->mb = new MessageBar();
		$this->supportsAttachments = false;
		$this->supportsNotes = false;
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
				if ($field[3]=='datetime') $html .= '<INPUT type="date" id="'.$field[1].'-date" /><INPUT type="time" id="'.$field[1].'-time" />';
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
	protected function abstractRecord($view,$module,$prefix='',$hdata=null,$ddata=null) {
		// $hdata = header data array; $ddata = 2d array of detail data.
		// TODO: If $view==new, make sure the user has the rights to create a new record for this module.
		$html = '';
		$intable = false;
		$embedded = false;
		$tablerow = 0;
		$tablecolumn = 0;
		$tableheader = "";
		$tableentry = "";
		$fieldsetlevel = 0;
		if ($view=='view') $cls = 'RecordView'; else $cls = 'RecordEdit';
		foreach ($this->entryFields as $field) {
			if (count($field)>=4) {
				if ($field[3]=='fieldset') {
					$html .= '<FIELDSET class="'.$cls.'" id="'.$field[0].$field[1].'_edit">';
					$html .= '<LEGEND onClick="$(this).siblings().toggle();">'.$field[2].'</LEGEND>';
					$fieldsetlevel++;
				} elseif ($field[3]=='endfieldset') {
					$html .= '</FIELDSET>';
					$fieldsetlevel--;
				} elseif ($field[3]=='embedded') {
					if (!$intable) {
						$html .= '<FIELDSET class="RecordEdit embedded" id="'.$field[0].'_'.$field[1].'_edit">';
						$html .= '<LEGEND onClick="$(this).siblings().toggle();">'.$field[2].'</LEGEND>';
					}
					$embedded = true;
				} elseif ($field[3]=='endembedded') {
					if (!$intable) $html .= '</FIELDSET>';
					$embedded = false;
				} elseif ($field[3]=='fieldtable') {
					$html .= '<FIELDSET class="'.$cls.'" id="'.$field[0].'_edit">';
					$html .= '<LEGEND onClick="$(this).siblings().toggle();">'.$field[2].'</LEGEND>';
					$html .= '<TABLE id="'.$field[0].'_table">';
					$intable = true;
					$tablerow = 0;
					$tablecolumn = 0;
					$tableheader = "";
					$tableentry = "";
				} elseif ($field[3]=='endfieldtable') {
					$html .= '<TR>'.$tableheader.'</TR>';
					$html .= '<TR id="row'.$tablerow.'">'.$tableentry.'</TR>';
					$html .= '</TABLE></FIELDSET>';
					$intable = false;
					$tablerow++;
				} elseif ($field[3]=='dropdown' && count($field)>=6 && is_array($field[5])) {
					$q = 'SELECT '.$field[5][0].','.$field[5][1].' FROM '.$field[4];
					if (count($field[5])>2 && $field[4]=='aa_uom' && is_integer($field[5][2])) $q .= ' WHERE uom_type='.$field[5][2].';';
					else $q .= ';';
					$result = $this->dbconn->query($q);
					if ($result!==false) {
						if (!$intable) {
							$html .= '<DIV class="labeldiv">';
							$html .= '<LABEL for="'.$prefix.$field[1].'">'.$field[2].'</LABEL>';
							$html .= '<SELECT id="'.$prefix.$field[1].'"><OPTION value="">&nbsp;</OPTION>';
							while ($option = $result->fetch_row()) {
								$selected='';
								if (is_array($hdata) && strpos($field[0],'_detail')===false && isset($hdata[$field[1]]) && $hdata[$field[1]]==$option[0]) $selected=' selected="selected"';
								if ($view=='new' && $selected=='' && count($field)>=7 && $field[6]==$option[0]) $selected=' selected="selected"';
								if ($view!='view' || $selected!='') $html .= '<OPTION value="'.$option[0].'"'.$selected.'>'.$option[1].'</OPTION>';
							}
							$html .= '</SELECT>';
							$html .= '</DIV>';
						} else {
							$tableheader .= '<TH>'.$field[2].'</TH>';
							$tableentry .= '<TD id="row'.$tablerow.'-'.$field[1].'"><SELECT id="'.$prefix.$field[1].'"><OPTION value="">&nbsp;</OPTION>';
							while ($option = $result->fetch_row()) {
								$tableentry .= '<OPTION value="'.$option[0].'">'.$option[1].'</OPTION>';
							}
							$tableentry .= '</SELECT></TD>';
						}
						$result->free();
					} else $html .= $this->dbconn->error;
				} elseif ($field[3]=='dropdown' && count($field)>=5 && is_array($field[4])) {
					if (!$intable) {
						$html .= '<DIV class="labeldiv">';
						$html .= '<LABEL for="'.$prefix.$field[1].'">'.$field[2].'</LABEL>';						
						$html .= '<SELECT id="'.$prefix.$field[1].'"><OPTION value="">&nbsp;</OPTION>';
						foreach ($field[4] as $option) {
							$selected='';
							if (is_array($hdata) && strpos($field[0],'_detail')===false && isset($hdata[$field[1]]) && $hdata[$field[1]]==$option[0]) $selected=' selected="selected"';
							$html .= '<OPTION value="'.$option[0].'"'.$selected.'>'.$option[1].'</OPTION>';
						}
						$html .= '</SELECT></DIV>';
					} else {
						$tableheader .= '<TH>'.$field[2].'</TH>';
						$tableentry .= '<TD id="row'.$tablerow.'-'.$field[1].'"><SELECT id="'.$prefix.$field[1].'"><OPTION value="">&nbsp;</OPTION>';
						foreach ($field[4] as $option) {
							$tableentry .= '<OPTION value="'.$option[0].'">'.$option[1].'</OPTION>';
						}
						$tableentry .= '</SELECT></TD>';
					}
				} elseif ($field[3]=='textarea') {
					if (!$intable) {
						$html .= '<DIV class="labeldiv" id="'.$prefix.$field[1].'-div" style="height: 4em;">';
						$html .= '<LABEL for="'.$prefix.$field[1].'">'.$field[2].'</LABEL>';						
						$html .= "<TEXTAREA id=\"{$field[1]}\" onMouseUp=\"document.getElementById('{$field[1]}-div').height=this.height;\">&nbsp;</TEXTAREA></DIV>";
					} else {
						$tableheader .= '<TH>'.$field[2].'</TH>';
						$tableentry .= '<TD id="row'.$tablerow.'-'.$field[1].'"><TEXTAREA id="'.$prefix.$field[1].'">&nbsp;</TEXTAREA></TD>';
					}
				} elseif (!$intable && !$embedded) {
					$html .= '<DIV class="labeldiv">';
					if ($field[3]!='button') {
						$html .= '<LABEL for="'.$prefix.$field[1].'">'.$field[2].'</LABEL>';
					}
				} elseif ($intable) {
					$tableheader .= '<TH>'.$field[2].'</TH>';
				}
				if ($field[3]=='textbox') {
					$readonly = '';
					if ($view=='view') $readonly = ' readonly="readonly"';
					$val = '';
					if (is_array($hdata) && strpos($field[0],'_detail')===false && isset($hdata[$field[1]])) $val=' value="'.$hdata[$field[1]].'"';
					if (!$intable) $html .= '<INPUT type="text" id="'.$prefix.$field[1].'"'.$readonly.$val.' />';
					else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$prefix.$field[1].'"><INPUT type="text" id="'.$prefix.$field[1].'" /></TD>';
					}
				}
				if ($field[3]=='integerid') {
					$val = '';
					if (is_array($hdata) && strpos($field[0],'_detail')===false && isset($hdata[$field[1]])) $val=' value="'.$hdata[$field[1]].'"';
					if (!$intable && ($view!='view' || $val=='')) $html .= '<INPUT type="number" id="'.$prefix.$field[1].'" min="0" step="1" readonly="readonly"'.$val.' />';
					elseif (!$intable && $val!='') $html .= '<B id="'.$prefix.$field[1].'">'.$hdata[$field[1]].'</B>';
					else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$prefix.$field[1].'"><INPUT type="number" id="'.$prefix.$field[1].'" min="0" step="1" readonly="readonly"/></TD>';
					}
				}
				if ($field[3]=='integer') {
					$readonly = '';
					if ($view=='view') $readonly = ' readonly="readonly"';
					$val = '';
					if (is_array($hdata) && strpos($field[0],'_detail')===false && isset($hdata[$field[1]])) $val=' value="'.$hdata[$field[1]].'"';
					if (!$intable) $html .= '<INPUT type="number" id="'.$prefix.$field[1].'" min="0" step="1"'.$readonly.$val.' />';
					else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$prefix.$field[1].'"><INPUT type="number" id="'.$field[1].'" min="0" step="1" /></TD>';
					}
				}
				if ($field[3]=='decimal') {
					$readonly = '';
					if ($view=='view') $readonly = ' readonly="readonly"';
					$val = '';
					if (is_array($hdata) && strpos($field[0],'_detail')===false && isset($hdata[$field[1]])) $val=' value="'.$hdata[$field[1]].'"';
					$places = 5;
					$decimals = 2;
					if (count($field)>5) {
						$places = $field[4];
						$decimals = $field[5];
					}
					$step = 1 / (pow(10,$decimals));
					$max = pow(10,$places-($decimals+1))-$step;
					if (!$intable) $html .= '<INPUT type="number" id="'.$prefix.$field[1].'" min="0" max="'.$max.'" step="'.$step.'"'.$readonly.$val.' />';
					else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$prefix.$field[1].'"><INPUT type="number" id="'.$prefix.$field[1].'" min="0" max="'.$max.'" step="'.$step.'" /></TD>';
					}
				}
				if ($field[3]=='checkbox') {
					$readonly = '';
					if ($view=='view') $readonly = ' disabled="disabled"';
					$checked = '';
					if (is_array($hdata) && strpos($field[0],'_detail')===false && isset($hdata[$field[1]]) && $hdata[$field[1]]=='Y') $checked=' checked="checked"';
					if ($view=='new' && count($field)>=6) {
						if ($field[5]) $checked=' checked="checked"';
						else $checked='';
					}
					if (!$intable) $html .= '<INPUT type="checkbox" id="'.$prefix.$field[1].'" indeterminate="true"'.$readonly.$checked.' />';
					else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$prefix.$field[1].'"><INPUT type="checkbox" id="'.$prefix.$field[1].'" indeterminate="true" /></TD>';
					}
				}
				if ($field[3]=='datetime') 
					if (!$intable) {
						$html .= '<INPUT type="date" id="'.$prefix.$field[1].'-date" /><INPUT type="time" id="'.$prefix.$field[1].'-time" />';
					} else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$prefix.$field[1].'"><INPUT type="date" id="'.$prefix.$field[1].'-date" />'.
							'<INPUT type="time" id="'.$prefix.$field[1].'-time" /></TD>';
					}
				if ($field[3]=='date') {
					$readonly = '';
					if ($view=='view') $readonly = ' readonly="readonly"';
					$val = '';
					if (is_array($hdata) && strpos($field[0],'_detail')===false && isset($hdata[$field[1]])) $val=' value="'.substr($hdata[$field[1]],0,10).'"';
					if (!$intable) {
						$html .= '<INPUT type="date" id="'.$prefix.$field[1].'-date"'.$val.$readonly.' />';
					} else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$prefix.$field[1].'"><INPUT type="date" id="'.$prefix.$field[1].'-date" />';
					}
				}
				if ($field[3]=='time') 
					if (!$intable) {
						$html .= '<INPUT type="time" id="'.$prefix.$field[1].'-time" />';
					} else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$prefix.$field[1].'"><INPUT type="time" id="'.$prefix.$field[1].'-time" /></TD>';
					}
				if ($field[3]=='newlinebutton')
					if (!$intable) {
						$html .= '<BUTTON id="newlinebutton"'.((count($field)>=5)?' onClick="'.$field[4].'"':'').'>'.$field[2].'</BUTTON>';
					} else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$prefix.$field[1].'">';
						$tableentry .= '<BUTTON id="newlinebutton"'.((count($field)>=5)?' onClick="'.$field[4].'"':'').'>'.$field[2].'</BUTTON>';
						$tableentry .= '</TD>';
					}
				if ($field[3]=='button')
					if (!$intable) {
						$html .= '<BUTTON id="'.$prefix.$field[1].'button"'.((count($field)>=5)?' onClick="'.$field[4].'"':'').'>'.$field[2].'</BUTTON>';
					} else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$prefix.$field[1].'">';
						$tableentry .= '<BUTTON id="'.$prefix.$field[1].'button"'.((count($field)>=5)?' onClick="'.$field[4].'"':'').'>'.$field[2].'</BUTTON>';
						$tableentry .= '</TD>';
					}					
				if ($field[3]=='function' && count($field)>=6 && is_object($field[4]) && method_exists($field[4],$field[5])) {
					if (!$intable) {
						$html .= $field[4]->{$field[5]}();
					} else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$prefix.$field[1].'">';
						$tableentry .= $field[4]->{$field[5]}();
						$tableentry .= '</TD>';
					}
				}
				if ($field[3]=='Address') {
					$addr = new Addresses($this->dbconn);
					if (!$intable) {
						$html .= '<DIV id="'.$field[1].'-div" class="embedded">'.$addr->embed($field[1],'search').'</DIV>';
					} else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$field[1].'">';
						$tableentry .= '<DIV id="'.$field[1].'-div" class="embedded">'.$addr->embed($field[1],'search').'</DIV>';
						$tableentry .= '</TD>';
					}
				}
				if ($field[3]=='Item') {
					$item = new ItemManager($this->dbconn);
					if (!$intable) {
						$html .= '<DIV id="'.$field[1].'-div" class="embedded">'.$item->embed($field[1],'search').'</DIV>';
					} else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$field[1].'">';
						$tableentry .= '<DIV id="'.$field[1].'-div" class="embedded">'.$item->embed($field[1],'search').'</DIV>';
						$tableentry .= '</TD>';
					}
				}
				if (!$intable && !$embedded) {
					$html .= '</DIV>';
				}
			} // else, if there are fewer than 4 entries for the field, it is malformed.
		}
		return $html;  // This one is returning instead of echoing, because the calling function may need to add some module-specific scripting.
	} // function abstractRecord()
	protected function abstractNewRecord($module,$prefix='') {
		return $this->abstractRecord('new',$module,$prefix);
	} // function abstractNewRecord()
	protected function abstractListRecords($module) {
		$printResultCount = true;
		if (count($this->recordSet)==0 && isset($_SESSION['recordSet']) && isset($_SESSION['recordSet'][$module])) {
			$this->recordSet = $_SESSION['recordSet'][$module];
			$printResultCount = false;
		}
		if (count($this->recordSet)==0) {
			$this->mb->addWarning('No records found.');
			$this->searchPage();
		} else {
			if ($printResultCount)
				$this->mb->addInfo(count($this->recordSet).' record'.(count($this->recordSet)==1?'':'s').' found.');
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
			if (!isset($_SESSION['recordSet'])) $_SESSION['recordSet'] = array();
			$_SESSION['recordSet'][$module] = $this->recordSet;
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