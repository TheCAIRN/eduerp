<?php
class Addresses extends ERPBase {
	private $id;
	private $building_number;
	private $street;
	private $attention;
	private $apartment;
	private $postal_box;
	private $line2;
	private $line3;
	private $city;
	private $spc_abbrev;
	private $postal_code;
	private $country;
	private $county;
	private $maidenhead;
	private $latitude;
	private $longitude;
	private $osm_id;
	private $last_validated;
	private $column_list;
	public function __construct($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		$this->searchFields[] = array('cx_addresses','unified_search','Type in any part of the address and click Search','textbox');
		// For embeddable classes, don't use fieldset.
		$this->entryFields[] = array('cx_addresses','id','ID','integerid');
		$this->entryFields[] = array('cx_addresses','building_number','Building #','textbox');
		$this->entryFields[] = array('cx_addresses','street','Street','textbox');
		$this->entryFields[] = array('cx_addresses','attention','Attn','textbox');
		$this->entryFields[] = array('cx_addresses','apartment','Apt','textbox');
		$this->entryFields[] = array('cx_addresses','postal_box','PO Box','textbox');
		$this->entryFields[] = array('cx_addresses','line2','Line 2','textbox');
		$this->entryFields[] = array('cx_addresses','line3','Line 3','textbox');
		$this->entryFields[] = array('cx_addresses','city','City','textbox');
		$this->entryFields[] = array('cx_addresses','spc_abbrev','State','dropdown','aa_spc',array('abbrev','name'));
		$this->entryFields[] = array('cx_addresses','postal_code','Zip','textbox');
		$this->entryFields[] = array('cx_addresses','country','Country','dropdown','aa_country',array('iso','printable_name'));
		$this->entryFields[] = array('cx_addresses','county','County','textbox');
		$this->entryFields[] = array('cx_addresses','maidenhead','Maidenhead','textbox');
		$this->entryFields[] = array('cx_addresses','latitude','Latitude','decimal',17,11);
		$this->entryFields[] = array('cx_addresses','longitude','Longitude','decimal',17,11);
		$this->entryFields[] = array('cx_addresses','osm_id','OSM ID','integer');
		$this->resetHeader();
	} // __construct()
	public function resetHeader() {
		$this->id = -1;
		$this->building_number = 0;
		$this->street = '';
		$this->attention = '';
		$this->apartment = '';
		$this->postal_box = '';
		$this->line2 = '';
		$this->line3 = '';
		$this->city = '';
		$this->spc_abbrev = '';
		$this->postal_code = '';
		$this->country = '';
		$this->county = '';
		$this->maidenhead = '';
		$this->latitude = 0.00;
		$this->longitude = 0.00;
		$this->osm_id = 0;
		$this->last_validated = null;
		$this->column_list = 'address_id,building_number,street,attention,apartment,postal_box,line2,line3,city,spc_abbrev,postal_code,country,'.
			'county,maidenhead,latitude,longitude,osm_id,last_validated';
	} // resetHeader()
	public function arrayify($id=0,$building_number='',$street='',$attention='',$apartment='',$postal_box='',$line2='',$line3='',
		$city='',$spc_abbrev='',$postal_code='',$country='',$county='',$maidenhead='',$latitude='',$longitude='',$osm_id='',$last_validated='') {
		if ($id==0 && $building_number=='' && $street=='' && $attention=='' && $apartment=='' && $postal_box=='' && $line2=='' && $line3=='' && 
			$city=='' && $spc_abbrev=='' && $postal_code=='' && $country=='' && $county=='' && $maidenhead=='' && $latitude=='' && $longitude=='' && 
			$osm_id=='' && $last_validated=='')
			return array('id'=>$this->id,'building_number'=>$this->building_number,'street'=>$this->street,'attention'=>$this->attention,
			'apartment'=>$this->apartment,'postal_box'=>$this->postal_box,
				'line2'=>$this->line2,'line3'=>$this->line3,'city'=>$this->city,'spc_abbrev'=>$this->spc_abbrev,'postal_code'=>$this->postal_code,
				'country'=>$this->country,'county'=>$this->county,
				'maidenhead'=>$this->maidenhead,'latitude'=>$this->latitude,'longitude'=>$this->longitude,'osm_id'=>$this->osm_id,
				'last_validated'=>$this->last_validated);
		else
			return array('id'=>$id,'building_number'=>$building_number,'street'=>$street,'attention'=>$attention,'apartment'=>$apartment,'postal_box'=>$postal_box,
				'line2'=>$line2,'line3'=>$line3,'city'=>$city,'spc_abbrev'=>$spc_abbrev,'postal_code'=>$postal_code,'country'=>$country,'county'=>$county,
				'maidenhead'=>$maidenhead,'latitude'=>$latitude,'longitude'=>$longitude,'osm_id'=>$osm_id,'last_validated'=>$last_validated);
	}
	/*
	 * Address fields are linked from many different tables within the ERP system.  As a result, many other modules need to have access to 
	 * look up, select, and add address records.  The embed method provides that capability without changing $_SESSION['currentScreen'] or
	 * requiring the user to open a new tab.
	 *
	 * $id = The HTML id attribute of the fieldset.
	 * $mode = ['search' | 'lookup' | 'new' | 'save' | 'display']
	 * $data = An array of address fields, or other data as appropriate to the mode.
	 */
	public function embed($id='address',$mode='search',$data=null) {
		if ($mode=='search') {
			return $this->embed_search($id,$data);
		} elseif ($mode=='lookup') {
			return $this->embed_lookup($id,$data);
		} elseif ($mode=='display') {
			return $this->embed_display($id,$data);
		} elseif ($mode=='new') {
			return $this->embed_new($id,$data);
		} elseif ($mode=='save') {
			return $this->embed_save($id,$data);
		} else {
			$this->mb->addError('JQ Embedded Address does not understand mode, "'.$mode.'".');
		}
	} // embed()
	private function embed_search($id='address',$data=null) {
		$html = "<INPUT type=\"text\" id=\"$id\" placeholder=\"Type in any part of the address and click Search\" size=\"50\" />
			<BUTTON onClick=\"embeddedAddressSearch('$id');\">Search</BUTTON>
			<BUTTON onClick=\"embeddedAddressList('$id');\">List</BUTTON>
			<BUTTON onClick=\"embeddedAddressNew('$id');\">New</BUTTON>";
		return $html;
	} // embed_search()
	private function embed_lookup($id='address',$data=null) {
		$q = "SELECT {$this->column_list} FROM cx_addresses";
		$html = $this->embed_search($id).'<BR /><SELECT id="'.$id.'-select"><OPTION value="[new]">--Create a new record--</OPTION>';
		$slevel = 0;
		if (is_null($data) || $data=='') {
			$slevel = 1;
		} elseif (strpos($data,' ')===false && strpos($data,',')===false) {
			// one word search
			$q .= ' WHERE street LIKE ? OR line2 LIKE ? OR line3 LIKE ? OR city LIKE ? OR spc_abbrev=? OR postal_code=? OR country=? OR maidenhead LIKE ? OR osm_id=? OR address_id=?';
			$slevel = 2;
		} elseif (strpos($data,',')===false) {
			// spaces, but no commas
			
		}
		$stmt = $this->dbconn->prepare($q);
		switch ($slevel) {
			case 2:
				$stmt->bind_param('ssssssssii',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10);
				$p1 = $p2 = $p3 = $p4 = $p8 = '%'.$data.'%';
				$p5 = $p6 = $p7 = $data;
				$p9 = ctype_digit($data)?$data:-99999;
				$p10 = ctype_digit($data)?$data:-99999;
				break;
		}
		$result = $stmt->execute();
		if ($result === false) {
			$this->mb->addError($this->dbconn->error);
		} else {
			$stmt->store_result();
			$stmt->bind_result($this->id,$this->building_number,$this->street,$this->attention,$this->apartment,$this->postal_box,$this->line2,$this->line3,
				$this->city,$this->spc_abbrev,$this->postal_code,$this->country,$this->county,$this->maidenhead,$this->latitude,$this->longitude,$this->osm_id,
				$this->last_validated);
			while ($stmt->fetch()) {
				$html .= '<OPTION value="'.$this->id.'">';
				if ($this->building_number>0) $html .= "{$this->building_number} {$this->street} ";
				elseif ($this->street!='') $html .= "{$this->street} ";
				if ($this->attention!='') $html .= "Attn: {$this->attention} ";
				if ($this->apartment!='') $html .= "Apt {$this->apartment} ";
				if ($this->postal_box!='') $html .= "Box {$this->postal_box} ";
				if ($this->city!='' && $this->spc_abbrev!='') $html .= "{$this->city}, {$this->spc_abbrev} {$this->postal_code} {$this->country}";
				elseif ($this->city!='') $html .= "{$this->city} {$this->postal_code} {$this->country}";
				$html .= '</OPTION>';
			}
		}
		$html .= '</SELECT>';
		$html .= "<BUTTON onClick=\"embeddedAddressSelect('$id');\">Select</BUTTON>";
		return $html;
	} // embed_lookup()
	private function embed_display($id='address',$data=null,$readonly=true) {
		if (!($this->isIDValid($data))) {
			$this->mb->addError("JQ Embedded Address: Selected ID is not valid.");
			return $this->embed_search($id);
		}
		$q = "SELECT {$this->column_list} FROM cx_addresses WHERE address_id=?";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('i',$data);
		$return = $stmt->execute();
		$stmt->store_result();
		if ($return===false || $stmt->num_rows==0) {
			$this->mb->addError("JQ Embedded Address: Selected ID is not valid.");
			return $this->embed_search($id);
		}
		$stmt->bind_result($this->id,$this->building_number,$this->street,$this->attention,$this->apartment,$this->postal_box,$this->line2,$this->line3,
			$this->city,$this->spc_abbrev,$this->postal_code,$this->country,$this->county,$this->maidenhead,$this->latitude,$this->longitude,$this->osm_id,
			$this->last_validated);
		if ($stmt->fetch()) {
			$html = '';
			if ($readonly) $html .= $this->embed_search($id).'<BR />';
			$html .= '<DIV class="labeldiv"><LABEL for="'.$id.'-address_id">ID:</LABEL><B id="'.$id.'-address_id">'.$this->id.'</B></DIV>';
			if ($readonly) {
				$html .= ''.$this->building_number.' '.$this->street.'<BR />';
				if ($this->attention!='') $html .= "Attn: {$this->attention} <BR />";
				if ($this->apartment!='') $html .= "Apt {$this->apartment} <BR />";
				if ($this->postal_box!='') $html .= "Box {$this->postal_box} <BR />";
				if ($this->line2!='') $html .= $this->line2.'<BR />';
				if ($this->line3!='') $html .= $this->line3.'<BR />';
				if ($this->city!='' && $this->spc_abbrev!='') $html .= "{$this->city}, {$this->spc_abbrev} {$this->postal_code} {$this->country}<BR />";
				elseif ($this->city!='') $html .= "{$this->city} {$this->postal_code} {$this->country}<BR />";
				if ($this->county!='') $html .= "{$this->county} County<BR />";
				$html .= "OSM ID: {$this->osm_id}  Coordinates: {$this->latitude},{$this->longitude}  <BR />Maidenhead: {$this->maidenhead}   Last Validated: {$this->last_validated}<BR />";
				return $html;	
			}
		} else {
			return $this->embed_search($id);
		}
	} // embed_display()
	public function embed_new($id='address',$data=null) {
		$html = parent::abstractNewRecord('Addresses',$id);
		$html .= "<BR /><BUTTON onClick=\"embeddedAddressSave('$id');\">Save</BUTTON><BR />";
		$html .= $this->embed_search($id);
		return $html;
	} // embed_new()
	public function embed_save($id='address',$data=null) {
		$this->insertHeader(true);
		if ($this->id==0) {
			return $this->embed_new($id,null);
		} else {
			return $this->embed_display($id,$this->id);
		}
	} // embed_save()
	public function listRecords() {
		parent::abstractListRecords('Addresses');
		$_SESSION['currentScreen'] = 1012;
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('AddressesSearch');
		$_SESSION['currentScreen'] = 12;
	} // searchPage()
	public function executeSearch($criteria) {
		// criteria is passed in as an array of key-value pairs
		$result = null;
		$q = "SELECT {$this->column_list} FROM cx_addresses ";
		if (!is_null($criteria) && is_array($criteria) && count($criteria)>0) {
			// The only key for Addresses is unified_search.
			if (is_array($criteria[0]) && count($criteria[0])>=2 && $criteria[0][0]=='unified_search') $criteria = $criteria[0][1];
			else $criteria='';
			$q .= 'WHERE street LIKE ? OR line2 LIKE ? OR line3 LIKE ? OR city LIKE ? OR spc_abbrev=? OR postal_code=? OR country=? OR maidenhead LIKE ? OR osm_id=? OR address_id=?';
			$stmt = $this->dbconn->prepare($q);
			$stmt->bind_param('ssssssssii',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10);
			$p1 = $p2 = $p3 = $p4 = $p8 = '%'.$criteria.'%';
			$p5 = $p6 = $p7 = $criteria;
			$p9 = ctype_digit($criteria)?$criteria:-99999;
			$p10 = ctype_digit($criteria)?$criteria:-99999;
			$result = $stmt->execute();
			if ($result!==false) {
				$stmt->store_result();
				$stmt->bind_result($this->id,$this->building_number,$this->street,$this->attention,$this->apartment,$this->postal_box,$this->line2,$this->line3,
					$this->city,$this->spc_abbrev,$this->postal_code,$this->country,$this->county,$this->maidenhead,$this->latitude,$this->longitude,$this->osm_id,
					$this->last_validated);
				while ($stmt->fetch()) {
					$this->recordSet[$this->id] = array('line1'=>$this->building_number.' '.$this->street,'po_box'=>$this->postal_box,
						'city'=>$this->city,'state'=>$this->spc_abbrev,'country'=>$this->country);
				}
			}
		// if criteria exists
		} else {
			$result = $this->dbconn->query($q);
			if ($result!==false) {
				while ($row=$result->fetch_assoc()) {
					$this->recordSet[$row['address_id']] = array('line1'=>$row['building_number'].' '.$row['street'],
						'po_box'=>$row['postal_box'],'city'=>$row['city'],'state'=>$row['spc_abbrev'],'country'=>$row['country']);
				}
			}
		} // if criteria does not exist
		$this->listRecords();
		$_SESSION['currentScreen'] = 1012;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['Addresses'] = array_keys($this->recordSet);		
	} // executeSearch()
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		if ($id<1) return false;
		if (is_integer($id)) return true;
		if (ctype_digit($id)) return true;
		return false;
	} // isIDValid()
	public function display($id,$mode='view') {
		if (!$this->isIDValid($id)) return;
		$readonly = true;
		$html = '';
		$q = "SELECT {$this->column_list} FROM cx_addresses WHERE address_id=?;";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('i',$p1);
		$p1 = $id;
		$result = $stmt->execute();
		if ($result!==false) {
			$stmt->bind_result($this->id,$this->building_number,$this->street,$this->attention,$this->apartment,$this->postal_box,$this->line2,$this->line3,
				$this->city,$this->spc_abbrev,$this->postal_code,$this->country,$this->county,$this->maidenhead,$this->latitude,$this->longitude,$this->osm_id,
				$this->last_validated);
			$stmt->fetch();
			$stmt->store_result();
			$this->last_validated = new DateTime($this->last_validated);
/*
			// TODO: Display map object
			// TODO: List all table records where address is applied.
*/		}
		$stmt->close();			
		if ($mode!='update') {
			$hdata = $this->arrayify();
			echo '<FIELDSET id="AddressRecord" class="Record'.ucwords($mode).'">';
			echo parent::abstractRecord($mode,'Addresses','',$hdata,null);
			echo '</FIELDSET>';
			
		}
		//echo $html;
		$_SESSION['currentScreen'] = 2012;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['Addresses']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['Addresses'],false);
			$f = $_SESSION['searchResults']['Addresses'][0];
			$l = $_SESSION['searchResults']['Addresses'][] = array_pop($_SESSION['searchResults']['Addresses']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['Addresses'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['Addresses'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}				
	} // display()
	public function newRecord() {
		echo '<FIELDSET class="RecordEdit" id="Addresses_edit">';
		echo '<LEGEND onClick="$(this).siblings().toggle();">Address</LEGEND>';
		echo parent::abstractNewRecord('Addresses');
		echo '</FIELDSET>';
		$_SESSION['currentScreen'] = 3012;
	} // newRecord()
	public function editRecord($id) {
		$this->display($id,'edit');
		$_SESSION['currentScreen'] = 4012;
	}
	private function insertHeader($embed=false) {
		$this->resetHeader();
		if (!isset($_POST['data'])) {
			$this->mb->addError("No data was submitted.  Address save failed.");
		}
		$data = $_POST['data'];
		$this->id = isset($data['id'])?$data['id']:0;
		$this->building_number = isset($data['building_number'])?$data['building_number']:'';
		$this->street = isset($data['street'])?$data['street']:'';
		$this->attention = isset($data['attention'])?$data['attention']:'';
		$this->apartment = isset($data['apartment'])?$data['apartment']:'';
		$this->postal_box = isset($data['postal_box'])?$data['postal_box']:'';
		$this->line2 = isset($data['line2'])?$data['line2']:'';
		$this->line3 = isset($data['line3'])?$data['line3']:'';
		$this->city = isset($data['city'])?$data['city']:'';
		$this->spc_abbrev = isset($data['spc_abbrev'])?$data['spc_abbrev']:'';
		$this->postal_code = isset($data['postal_code'])?$data['postal_code']:'';
		$this->country = isset($data['country'])?$data['country']:'';
		$this->county = isset($data['county'])?$data['county']:'';
		$this->maidenhead = isset($data['maidenhead'])?$data['maidenhead']:'';
		$this->latitude = isset($data['latitude'])?$data['latitude']:0.00;
		$this->longitude = isset($data['longitude'])?$data['longitude']:0.00;
		$this->osm_id = isset($data['osm_id'])?$data['osm_id']:0;
		$q = "INSERT INTO cx_addresses ({$this->column_list}) VALUES (NULL,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('sssssssssssssddi',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p14,$p15,$p16);
		$p1 = $this->building_number;
		$p2 = $this->street;
		$p3 = $this->attention;
		$p4 = $this->apartment;
		$p5 = $this->postal_box;
		$p6 = $this->line2;
		$p7 = $this->line3;
		$p8 = $this->city;
		$p9 = $this->spc_abbrev;
		$p10 = $this->postal_code;
		$p11 = $this->country;
		$p12 = $this->county;
		// TODO: Validate Maidenhead
		$p13 = $this->maidenhead;
		$p14 = (float)$this->latitude;
		$p15 = (float)$this->longitude;
		$p16 = (int)$this->osm_id;
		$result = $stmt->execute();
		if ($embed) {
			if ($result!==false) {
				$this->id = $this->dbconn->insert_id;
			} else {
				echo 'Address save failed: '.$this->dbconn->error.'<BR />';
			}
		} else {
			if ($result!==false) {
				echo 'inserted|'.$this->dbconn->insert_id;
			} else {
				echo 'fail|'.$this->dbconn->error;
				$this->mb->addError($this->dbconn->error);
			}
		}
		$stmt->close();		
	} // insertHeader()
	private function updateHeader($embed=false) {
		$this->resetHeader();
		$now = new DateTime();
		$data=$_POST['data'];
		$id = $data['id'];
		if ((!is_integer($id) && !ctype_digit($id)) || $id<1) {
			echo 'fail|Invalid address id for updating';
			return;
		}
		$this->id = $id;
		$this->display($id,'update'); // Display already has the logic for loading the record.  TODO: Refactor into separate function.
		$update = array();
		if (isset($data['building_number']) && $data['building_number']!=$this->building_number) $update['building_number'] = array('s',$data['building_number']);
		if (isset($data['street']) && $data['street']!=$this->street) $update['street'] = array('s',$data['street']);
		if (isset($data['attention']) && $data['attention']!=$this->attention) $update['attention'] = array('s',$data['attention']);
		if (isset($data['apartment']) && $data['apartment']!=$this->apartment) $update['apartment'] = array('s',$data['apartment']);
		if (isset($data['postal_box']) && $data['postal_box']!=$this->postal_box) $update['postal_box'] = array('s',$data['postal_box']);
		if (isset($data['line2']) && $data['line2']!=$this->line2) $update['line2'] = array('s',$data['line2']);
		if (isset($data['line3']) && $data['line3']!=$this->line3) $update['line3'] = array('s',$data['line3']);
		if (isset($data['city']) && $data['city']!=$this->city) $update['city'] = array('s',$data['city']);
		if (isset($data['spc_abbrev']) && $data['spc_abbrev']!=$this->spc_abbrev) $update['spc_abbrev'] = array('s',$data['spc_abbrev']);
		if (isset($data['postal_code']) && $data['postal_code']!=$this->postal_code) $update['postal_code'] = array('s',$data['postal_code']);
		if (isset($data['country']) && $data['country']!=$this->country) $update['country'] = array('s',$data['country']);
		if (isset($data['county']) && $data['county']!=$this->county) $update['county'] = array('s',$data['county']);
		if (isset($data['maidenhead']) && $data['maidenhead']!=$this->maidenhead) $update['maidenhead'] = array('s',$data['maidenhead']);
		if (isset($data['latitude']) && (float)$data['latitude']!=$this->latitude) $update['latitude'] = array('d',(float)$data['latitude']);
		if (isset($data['longitude']) && (float)$data['longitude']!=$this->longitude) $update['longitude'] = array('d',(float)$data['longitude']);
		if (isset($data['osm_id']) && (int)$data['osm_id']!=$this->osm_id) $update['osm_id'] = array('i',(int)$data['osm_id']);
		// Create UPDATE String
		
		if (count($update)==0) {
			echo 'fail|Nothing to update';
			return;
		}
		$q = 'UPDATE cx_addresses SET ';
		$ctr = 0;
		$bp_types = '';
		$bp_values = array_fill(0,count($update),null);
		foreach ($update as $field=>$data) {
			if ($ctr > 0) $q .= ',';
			$q .= "$field=?";
			$bp_types .= $data[0];
			$bp_values[$ctr] = $data[1];
			$ctr++;
		}
		$q .= ' WHERE address_id=?';
		$ctr++;
		$bp_types .= 'i';
		$bp_values[$ctr] = $this->id;
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo 'fail|'.$this->dbconn->error;
			return;
		}
		/* The internet has a lot of material about different ways to pass a variable number of arguments to bind_param.
		   I feel that using Reflection is the best tool for the job.
		   Reference: https://www.php.net/manual/en/mysqli-stmt.bind-param.php#107154
		*/
		$bp_method = new ReflectionMethod($stmt,'bind_param');
		$bp_refs = array();
		foreach ($bp_values as $key=>$value) {
			$bp_refs[$key] = &$bp_values[$key];
		}
		array_unshift($bp_values,$bp_types);
		$bp_method->invokeArgs($stmt,$bp_values);
		$stmt->execute();
		if ($stmt->affected_rows > 0) {
			echo 'updated|'.$id;
		} else {
			if ($this->dbconn->error) {
				echo 'fail|'.$this->dbconn->error;
				$this->mb->addError($this->dbconn->error);
			} else echo 'fail|No rows updated';
		}
		$stmt->close();		
	} // updateHeader()
	public function insertRecord() {
		if (isset($_POST['level']) && $_POST['level']=='header') $this->insertHeader(false);
	}
	public function updateRecord() {
		if (isset($_POST['level']) && $_POST['level']=='header') $this->updateHeader();
	}
	public function saveRecord() {
	
	} // saveRecord()
} // class Address
?>
