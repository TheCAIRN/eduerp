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
			LEFT OUTER JOIN item_categories c ON i.item_category_id=c.item_category_id";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
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
		$_SESSION['currentScreen'] = 110;
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
		FROM inv_master i
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
		$result = $stmt->execute($entid,$divid,$deptid,$typecode,$categoryid,$productcode,$description,$catalog,$productuom,
			$gtin,$stdcost,$msrp,$wholesale,$currency,$length,$width,$height,$lwhuom,$weight,$weightuom,$htc,$tariffrevision,
			$promotion_start_date,$promotion_end_date,$product_launch_date,$product_sunset_date,$product_end_of_support_date,
			$product_end_extended_support_date,$typedescription,$categorydescription,
			$visible,$revyn,$revnum,$createdby,$createddate,$updateby,$updatedate);
		if ($result!==false) {
			$stmt->bind_result();
			$stmt->fetch();
			if ($readonly) $cls = 'RecordView'; else $cls = 'RecordEdit';
			if ($readonly) $inputtextro = ' readonly="readonly"'; else $inputtextro = '';
			$html .= '<FIELDSET id="ItemRecord" class="'.$cls.'">';
			
			$html .= '</FIELDSET>';
		}
		$stmt->close();
		echo $html;
		$_SESSION['currentScreen'] = 210;
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