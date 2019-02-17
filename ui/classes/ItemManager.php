<?php
class ItemManager extends ERPBase {
	public function __construct($link=null) {
		parent::__construct($link);
		$this->searchFields[] = array('item_master','product_id','ID','integer');
		$this->searchFields[] = array('item_master','product_code','Code','textbox');
		$this->searchFields[] = array('item_master','product_description','Description','textbox');
		$this->searchFields[] = array('item_master','gtin','GTIN','textbox');
		$this->searchFields[] = array('ent_entities',array('entity_id','entity_name'),'Entity','dropdown');
		$this->searchFields[] = array('ent_division_master',array('division_id','division_name'),'Division','dropdown');
		$this->searchFields[] = array('ent_department_master',array('department_id','department_name'),'Department','dropdown');
		$this->searchFields[] = array('item_types',array('item_type_code','item_type_description'),'Type','dropdown');
		$this->searchFields[] = array('item_categories',array('item_category_id','item_category_description'),'Category','dropdown');
		$this->searchFields[] = array('item_master','product_catalog_title','Catalog','textbox');
		$this->searchFields[] = array('item_master','visible','Visible','yn');
	} // constructor
	public function itemSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'item_master','product_id','product_code','item');
	} // function itemSelect
	public function searchPage() {
		parent::abstractSearchPage('ItemSearch');
	} // function searchPage()
	public function listRecords() {
		parent::abstractListRecords('ItemManager');
	} // function listRecords()
	public function executeSearch($criteria) {
		$q = "SELECT product_id,product_code,product_description,gtin,item_type_description,item_category_description,product_catalog_title,i.visible 
			FROM item_master i
			LEFT OUTER JOIN item_types t ON i.item_type_code=t.item_type_code
			LEFT OUTER JOIN item_categories c ON i.item_category_id=c.item_category_id ";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY product_id";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['product_id']] = array('code'=>$row['product_code'],'description'=>$row['product_description'],'gtin'=>$row['gtin'],
					'type'=>$row['item_type_description'],'category'=>$row['item_category_description'],'catalog'=>$row['product_catalog_title'],
					'visible'=>$row['visible']==0?'N':'Y');
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 113;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['Item'] = array_keys($this->recordSet);
			
	}
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
		$q = "SELECT i.entity_id, i.division_id, i.department_id, i.item_type_code, i.item_category_id, i.product_code, 
		i.product_description, i.product_catalog_title, i.product_uom, i.gtin, i.standard_cost, i.suggested_retail, i.wholesale_price, 
		i.currency, i.length, i.width, i.height, i.lwh_uom, i.weight, i.weight_uom, i.harmonized_tariff_code, i.tariff_revision, 
		i.promotion_start_date, i.promotion_end_date, i.product_launch_date, i.product_sunset_date, i.product_end_of_support_date, 
		i.product_end_extended_support_date, 
		t.item_type_description,c.item_category_description,
		i.visible, i.rev_enabled, i.rev_number, i.created_by, i.creation_date, i.last_update_by, i.last_update_date 
		FROM item_master i
		LEFT OUTER JOIN item_types t ON i.item_type_code=t.item_type_code
		LEFT OUTER JOIN item_categories c ON i.item_category_id=c.item_category_id
		WHERE i.product_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$productid);
		$productid = $id;
		$result = $stmt->execute();
		if ($result!==false) {
			$stmt->store_result();
			$stmt->bind_result($entid,$divid,$deptid,$typecode,$categoryid,$productcode,$description,$catalog,$productuom,
			$gtin,$stdcost,$msrp,$wholesale,$currency,$length,$width,$height,$lwhuom,$weight,$weightuom,$htc,$tariffrevision,
			$promotion_start_date,$promotion_end_date,$product_launch_date,$product_sunset_date,$product_end_of_support_date,
			$product_end_extended_support_date,$typedescription,$categorydescription,
			$visible,$revyn,$revnum,$createdby,$createddate,$updateby,$updatedate);
			$stmt->fetch();
			if ($readonly) $cls = 'RecordView'; else $cls = 'RecordEdit';
			if ($readonly) $inputtextro = ' readonly="readonly"'; else $inputtextro = '';
			$ent = new Entity($this->dbconn);
			$html .= '<FIELDSET id="ItemRecord" class="'.$cls.'">';
			$html .= '<DIV class="labeldiv"><LABEL for="productID">Product ID:</LABEL><B id="productID">'.$id.'</B></DIV>';
			$html .= $ent->entitySelect($entid,$readonly);
			$html .= $ent->divisionSelect($divid,$readonly);
			$html .= $ent->departmentSelect($deptid,$readonly);
			// TODO: Convert type and category to dropdowns.
			$html .= '<DIV class="labeldiv"><LABEL for="itemType">Type:</LABEL><INPUT type="text" id="itemType" value="'.$typecode.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="itemCategory">Category:</LABEL><INPUT type="text" id="itemCategory" value="'.$categoryid.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="productCode">Product Code:</LABEL><INPUT type="text" id="productCode" value="'.$productcode.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="itemCatalog">Catalog:</LABEL><INPUT type="text" id="itemCatalog" value="'.$catalog.'"'.$inputtextro.' /></DIV>';
			$html .= '<BR /><DIV style="display:block;"><LABEL for="itemDescription">Description:</LABEL><TEXTAREA id="itemDescription"'.$inputtextro.'>'.$description.'</TEXTAREA></DIV>';
			// TODO: Convert all UOM boxes to dropdowns.
			$html .= '<DIV class="labeldiv"><LABEL for="productUOM">Product UOM:</LABEL><INPUT type="text" id="productUOM" value="'.$productuom.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="itemGTIN">GTIN:</LABEL><INPUT type="text" id="itemGTIN" value="'.$gtin.'"'.$inputtextro.' />';
			if (!$readonly) $html .= "<BUTTON onClick=\"gtinCheck()\">CHECK</BUTTON><BUTTON onClick=\"gtinAssign()\">ASSIGN</BUTTON>"; // in "item.js"
			$html .= '</DIV>';
			// TODO: Convert currency to dropdown.
			$html .= '<DIV class="labeldiv"><LABEL for="currency">Currency:</LABEL><INPUT type="text" id="currency" value="'.$currency.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="standardCost">Standard Cost:</LABEL><INPUT type="number" step="any" id="standardCost" value="'.$stdcost.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="retailPrice">MSRP:</LABEL><INPUT type="number" step="any" id="retailPrice" value="'.$msrp.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="wholesalePrice">Wholesale Price:</LABEL><INPUT type="number" step="any" id="wholesalePrice" value="'.$wholesale.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="length">Length:</LABEL><INPUT type="number" step="any" id="length" value="'.$length.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="width">Width:</LABEL><INPUT type="number" step="any" id="width" value="'.$width.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="height">Height:</LABEL><INPUT type="number" step="any" id="height" value="'.$height.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="lwhUOM">L/W/H UOM:</LABEL><INPUT type="text" id="lwhUOM" value="'.$lwhuom.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="weight">Weight:</LABEL><INPUT type="number" step="any" id="weight" value="'.$weight.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="weightUOM">Weight UOM:</LABEL><INPUT type="text" id="weightUOM" value="'.$weightuom.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="htc">HTC:</LABEL><INPUT type="text" id="htc" placeholder="####.##.####" value="'.$htc.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="tariffRevision">HTC Revision:</LABEL><INPUT type="number" step="1" min="1" id="tariffRevision" value="'.$tariffrevision.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="promoStart">Promotion Start Date:</LABEL><INPUT type="date" id="promoStart" value="'.$promotion_start_date.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="promoEnd">Promotion End Date:</LABEL><INPUT type="date" id="promoEnd" value="'.$promotion_end_date.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="prodLaunch">Product Launch Date:</LABEL><INPUT type="date" id="prodLaunch" value="'.$product_launch_date.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="prodSunset">Product Sunset Date:</LABEL><INPUT type="date" id="prodSunset" value="'.$product_sunset_date.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="prodEOS">Product End of Support Date:</LABEL><INPUT type="date" id="prodEOS" value="'.$product_end_of_support_date.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="promEOES">Product End of Extended Support Date:</LABEL><INPUT type="date" id="prodEOES" value="'.$product_end_extended_support_date.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV id="RecordAudit">';
			$html .= '<DIV class="labeldiv"><LABEL for="visible">Is product visible?<INPUT type="checkbox" id="visible"'.$inputtextro.' '.($visible==1?'checked="checked" />':'/>').'</DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="revenabled">Revision Enabled:</LABEL><INPUT type="checkbox" id="revenabled" '.$inputtextro.' '.($revyn=='Y'?'checked="checked" />':'/>').'</DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="revnumber">Revision Number:</LABEL><INPUT type="number" id="revnumber" value="'.$revnum.'"'.$inputtextro.' /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="createdby">Created By:</LABEL><INPUT type="text" id="createdby" value="'.$createdby.'" readonly="readonly" /></DIV>';	// These 4 fields can only ever be modified by the system
			$html .= '<DIV class="labeldiv"><LABEL for="createdon">Created On:</LABEL><INPUT type="date" id="createdon" value="'.$createddate.'" readonly="readonly" /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="modifiedby">Modified By:</LABEL><INPUT type="text" id="modifiedby" value="'.$updateby.'" readonly="readonly" /></DIV>';
			$html .= '<DIV class="labeldiv"><LABEL for="modifiedon">Modified On:</LABEL><INPUT type="date" id="modifiedon" value="'.$updatedate.'" readonly="readonly" /></DIV>';			
			$html .= '</DIV>';
			$html .= '</FIELDSET>';
		}
		$stmt->close();
		echo $html;
		$_SESSION['currentScreen'] = 213;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['Item']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['Item'],false);
			$f = $_SESSION['searchResults']['Item'][0];
			$l = $_SESSION['searchResults']['Item'][] = array_pop($_SESSION['searchResults']['Item']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['Item'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['Item'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	}
} // class ItemManager
?>