<?php
class ERPBase {
	protected $dbconn;
	protected $currentRecord;
	protected $primaryKey;
	protected $recordSet;
	protected $searchFields;
	protected $entryFields;
	protected $mb;
	protected $supportsAttachments;
	protected $supportsNotes;
	public function __construct($link=null) {
		$this->dbconn = $link;
		$this->currentRecord = -1;
		$this->primaryKey = 'id';
		$this->recordSet = array();
		$this->searchFields = array();
		$this->entryFields = array();
		$this->mb = new Messagebar();
		$this->supportsAttachments = false;
		$this->supportsNotes = false;
	} // constructor
	public function setDbConn($link) {
		$this->dbconn = $link;
	} // function setDbConn()
	private function dropdownQuery($field,$prefix='',$intable=false,$view='search',$hdata=null) {
		$html = '';
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
		return $html;
	}
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
				if ($field[3]=='dropdown' && count($field)==6 && is_array($field[5])) $html .= $this->dropdownQuery($field);
				elseif ($field[3]=='dropdown') {
					$html .= '<SELECT id="'.$field[1].'"><OPTION value="">&nbsp;</OPTION>';
					if (count($field)==5 && is_array($field[4])) {
						foreach ($field[4] as $option) {
							$html .= '<OPTION value="'.$option[0].'">'.$option[1].'</OPTION>';
						}
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
						$html .= '<FIELDSET class="'.$cls.' embedded" id="'.$field[0].'_'.$field[1].'_edit">';
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
					if ($view!='view') $html .= '<TR id="row'.$tablerow.'">'.$tableentry.'</TR>';
					if ((strpos($field[0],'_detail')!==false || strpos($field[0],'_transactions')!==false) && is_array($ddata)) {
						foreach($ddata as $dkey=>$drow) {
							$tablerow++;
							$html .= '<TR id="row'.$tablerow.'">';
							// TODO: Match the array fields to the ids in $tableentry
							foreach ($drow as $dlabel=>$dfield) {
								if (!empty($dfield) && (strpos($dlabel,'product_id')!==false || (strpos($dlabel,'item_')!==false && strpos($dlabel,'_id')!==false))) {
									$html .= '<TD><DIV id="row'.$tablerow.'-'.$dlabel.'-div" class="embedded">'.$item->embed('row'.$tablerow.'-'.$dlabel,'display readonly',$dfield).'</DIV></TD>';
								} else
									$html .= '<TD id="row'.$tablerow.'-'.$dlabel.'">'.$dfield.'</TD>';
							}
							if ($view!='view') $html .= "<TD id=\"row{$tablerow}-editButton\"><BUTTON onClick=\"editDetailRow('{$field[0]}','row{$tablerow}');\">Edit</BUTTON></TD>";
							$html .= '</TR>';
						}
					}
					$html .= '</TABLE></FIELDSET>';
					$intable = false;
					$tablerow++;
				} elseif ($field[3]=='dropdown' && count($field)>=6 && is_array($field[5])) {
					// TODO: I would like to move this section to just call $this->dropdownQuery; but there are too
					// many operational variables as part of the field loop to make it easy.
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
					$readonly = '';
					if ($view=='view') $readonly = ' readonly="readonly"';
					$val = '&nbsp;';
					if (is_array($hdata) && strpos($field[0],'_detail')===false && isset($hdata[$field[1]])) $val=$hdata[$field[1]];
					if (!$intable) {
						$html .= '<DIV class="labeldiv" id="'.$prefix.$field[1].'-div" style="height: 4em;">';
						$html .= '<LABEL for="'.$prefix.$field[1].'">'.$field[2].'</LABEL>';						
						$html .= "<TEXTAREA id=\"{$field[1]}\" onMouseUp=\"document.getElementById('{$field[1]}-div').height=this.height;\" $readonly>$val</TEXTAREA></DIV>";
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
				if ($field[3]=='datetime') {
					$readonly = '';
					if ($view=='view') $readonly = ' readonly="readonly"';
					$dval = '';
					$tval = '';
					if (is_array($hdata) && strpos($field[0],'_detail')===false && isset($hdata[$field[1]])) {
						$dval = ' value="'.substr($hdata[$field[1]],0,10).'"';
						$tval = ' value="'.substr($hdata[$field[1]],11).'"';
					} elseif (count($field)>=5) {
						$now = new DateTime();
						if ($field[4]=='now') {
							$dval = ' value="'.$now->format('Y-m-d').'"';
							$tval = ' value="'.$now->format('H:i:s').'"';
						}
					}
					if (!$intable) {
						$html .= '<INPUT type="date" id="'.$prefix.$field[1].'-date"'.$dval.$readonly.' /><INPUT type="time" id="'.$prefix.$field[1].'-time"'.$tval.$readonly.' />';
					} else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$prefix.$field[1].'"><INPUT type="date" id="'.$prefix.$field[1].'-date" />'.
							'<INPUT type="time" id="'.$prefix.$field[1].'-time" /></TD>';
					}
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
				if ($field[3]=='newlinebutton' && $view!='view')
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
						if (is_array($hdata) && strpos($field[0],'_detail')===false && isset($hdata[$field[1]]))
							if (count($field)>=7) $html .= $field[4]->{$field[5]}($hdata[$field[1]],$view=='view',false,$field[6]);
							else $html .= $field[4]->{$field[5]}($hdata[$field[1]]);
						else 
							if (count($field)>=7) $html .= $field[4]->{$field[5]}(null,$view=='view',false,$field[6]);
							else $html .= $field[4]->{$field[5]}();
					} else {
						$tableentry .= '<TD id="row'.$tablerow.'-'.$prefix.$field[1].'">';
						if (count($field)>=7) $tableentry .= $field[4]->{$field[5]}(null,$view=='view',false,$field[6]);
						else $tableentry .= $field[4]->{$field[5]}();
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
					if ($view=='view') {
						if (!$intable) {
							if (is_array($hdata) && strpos($field[0],'_detail')===false && !empty($hdata[$field[1]])) $html .= '<DIV id="'.$field[1].'-div" class="embedded">'.$item->embed($field[1],'display readonly',$hdata[$field[1]]).'</DIV>';
						} else {
							$tableentry .= '<TD id="row'.$tablerow.'-'.$field[1].'">';
							if (is_array($hdata) && strpos($field[0],'_detail')===false && isset($hdata[$field[1]])) $tableentry .= '<DIV id="'.$field[1].'-div" class="embedded">'.$hdata[$field[1]].'</DIV>';
							$tableentry .= '</TD>';
						}
					} else {
						if (!$intable) {
							if ($view=='edit' && is_array($hdata) && strpos($field[0],'_detail')===false && isset($hdata[$field[1]])) 
								$html .= '<DIV id="'.$field[1].'-div" class="embedded">'.$item->embed($field[1],'display',$hdata[$field[1]]).'</DIV>';
							else $html .= '<DIV id="'.$field[1].'-div" class="embedded">'.$item->embed($field[1],'search').'</DIV>';
						} else {
							$tableentry .= '<TD id="row'.$tablerow.'-'.$field[1].'">';
							$tableentry .= '<DIV id="'.$field[1].'-div" class="embedded">'.$item->embed($field[1],'search').'</DIV>';
							$tableentry .= '</TD>';
						}
					}
				}
				if (!$intable && !$embedded) {
					$html .= '</DIV>';
				}
			} // else, if there are fewer than 4 entries for the field, it is malformed.
		} // foreach entryfields
		if (is_string($this->supportsNotes) && !$embedded) {
			$q = 'SELECT note_id,'.$this->primaryKey.',n.note_type_id,seq,note_text,rev_enabled,rev_number,created_by,creation_date,'.
				'last_update_by,last_update_date,note_type_code FROM '.
				$this->supportsNotes.' n LEFT OUTER JOIN aa_note_types nt ON n.note_type_id=nt.note_type_id WHERE '.$this->primaryKey.'='.$this->currentRecord;
			$result = $this->dbconn->query($q);
			if ($result!==false) {
				if ($this->supportsNotes=='item_notes') {
					$html .= '</FIELDSET>';
				}
				$html .= '<FIELDSET class="'.$cls.'" id="notes"><LEGEND onClick="$(this).siblings().toggle();">Notes</LEGEND>';
				while ($row = $result->fetch_assoc()) {
					$html .= '<DIV class="noteClass" id="note'.$row['note_id'].'">'.$row['note_type_code'].' '.$row['seq'].' '.$row['creation_date'].'<DIV>'.$row['note_text'].'</DIV></DIV>';
				}
				if ($view=='edit' /*|| $view=='new'*/) {
					$nt = new NoteTypes($this->dbconn);
					$html .= '<DIV id="newNote"><LABEL>Add another note:</LABEL><INPUT type="hidden" id="supportsNotes" value="'.$this->supportsNotes.'" />'.
						'<INPUT type="hidden" id="notePrimaryKey" value="'.$this->primaryKey.'" /><INPUT type="hidden" id="noteCurrentRecord" value="'.$this->currentRecord.'" />'.
						$nt->NoteTypesSelect(0,false).
						'<LABEL for="seq">Sequence</LABEL>'.
						'<INPUT type="number" id="seq" value="1" />'.
						'<LABEL for="noteText">Note Text</LABEL>'.
						'<TEXTAREA id="noteText" rows="3" columns="50"></TEXTAREA><BUTTON onClick="onClick_addNote();">Add Note</BUTTON></DIV>';
				} // if view==edit, add new note section
				if ($this->supportsNotes!='item_notes') {
					$html .= '</FIELDSET>';
				}
			} else $html .= '<DIV>'.$this->dbconn->error.'</DIV>';
		} // supportsNotes
		if (is_string($this->supportsAttachments) && !$embedded) {
			$q = 'SELECT aa.attachment_id,aa.attachment_type_id,aa.file_name,aa.uri,aa.description,aa.data FROM '.
				$this->supportsAttachments.' pr JOIN aa_attachments aa ON pr.attachment_id=aa.attachment_id WHERE '.$this->primaryKey.'='.$this->currentRecord;
			$result = $this->dbconn->query($q);
			if ($result!==false) {
				if ($this->supportsAttachments=='item_attachments') {
					$html .= '</FIELDSET>';
				}
				$html .= '<FIELDSET class="'.$cls.'" id="attachments"><LEGEND onClick="$(this).siblings().toggle();">Attachments</LEGEND>';
				while ($row = $result->fetch_assoc()) {
					$html .= '<DIV><A href="getAttachment.php?id='.$row['attachment_id'].'">'.$row['file_name'].'</A><DIV>'.$row['description'].'</DIV></DIV>';
				}
				if ($view=='edit' /*|| $view=='new'*/) {
					$at = new AttachmentTypes($this->dbconn);
					$html .= '<DIV id="newAttachment"><LABEL>Add another attachment:</LABEL><INPUT type="hidden" id="supportsAttachments" value="'.$this->supportsAttachments.'" />'.
						'<INPUT type="hidden" id="attachmentPrimaryKey" value="'.$this->primaryKey.'" /><INPUT type="hidden" id="attachmentCurrentRecord" value="'.$this->currentRecord.'" />'.
						'<INPUT type="file" id="attachmentAddFile" />'.
						$at->AttachmentTypesSelect(0,false).
						'<LABEL for="attachmentDescription">Description</LABEL>'.
						'<TEXTAREA id="attachmentDescription" rows="3" columns="30"></TEXTAREA><BUTTON onClick="onClick_addFile();">Attach File</BUTTON></DIV>';
				} // if view==edit, add new attachment section
				if ($this->supportsAttachments!='item_attachments') {
					$html .= '</FIELDSET>';
				}
			} else $html .= '<DIV>'.$this->dbconn->error.'</DIV>';
		} // supportsAttachments
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
		$html = '<LABEL for="'.str_replace(' ','',$idlabel).'Select">'.ucwords($idlabel).':</LABEL><SELECT id="'.str_replace(' ','',$idlabel).'Select">';
		if (!$readonly) $html .= '<OPTION value="">&nbsp;</OPTION>';
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
