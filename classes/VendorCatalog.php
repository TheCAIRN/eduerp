<?php
class VendorCatalog extends ERPBase {
	public function __construct($link) {
		parent::__construct($link);
		$this->searchFields[] = array('pur_vendor_catalog','vendor_catalog_id','Vendor Catalog ID','integer');
		$this->searchFields[] = array('pur_vendors',array('vendor_id','vendor_name'),'Vendor','dropdown');
		$this->searchFields[] = array('item_master',array('product_id','product_code'),'Product ID','dropdown');
		$this->searchFields[] = array('pur_vendor_catalog','vendor_item_number','Vendor Item #','textbox');
		$this->searchFields[] = array('pur_vendor_catalog','vendor_gtin','Vendor GTIN','textbox');
		$this->searchFields[] = array('pur_vendor_catalog','description','Description','textbox');
	}
	public function listRecords() {
		parent::abstractListRecords('VendorCatalog');
	} // function listRecords()
	public function searchPage() {
		parent::abstractSearchPage('VendorCatalogSearch');
	} // function searchPage()
	public function executeSearch($criteria) {
		$q = "SELECT vendor_catalog_id,vc.vendor_id,vendor_name,vc.product_id,product_code,vendor_item_number,vendor_gtin,description 
			FROM pur_vendor_catalog vc
			JOIN pur_vendors v ON vc.vendor_id=v.vendor_id
			JOIN item_master i ON i.product_id=vc.product_id";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY vc.product_id;";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['vendor_catalog_id']] = array('vendor'=>$row['vendor_name'],'product'=>$row['product_code'],'vin'=>$row['vendor_item_number'],
				'gtin'=>$row['vendor_gtin'],'description'=>$row['description']);
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 1020;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['VendorCatalog'] = array_keys($this->recordSet);		
	} // function executeSearch
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
		$q = "SELECT vc.vendor_id,vendor_name,vc.product_id,product_code,vendor_item_number,vendor_gtin,description,
			vc.rev_enabled,vc.rev_number,vc.created_by,vc.creation_date,vc.last_update_by,vc.last_update_date 
			FROM pur_vendor_catalog vc
			JOIN pur_vendors v ON vc.vendor_id=v.vendor_id
			JOIN item_master i ON i.product_id=vc.product_id
			WHERE vendor_catalog_id=?;";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$vendorid);
		$vendorid = $id;
		$result = $stmt->execute();
		if ($result!==false) {
			$stmt->store_result();
			$stmt->bind_result($vendorid,$vendorname,$productid,$productcode,$vin,$gtin,$description,
				$vrevyn,$vrevnumber,$vuser_creation,$vdate_creation,$vuser_modify,$vdate_modify);
			$stmt->fetch();
			if ($readonly) $cls = 'RecordView'; else $cls = 'RecordEdit';
			if ($readonly) $inputtextro = ' readonly="readonly"'; else $inputtextro = '';
			$html .= '<FIELDSET id="VendorCatalogRecord" class="'.$cls.'">';
			$html .= '<LABEL for="vendorcatalogid">Vendor Catalog ID:</LABEL><B id="vendorcatalogid">'.$id.'</B>';
			$v = new Vendor($this->dbconn);
			$html .= $v->vendorSelect($vendorid,$readonly);
			$p = new ItemManager($this->dbconn);
			$html .= $p->itemSelect($productid,$readonly);
			$html .= '<LABEL for="vendoritemnumber">Vendor Item #:</LABEL><INPUT type="text" id="vendoritemnumber" value="'.$vin.'"'.$inputtextro.' />';
			$html .= '<LABEL for="vendorgtin">Vendor GTIN:</LABEL><INPUT type="text" id="vendorgtin" value="'.$gtin.'"'.$inputtextro.' />';
			$html .= '<BR /><LABEL for="vcdescription">Description:</LABEL><TEXTAREA id="vcdescription" '.$inputtextro.'>'.$description.'</TEXTAREA>';
			$html .= parent::displayRecordAudit($inputtextro,$vrevyn,$vrevnumber,$vuser_creation,$vdate_creation,$vuser_modify,$vdate_modify);
			$html .= '</FIELDSET>';
		} // if $result
		$stmt->close();			
		echo $html;
		$_SESSION['currentScreen'] = 2020;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['VendorCatalog']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['VendorCatalog'],false);
			$f = $_SESSION['searchResults']['VendorCatalog'][0];
			$l = $_SESSION['searchResults']['VendorCatalog'][] = array_pop($_SESSION['searchResults']['VendorCatalog']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['VendorCatalog'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['VendorCatalog'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}
	} // function display

} // class VendorCatalog
?>