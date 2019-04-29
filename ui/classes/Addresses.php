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
		
		$this->resetHeader();
	} // __construct()
	public function resetHeader() {
	
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
	 * $mode = ['search' | 'lookup' | 'new' | 'save' | 'select' | 'display']
	 * $data = An array of address fields, or other data as appropriate to the mode.
	 */
	public function embed($id='address',$mode='search',$data=null) {
		if ($mode=='search') {
			return $this->embed_search($id,$data);
		} elseif ($mode=='lookup') {
			return $this->embed_lookup($id,$data);
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
			$q .= ' WHERE street LIKE ? OR line2 LIKE ? OR line3 LIKE ? OR city LIKE ? OR spc_abbrev=? OR postal_code=? OR country=? OR maidenhead LIKE ? OR osm_id=?';
			$slevel = 2;
		} elseif (strpos($data,',')===false) {
			// spaces, but no commas
			
		}
		$stmt = $this->dbconn->prepare($q);
		switch ($slevel) {
			case 2:
				$stmt->bind_param('ssssssssi',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9);
				$p1 = $p2 = $p3 = $p4 = $p8 = '%'.$data.'%';
				$p5 = $p6 = $p7 = $data;
				$p9 = ctype_digit($data)?$data:-99999;
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
	public function listRecords() {
	
	} // listRecords()
	public function searchPage() {
	
	} // searchPage()
	public function executeSearch($criteria) {
	
	} // executeSearch()
	public function isIDValid($id) {
	
	} // isIDValid()
	public function display($id) {
	
	} // display()
	public function newRecord() {
	
	} // newRecord()
	public function insertHeader() {
	
	} // insertHeader()
	public function updateHeader() {
	
	} // updateHeader()
	public function saveRecord() {
	
	} // saveRecord()
} // class Address
?>