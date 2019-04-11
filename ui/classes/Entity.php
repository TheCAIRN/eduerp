<?php
class Entity extends ERPBase {
	public function __construct($link=null) {
		parent::__construct($link);
		$this->searchFields[] = array('ent_entities','entity_id','ID','integer');
		$this->searchFields[] = array('ent_entity_types',array('entity_type','entity_type_description'),'Type','dropdown');
		$this->searchFields[] = array('ent_classes',array('entity_class_id','entity_class_description'),'Class','dropdown');
		$this->searchFields[] = array('ent_entities','entity_name','Name','textbox');
		$this->searchFields[] = array('ent_entities','status','Active Flag','dropdown',array(array('A','Active'),array('B','Bankrupt'),array('D','Defunct'),
			array('I','Temporarily Inactive'),array('S','Seasonally Inactive')));
	} // function __construct()
	public function entitySelect($id=0,$readonly=false) {
		$html = '<LABEL for="entitySelect">Entity:</LABEL><SELECT id="entitySelect">';
		$q = 'SELECT entity_id,entity_name FROM ent_entities ORDER BY entity_name;';
		$result = $this->dbconn->query($q);
		if ($result!==false) while ($row = $result->fetch_assoc()) {
			if ($row['entity_id']==$id || !$readonly) 
				$html .= '<OPTION value="'.$row['entity_id'].'"'.($id==$row['entity_id']?' selected="selected">':'>').$row['entity_name'].'</OPTION>';
		} else {
			$html .= '<OPTION>'.$this->dbconn->error.'</OPTION>';
		}
		$html .= '</SELECT>';
		return $html;
	} // function entitySelect()
	public function divisionSelect($id=0,$readonly=false,$entity=0) {
		$html = '<LABEL for="divisionSelect">Division:</LABEL><SELECT id="divisionSelect">';
		$q = 'SELECT division_id,division_name FROM ent_division_master ORDER BY division_name';
		if ($entity!=0 && (is_integer($entity) || ctype_digit($entity))) 
			$q .= ' m INNER JOIN ent_divisions d ON m.division_id=d.division_id AND d.entity_id='.$entity.';';
		else $q .= ';';
		$result = $this->dbconn->query($q);
		if ($result!==false) while ($row = $result->fetch_assoc()) {
			if ($row['division_id']==$id || !$readonly) 
				$html .= '<OPTION value="'.$row['division_id'].'"'.($id==$row['division_id']?' selected="selected">':'>').$row['division_name'].'</OPTION>';
		} else {
			$html .= '<OPTION>'.$this->dbconn->error.'</OPTION>';
		}
		$html .= '</SELECT>';
		return $html;
	} // function divisionSelect()
	public function departmentSelect($id=0,$readonly=false,$entity=0) {
		$html = '<LABEL for="departmentSelect">Department:</LABEL><SELECT id="departmentSelect">';
		$q = 'SELECT department_id,department_name FROM ent_department_master ORDER BY department_name';
		if ($entity!=0 && (is_integer($entity) || ctype_digit($entity))) 
			$q .= ' m INNER JOIN ent_departments d ON m.department_id=d.department_id AND d.entity_id='.$entity.';';
		else $q .= ';';
		$result = $this->dbconn->query($q);
		if ($result!==false) while ($row = $result->fetch_assoc()) {
			if ($row['department_id']==$id || !$readonly) 
				$html .= '<OPTION value="'.$row['department_id'].'"'.($id==$row['department_id']?' selected="selected">':'>').$row['department_name'].'</OPTION>';
		} else {
			$html .= '<OPTION>'.$this->dbconn->error.'</OPTION>';
		}
		$html .= '</SELECT>';
		return $html;
	} // function departmentSelect()
	public function statusSelect($status='',$readonly=false) {
		$html = '<LABEL for="entityStatus">Status:</LABEL><SELECT id="entityStatus">';
		if ($status=='A' || !$readonly) $html .= '<OPTION value="A"'.($status=='A'?' selected="selected">':'>').'Active</OPTION>';
		if ($status=='B' || !$readonly) $html .= '<OPTION value="B"'.($status=='B'?' selected="selected">':'>').'Bankrupt</OPTION>';
		if ($status=='D' || !$readonly) $html .= '<OPTION value="D"'.($status=='D'?' selected="selected">':'>').'Defunct</OPTION>';
		if ($status=='I' || !$readonly) $html .= '<OPTION value="I"'.($status=='I'?' selected="selected">':'>').'Temporarily Inactive</OPTION>';
		if ($status=='S' || !$readonly) $html .= '<OPTION value="S"'.($status=='A'?' selected="selected">':'>').'Seasonally Inactive</OPTION>';
		$html .= '</SELECT>';
		return $html;
	} // function statusSelect
	public function listRecords() {
		parent::abstractListRecords('Entity');
	} // function listRecords()
	public function searchPage() {
		parent::abstractSearchPage('EntitySearch');
	} // function searchPage()
	public function executeSearch($criteria) {
		$q = "SELECT entity_id,entity_name,entity_type_description,entity_class_description,entity_status,city,spc_abbrev,country ".
			"FROM ent_entities e ".
			"LEFT OUTER JOIN cx_addresses a ON a.address_id=e.primary_address ".
			"LEFT OUTER JOIN ent_entity_types t ON e.entity_type=t.entity_type ".
			"LEFT OUTER JOIN ent_classes c ON c.entity_class_id=e.entity_class_id ";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY entity_id";
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
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['Entity'] = array_keys($this->recordSet);
	} // executeSearch()
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		if ($id<1) return false;
		if (is_integer($id)) return true;
		if (ctype_digit($id)) return true;
		return false;
	}
	public function display($id) {
		if (!($this->isIDValid($id))) return;
		$readonly = true;
		$html = '';
		$q = "SELECT entity_name,entity_type_description,entity_class_description,entity_status,active_from,active_until,active_resume, ".
			"parent_entity,rev_enabled,rev_number,e.created_by,e.creation_date,e.last_update_by,e.last_update_date, ".
			"a.building_number,a.street,a.attention,a.apartment,a.postal_box,a.line2,a.line3,a.city,a.spc_abbrev,a.postal_code,a.country,a.county,a.maidenhead,a.latitude,a.longitude,a.osm_id,a.last_validated ".
			"FROM ent_entities e ".
			"LEFT OUTER JOIN cx_addresses a ON a.address_id=e.primary_address ".
			"LEFT OUTER JOIN ent_entity_types t ON e.entity_type=t.entity_type ".
			"LEFT OUTER JOIN ent_classes c ON c.entity_class_id=e.entity_class_id ".
			"WHERE entity_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$entityid);
		$entityid = $id;
		$result = $stmt->execute();
		if ($result!==false) {
			$stmt->bind_result($ename,$etype,$eclass,$estatus,$edate_activefrom,$edate_activeuntil,$edate_activeresume,
				$eparent,$erevyn,$erevnumber,$euser_creation,$edate_creation,$euser_modify,$edate_modify,
				$anumber,$astreet,$aattn,$aapt,$apobox,$aline2,$aline3,$acity,$aspc,$azip,$acountry,$acounty,$amaidenhead,$alatitude,$alongitude,$aosm,$alastvalidated);
			$stmt->fetch();
			if ($readonly) $cls = 'RecordView'; else $cls = 'RecordEdit';
			if ($readonly) $inputtextro = ' readonly="readonly"'; else $inputtextro = '';
			$html .= '<FIELDSET id="EntityRecord" class="'.$cls.'">';
			$html .= '<LABEL for="entityid">Entity ID:</LABEL><B id="entityid">'.$id.'</B>';
			$html .= '<LABEL for="entityname">Name:</LABEL><INPUT type="text" id="entityname" value="'.$ename.'"'.$inputtextro.' />';
			$html .= '<LABEL for="entitytype">Entity Type:</LABEL><SELECT id="entitytype"><OPTION>'.$etype.'</OPTION></SELECT>';
			$html .= '<LABEL for="entityclass">Entity Class:</LABEL><SELECT id="entityclass"><OPTION>'.$eclass.'</OPTION></SELECT>';
			$html .= $this->statusSelect($estatus,$readonly);
			$html .= '<LABEL for="activefrom">Active From:</LABEL><INPUT type="date" id="activefrom" value="'.$edate_activefrom.'"'.$inputtextro.' />';
			$html .= '<LABEL for="activeuntil">Active Until:</LABEL><INPUT type="date" id="activeuntil" value="'.$edate_activeuntil.'"'.$inputtextro.' />';
			$html .= '<LABEL for="activeresume">Active Resume:</LABEL><INPUT type="date" id="activeresume" value="'.$edate_activeresume.'"'.$inputtextro.' />';
			$html .= '<LABEL for="parent">Parent</LABEL><INPUT type="number" id="parent" value="'.$eparent.'"'.$inputtextro.' />';
			$html .= '<DIV id="RecordAudit">';
			$html .= '<LABEL for="revenabled">Revision Enabled:</LABEL><INPUT type="checkbox" id="revenabled" '.$inputtextro.' '.($erevyn=='Y'?'checked="checked" />':'/>');
			$html .= '<LABEL for="revnumber">Revision Number:</LABEL><INPUT type="number" id="revnumber" value="'.$erevnumber.'"'.$inputtextro.' />';
			$html .= '<LABEL for="createdby">Created By:</LABEL><INPUT type="text" id="createdby" value="'.$euser_creation.'" readonly="readonly" />';	// These 4 fields can only ever be modified by the system
			$html .= '<LABEL for="createdon">Created On:</LABEL><INPUT type="date" id="createdon" value="'.$edate_creation.'" readonly="readonly" />';
			$html .= '<LABEL for="modifiedby">Modified By:</LABEL><INPUT type="text" id="modifiedby" value="'.$euser_modify.'" readonly="readonly" />';
			$html .= '<LABEL for="modifiedon">Modified On:</LABEL><INPUT type="date" id="modifiedon" value="'.$edate_modify.'" readonly="readonly" />';			
			$html .= '</DIV>';
			$html .= '</FIELDSET>';
			$html .= '<FIELDSET id="AddressRecord" class="'.$cls.'">';
			$html .= '<LABEL for="addr_attn">Attn:</LABEL><INPUT type="text" id="addr_attn" value="'.$aattn.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_pobox">PO Box:</LABEL><INPUT type="text" id="addr_pobox" value="'.$apobox.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_number" value="'.$anumber.'"'.$inputtextro.' /><INPUT id="addr_street" value="'.$astreet.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_apt">Apartment/Suite:</LABEL><INPUT type="text" id="addr_apt" value="'.$aapt.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_line2" value="'.$aline2.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_line3" value="'.$aline3.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_city" value="'.$acity.'"'.$inputtextro.' /><INPUT id="addr_spc" value="'.$aspc.'"'.$inputtextro.' /><INPUT id="addr_zip" value="'.$azip.'"'.$inputtextro.
				' /><INPUT id="addr_country" value="'.$acountry.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_county">County:</LABEL><INPUT type="text" id="addr_county" value="'.$acounty.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_latitude">Lat/Long/Grid:</LABEL><INPUT id="addr_latitude" value="'.$alatitude.'"'.$inputtextro.' /><INPUT id="addr_longitude" value="'.$alongitude.
				'"'.$inputtextro.' /><INPUT	id="addr_maidenhead" value="'.$amaidenhead.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_osm">Open Street Map ID:</LABEL><INPUT id="addr_osm" value="'.$aosm.'"'.$inputtextro.' /><LABEL for="addr_lastval">Last validated:</LABEL>'.
				'<INPUT type="date" id="addr_lastval" value="'.$alastvalidated.'" /><BR />';
			
			$html .= '</FIELDSET>';
			$html .= "<DIV id=\"EntityResourceModuleIcon\" class=\"DashboardIcon\" onClick=\"executeSearch('EntityResourceSearch');\">Entity Resources</DIV>\r\n";
			$_SESSION['currentEntity'] = $id;
		}
		$stmt->close();
		echo $html;
		$_SESSION['currentScreen'] = 202;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['Entity']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['Entity'],false);
			$f = $_SESSION['searchResults']['Entity'][0];
			$l = $_SESSION['searchResults']['Entity'][] = array_pop($_SESSION['searchResults']['Entity']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['Entity'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['Entity'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}
	} // function display()
} // class Entity
?>