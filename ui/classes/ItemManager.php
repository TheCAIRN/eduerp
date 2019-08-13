<?php
class ItemManager extends ERPBase {
	private $entity_id;
	private $division_id;
	private $department_id;
	private $item_type_code;
	private $item_type_description;
	private $item_category_id;
	private $item_category_description;
	private $product_id;
	private $product_code;
	private $product_description;
	private $product_catalog_title;
	private $product_uom;
	private $gtin;
	private $season;
	private $standard_cost;
	private $suggested_retail;
	private $wholesale_price;
	private $currency_code;
	private $length;
	private $width;
	private $height;
	private $lwh_uom;
	private $weight;
	private $weight_uom;
	private $htc;
	private $tariff_revision;
	private $promotion_start_date;
	private $promotion_end_date;
	private $product_launch_date;
	private $product_sunset_date;
	private $product_end_of_support_date;
	private $product_end_extended_support_date;
	private $visible;
	private $rev_enabled;
	private $rev_number;
	private $column_list;
	public function __construct($link=null) {
		parent::__construct($link);
		$this->supportsNotes = 'item_notes';
		$this->supportsAttachments = 'item_attachments';
		$this->primaryKey = 'product_id';
		/*
		Revised 13 May 2019 to use unified_search instead of field based search (Michael J. Sabal)
		
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
		*/
		$this->searchFields[] = array('item_master','unified_search','Type in any item related info and click Search','textbox');
		// For embeddable classes, don't use fieldset.
		$this->entryFields[] = array('item_master','entity_id','Entity','dropdown','ent_entities',array('entity_id','entity_name'));
		$this->entryFields[] = array('item_master','division_id','Division','dropdown','ent_division_master',array('division_id','division_name'));
		$this->entryFields[] = array('item_master','department_id','Department','dropdown','ent_department_master',array('department_id','department_name'));
		$this->entryFields[] = array('item_master','item_type_code','Type','dropdown','item_types',array('item_type_code','item_type_description'));
		$this->entryFields[] = array('item_master','item_category_id','Category','dropdown','item_categories',array('item_category_id','item_category_description'));
		$this->entryFields[] = array('item_master','product_id','Product ID','integerid');
		$this->entryFields[] = array('item_master','product_code','Product Code','textbox');
		$this->entryFields[] = array('item_master','product_description','Description','textbox');
		$this->entryFields[] = array('item_master','product_catalog_title','Catalog Title','textbox');
		$this->entryFields[] = array('item_master','product_uom','Product UOM','dropdown','aa_uom',array('uom_code','uom_description'));
		$this->entryFields[] = array('item_master','gtin','GTIN','textbox');
		$this->entryFields[] = array('item_master','gtin','Assign GTIN','button','gtinAssign();');
		$this->entryFields[] = array('item_master','standard_cost','Cost','decimal',24,5);
		$this->entryFields[] = array('item_master','suggested_retail','MSRP','decimal',24,5);
		$this->entryFields[] = array('item_master','wholesale_price','Wholesale','decimal',24,5);
		$this->entryFields[] = array('item_master','currency_code','Currency','dropdown','aa_currency',array('code','code'));
		$this->entryFields[] = array('item_master','length','Length','decimal',11,3);
		$this->entryFields[] = array('item_master','width','Width','decimal',11,3);
		$this->entryFields[] = array('item_master','height','Height','decimal',11,3);
		$this->entryFields[] = array('item_master','lwh_uom','LWH UOM','dropdown','aa_uom',array('uom_code','uom_description'),1);
		$this->entryFields[] = array('item_master','weight','Weight','decimal',11,5);
		$this->entryFields[] = array('item_master','weight_uom','Weight UOM','dropdown','aa_uom',array('uom_code','uom_description'),3);
		$this->entryFields[] = array('item_master','harmonized_tariff_code','HTC','textbox'); // TODO: Change to dropdown
		$this->entryFields[] = array('item_master','tariff_revision','HTC Revision','integer');
		$this->entryFields[] = array('item_master','promotion_start_date','Promotion Start','datetime');
		$this->entryFields[] = array('item_master','promotion_end_date','Promotion End','datetime');
		$this->entryFields[] = array('item_master','product_launch_date','Product launch','date');
		$this->entryFields[] = array('item_master','product_sunset_date','Product sunset','date');
		$this->entryFields[] = array('item_master','product_end_of_support_date','End of support','date');
		$this->entryFields[] = array('item_master','product_end_extended_support_date','End of LTS','date');
		$this->entryFields[] = array('item_master','visible','Visible','checkbox');
		$this->entryFields[] = array('item_master','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('item_master','rev_number','Revision number','integer');
		$this->resetHeader();
	} // constructor
	public function jquery() {
		if (isset($_POST['mode'])) {
			if ($_POST['mode']=='gtinAssign') {
				if (!isset($_POST['entity_id']) || !isset($_POST['division_id']) || !isset($_POST['department_id']) || !isset($_POST['item_type_code'])) {
					echo 'fail|POST is missing necessary information.';
					return;
				}
				$gtinmanager = new GTINManager($this->dbconn);
				// assignOneGTIN echoes the result directly
				$gtinmanager->assignOneGTIN($_POST['entity_id'],$_POST['division_id'],$_POST['department_id'],$_POST['item_type_code']);
			}
		}
	} // itemjq
	public function resetHeader() {
		$this->entity_id = 0;
		$this->division_id = 0;
		$this->department_id = 0;
		$this->item_type_code = '';
		$this->item_type_description = '';
		$this->item_category_id = 0;
		$this->item_category_description = '';
		$this->product_id = 0;
		$this->product_code = '';
		$this->product_description = '';
		$this->product_catalog_title = '';
		$this->product_uom = '';
		$this->gtin = '';
		$this->season = 0;
		$this->standard_cost = 0.00;
		$this->suggested_retail = 0.00;
		$this->wholesale_price = 0.00;
		$this->currency_code = '';
		$this->length = 0.00;
		$this->width = 0.00;
		$this->height = 0.00;
		$this->lwh_uom = '';
		$this->weight = 0.00;
		$this->weight_uom = '';
		$this->htc = '';
		$this->tariff_revision = 0;
		$this->promotion_start_date = null;
		$this->promotion_end_date = null;
		$this->product_launch_date = null;
		$this->product_sunset_date = null;
		$this->product_end_of_support_date = null;
		$this->product_end_extended_support_date = null;
		$this->visible = 1;
		$this->rev_enabled = 'Y';
		$this->rev_number = 1;
		$this->column_list = 'entity_id, division_id, department_id, item_type_code, item_category_id, product_id, product_code, 
			product_description, product_catalog_title, product_uom, gtin, standard_cost, suggested_retail, wholesale_price, 
			currency, length, width, height, lwh_uom, weight, weight_uom, harmonized_tariff_code, tariff_revision, 
			promotion_start_date, promotion_end_date, product_launch_date, product_sunset_date, product_end_of_support_date, 
			product_end_extended_support_date, 
			visible, rev_enabled, rev_number, created_by, creation_date, last_update_by, last_update_date';
	} // resetHeader()
	public function arrayify() {
		return array('entity_id'=>$this->entity_id, 'division_id'=>$this->division_id, 'department_id'=>$this->department_id, 'item_type_code'=>$this->item_type_code, 
			'item_category_id'=>$this->item_category_id, 'product_id'=>$this->product_id, 'product_code'=>$this->product_code, 
			'product_description'=>$this->product_description, 'product_catalog_title'=>$this->product_catalog_title, 'product_uom'=>$this->product_uom, 
			'gtin'=>$this->gtin, 'standard_cost'=>$this->standard_cost, 'suggested_retail'=>$this->suggested_retail, 'wholesale_price'=>$this->wholesale_price, 
			'currency_code'=>$this->currency_code, 'length'=>$this->length, 'width'=>$this->width, 'height'=>$this->height, 'lwh_uom'=>$this->lwh_uom, 
			'weight'=>$this->weight, 'weight_uom'=>$this->weight_uom, 'harmonized_tariff_code'=>$this->harmonized_tariff_code, 'tariff_revision'=>$this->tariff_revision, 
			'promotion_start_date'=>$this->promotion_start_date, 'promotion_end_date'=>$this->promotion_end_date, 'product_launch_date'=>$this->product_launch_date, 
			'product_sunset_date'=>$this->product_sunset_date, 'product_end_of_support_date'=>$this->product_end_of_support_date, 
			'product_end_of_extended_support_date'=>$this->product_end_extended_support_date, 
			'visible'=>$this->visible, 'rev_enabled'=>$this->rev_enabled, 'rev_number'=>$this->rev_number, 'created_by'=>$this->created_by, 
			'creation_date'=>$this->creation_date, 'last_update_by'=>$this->last_update_by, 'last_update_date'=>$this->last_update_date);
	} // arrayify
	/*
	 * Item fields are linked from many different tables within the ERP system.  As a result, many other modules need to have access to 
	 * look up, select, and add item records.  The embed method provides that capability without changing $_SESSION['currentScreen'] or
	 * requiring the user to open a new tab.
	 *
	 * $id = The HTML id attribute of the fieldset.
	 * $mode = ['search' | 'lookup' | 'new' | 'save' | 'display']
	 * $data = An array of item fields, or other data as appropriate to the mode.
	 */
	public function embed($id='item',$mode='search',$data=null) {
		if ($mode=='search') {
			return $this->embed_search($id,$data);
		} elseif ($mode=='lookup') {
			return $this->embed_lookup($id,$data);
		} elseif ($mode=='display') {
			return $this->embed_display($id,$data,false);
		} elseif ($mode=='display readonly') {
			return $this->embed_display($id,$data,true);
		} elseif ($mode=='new') {
			return $this->embed_new($id,$data);
		} elseif ($mode=='save') {
			return $this->embed_save($id,$data);
		} else {
			$this->mb->addError('JQ Embedded Item does not understand mode, "'.$mode.'".');
		}
	} // embed()
	private function embed_search($id='item',$data=null) {
		$html = "<INPUT type=\"text\" id=\"$id\" placeholder=\"Type in any item related info and click Search\" size=\"50\" />
			<BUTTON onClick=\"embeddedItemSearch('$id');\">Search</BUTTON>
			<BUTTON onClick=\"embeddedItemList('$id');\">List</BUTTON>
			<BUTTON onClick=\"embeddedItemNew('$id');\">New</BUTTON>";
		return $html;
	} // embed_search()
	private function embed_lookup($id='item',$data=null) {
		$q = "SELECT product_id,product_description FROM item_master 
			JOIN item_categories ON item_master.item_category_id = item_categories.item_category_id";
		$html = $this->embed_search($id).'<BR /><SELECT id="'.$id.'-select"><OPTION value="[new]">--Create a new record--</OPTION>';
		$slevel = 0;
		if (is_null($data) || $data=='') {
			$slevel = 1;
		} elseif (strpos($data,' ')===false && strpos($data,',')===false) {
			// one word search
			$q .= " WHERE item_category_description LIKE ? OR product_id = ? OR product_code LIKE ? OR product_description LIKE ? OR product_catalog_title LIKE ? or gtin=?";
			$slevel = 2;
		} elseif (strpos($data,',')===false) {
			// spaces, but no commas
			
		}
		$stmt = $this->dbconn->prepare($q);
		switch ($slevel) {
			case 2:
				$stmt->bind_param('sissss',$p1,$p2,$p3,$p4,$p5,$p6);
				$p1 = $p3 = $p4 = $p5 = '%'.$data.'%';
				$p6 = $data;
				$p2 = ctype_digit($data)?$data:-99999;
				break;
		}
		$result = $stmt->execute();
		if ($result === false) {
			$this->mb->addError($this->dbconn->error);
		} else {
			$stmt->store_result();
			$stmt->bind_result($pid,$pdesc);
			while ($stmt->fetch()) {
				$html .= '<OPTION value="'.$pid.'">'.$pdesc.'</OPTION>';
			} // fetch
		}
		$html .= '</SELECT>';
		$html .= "<BUTTON onClick=\"embeddedItemSelect('$id');\">Select</BUTTON>";
		return $html;		
	} // embed_lookup()
	private function embed_display($id='item',$data=null,$readonly=true) {
		if (!($this->isIDValid($data))) {
			$this->mb->addError("JQ Embedded Item: Selected ID is not valid.");
			return $this->embed_search($id);
		}
		$q = "SELECT product_id,product_code,product_description,gtin FROM item_master WHERE product_id=?";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('i',$p1);
		$p1 = $data;
		$return = $stmt->execute();
		$stmt->store_result();
		if ($return===false || $stmt->num_rows==0) {
			$this->mb->addError("JQ Embedded Item: Selected ID is not valid.");
			return $this->embed_search($id);
		}
		$stmt->bind_result($pid,$pcode,$pdesc,$gtin);
		if ($stmt->fetch()) {
			$html = '';
			if (!$readonly) $html .= $this->embed_search($id).'<BR />';
			$html .= '<DIV class="labeldiv"><LABEL for="'.$id.'-product_id">ID:</LABEL><B id="'.$id.'-product_id">'.$pid.'</B></DIV>';
			//if ($readonly) {
				$html .= '&nbsp;<I id="'.$id.'-product_code">'.$pcode.'</I>&nbsp;'.$pdesc.'<BR />';
				if ($gtin!='') $html .= 'GTIN: '.$gtin;
			//}
			return $html;
		} else {
			return $this->embed_search($id);
		}
	} // embed_display()
	public function embed_new($id='item',$data=null) {
		$html = parent::abstractNewRecord('ItemManager',$id);
		$html .= "<BR /><BUTTON onClick=\"embeddedItemSave('$id');\">Save</BUTTON><BR />";
		$html .= $this->embed_search($id);
		return $html;
	} // embed_new()
	public function embed_save($id='item',$data=null) {
		$this->insertHeader(true);
		if ($this->id==0) {
			return $this->embed_new($id,null);
		} else {
			return $this->embed_display($id,$this->id);
		}
	} // embed_save()	
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
		$result = null;
		$q = "SELECT product_id,product_code,product_description,gtin,item_type_description,item_category_description,product_catalog_title,i.visible 
			FROM item_master i
			LEFT OUTER JOIN item_types t ON i.item_type_code=t.item_type_code
			LEFT OUTER JOIN item_categories c ON i.item_category_id=c.item_category_id ";
		// Add $criteria
		if (!is_null($criteria) && is_array($criteria) && count($criteria)>0) {
			// The only key for Addresses is unified_search.
			if (is_array($criteria[0]) && count($criteria[0])>=2 && $criteria[0][0]=='unified_search') $criteria = $criteria[0][1];
			else $criteria='';
			$criteria = str_replace('berry','berr',$criteria); // The PLU master typically uses plural for berries, but most people search singular
			$q .= " WHERE item_category_description LIKE ? OR product_id = ? OR product_code LIKE ? OR product_description LIKE ? OR product_catalog_title LIKE ? or gtin=?";
			$stmt = $this->dbconn->prepare($q);
			$stmt->bind_param('sissss',$p1,$p2,$p3,$p4,$p5,$p6);
			$p1 = $p3 = $p4 = $p5 = '%'.$criteria.'%';
			$p6 = $criteria;
			$p2 = ctype_digit($criteria)?$criteria:-99999;
			$result = $stmt->execute();
			if ($result !== false) {
				$this->recordSet = array();
				if (isset($_SESSION['recordSet']['ItemManager'])) unset($_SESSION['recordSet']['ItemManager']); // A search criteria was given, so do not display the last search on an empty set.
				$stmt->store_result();
				$stmt->bind_result($this->product_id,$this->product_code,$this->product_description,$this->gtin,$this->item_type_description,
					$this->item_category_description,$this->product_catalog_title,$this->visible);
				while ($stmt->fetch()) {
					$this->recordSet[$this->product_id] = array('code'=>$this->product_code,'description'=>$this->product_description,'gtin'=>$this->gtin,
						'type'=>$this->item_type_description,'category'=>$this->item_category_description,'catalog'=>$this->product_catalog_title,
						'visible'=>($this->visible==0?'N':'Y'));
				}
			}
		// if criteria exists
		} else {
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
		} // if criteria does not exist
		$this->listRecords();
		$_SESSION['currentScreen'] = 1013;
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
	public function display($id,$mode='view') {
		if (!($this->isIDValid($id))) return;
		$readonly = true;
		$html = '';
		$q = "SELECT {$this->column_list} FROM item_master WHERE product_id=?;";
/*		$q = "SELECT i.entity_id, i.division_id, i.department_id, i.item_type_code, i.item_category_id, i.product_code, 
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
*/		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$productid);
		$productid = $id;
		$result = $stmt->execute();
		if ($result!==false) {
			$stmt->bind_result($this->entity_id, $this->division_id, $this->department_id, $this->item_type_code, $this->item_category_id, $this->product_id, $this->product_code, 
				$this->product_description, $this->product_catalog_title, $this->product_uom, $this->gtin, $this->standard_cost, $this->suggested_retail, $this->wholesale_price, 
				$this->currency_code, $this->length, $this->width, $this->height, $this->lwh_uom, $this->weight, $this->weight_uom, $this->harmonized_tariff_code, $this->tariff_revision, 
				$this->promotion_start_date, $this->promotion_end_date, $this->product_launch_date, $this->product_sunset_date, $this->product_end_of_support_date, 
				$this->product_end_extended_support_date, 
				$this->visible, $this->rev_enabled, $this->rev_number, $this->created_by, $this->creation_date, $this->last_update_by, $this->last_update_date);
			$stmt->store_result();
			$stmt->fetch();
			$this->currentRecord = $id;
/*
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
*/
		}
		$stmt->close();
		if ($mode!='update') {
			$hdata = $this->arrayify();
			echo '<FIELDSET id="ItemRecord" class="Record'.ucwords($mode).'">';
			echo '<LEGEND onClick="$(this).siblings().toggle();">Item</LEGEND>';
			echo parent::abstractRecord($mode,'ItemManager','',$hdata,null);
			echo '</FIELDSET>';
		}
//		echo $html;
		$_SESSION['currentScreen'] = 2013;
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
	} // display()
	public function newRecord() {
		echo '<FIELDSET class="RecordEdit" id="ItemManager_edit">';
		echo '<LEGEND onClick="$(this).siblings().toggle();">Item</LEGEND>';
		echo parent::abstractNewRecord('ItemManager');
		echo '</FIELDSET>';
		$_SESSION['currentScreen'] = 3013;
	} // newRecord()
	public function editRecord($id) {
		$this->display($id,'edit');
		$_SESSION['currentScreen'] = 4013;
	}
	private function insertHeader($embed=false) {
		$this->resetHeader();
		if (!isset($_POST['data'])) {
			$this->mb->addError("No data was submitted.  Item save failed.");
		}
		$data = $_POST['data'];
		$this->entity_id = isset($data['entity_id'])?$data['entity_id']:0;
		$this->division_id = isset($data['division_id'])?$data['division_id']:0;
		$this->department_id = isset($data['department_id'])?$data['department_id']:0;
		$this->item_type_code = isset($data['item_type_code'])?$data['item_type_code']:null;
		$this->item_category_id = isset($data['item_category_id'])?$data['item_category_id']:null;
		$this->product_id = isset($data['product_id'])?$data['product_id']:0;
		$this->product_code = isset($data['product_code'])?$data['product_code']:null;
		$this->product_description = isset($data['product_description'])?$data['product_description']:'';
		$this->product_catalog_title = isset($data['product_catalog_title'])?$data['product_catalog_title']:'';
		$this->product_uom = isset($data['product_uom'])?$data['product_uom']:null;
		$this->gtin = isset($data['gtin'])?$data['gtin']:'';
		$this->standard_cost = isset($data['standard_cost'])?$data['standard_cost']:0.00;
		$this->suggested_retail = isset($data['suggested_retail'])?$data['suggested_retail']:0.00;
		$this->wholesale_price = isset($data['wholesale_price'])?$data['wholesale_price']:0.00;
		$this->currency_code = isset($data['currency_code'])?$data['currency_code']:null;
		$this->length = isset($data['length'])?$data['length']:0.00;
		$this->width = isset($data['width'])?$data['width']:0.00;
		$this->height = isset($data['height'])?$data['height']:0.00;
		$this->lwh_uom = isset($data['lwh_uom'])?$data['lwh_uom']:null;
		$this->weight = isset($data['weight'])?$data['weight']:0.00;
		$this->weight_uom = isset($data['weight_uom'])?$data['weight_uom']:null;
		$this->harmonized_tariff_code = isset($data['harmonized_tariff_code'])?$data['harmonized_tariff_code']:null;
		$this->tariff_revision = isset($data['tariff_revision'])?$data['tariff_revision']:1;
		if (!empty($data['promotion_start_dated']) && !empty($data['promotion_start_datet'])) {
			$this->promotion_start_date = new DateTime($data['promotion_start_dated'].' '.$data['promotion_start_datet']);
		} else $this->promotion_start_date = null;
		if (!empty($data['promotion_end_dated']) && !empty($data['promotion_end_datet'])) {
			$this->promotion_end_date = new DateTime($data['promotion_end_dated'].' '.$data['promotion_end_datet']);
		} else $this->promotion_end_date = null;
		$this->product_launch_date = !empty($data['product_launch_dated'])?(new DateTime($data['product_launch_dated'])):null;
		$this->product_sunset_date = !empty($data['product_sunset_dated'])?(new DateTime($data['product_sunset_dated'])):null;
		$this->product_end_of_support_date = !empty($data['product_end_of_support_dated'])?(new DateTime($data['product_end_of_support_dated'])):null;
		$this->product_end_extended_support_date = !empty($data['product_end_of_extended_support_dated'])?(new DateTime($data['product_end_of_extended_support_dated'])):null;
		$this->visible = isset($data['visible'])?($data['visible']=='true'?1:0):1;
		$this->rev_enabled = isset($data['rev_enabled'])?($data['rev_enabled']=='true'?'Y':'N'):'Y';
		$this->rev_number = isset($data['rev_number'])?$data['rev_number']:1;
		$q = "INSERT INTO item_master ({$this->column_list}) VALUES (?,?,?,?,?,NULL,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		if (!$stmt) {
			echo 'fail|'.$this->dbconn->error;
		}
		$stmt->bind_param('iiisisssssdddsdddsdsiissssssisiii',
			$p1,$p2,$p3,$p4,$p5,
			$p6,$p7,$p8,$p9,$p10,
			$p11,$p12,$p13,$p14,
			$p15,$p16,$p17,$p18,
			$p19,$p20,$p21,$p22,
			$p23,$p24,$p25,$p26,$p27,$p28,
			$p29,$p30,$p31,$p32,$p33
		);
		$p1 = $this->entity_id;
		$p2 = $this->division_id;
		$p3 = $this->department_id;
		$p4 = $this->item_type_code;
		$p5 = $this->item_category_id;
		$p6 = $this->product_code;
		$p7 = $this->product_description;
		$p8 = $this->product_catalog_title;
		$p9 = $this->product_uom;
		$p10 = $this->gtin;
		$p11 = $this->standard_cost;
		$p12 = $this->suggested_retail;
		$p13 = $this->wholesale_price;
		$p14 = $this->currency_code;
		$p15 = $this->length;
		$p16 = $this->width;
		$p17 = $this->height;
		$p18 = $this->lwh_uom;
		$p19 = $this->weight;
		$p20 = $this->weight_uom;
		$p21 = $this->harmonized_tariff_code;
		$p22 = $this->tariff_revision;
		$p23 = is_null($this->promotion_start_date)?null:$this->promotion_start_date->format('Y-m-d H:i:s');
		$p24 = is_null($this->promotion_end_date)?null:$this->promotion_end_date->format('Y-m-d H:i:s');
		$p25 = is_null($this->product_launch_date)?null:$this->product_launch_date->format('Y-m-d');
		$p26 = is_null($this->product_sunset_date)?null:$this->product_sunset_date->format('Y-m-d');
		$p27 = is_null($this->product_end_of_support_date)?null:$this->product_end_of_support_date->format('Y-m-d');
		$p28 = is_null($this->product_end_extended_support_date)?null:$this->product_end_extended_support_date->format('Y-m-d');
		$p29 = $this->visible;
		$p30 = $this->rev_enabled;
		$p31 = $this->rev_number;
		$p32 = $_SESSION['dbuserid'];
		$p33 = $_SESSION['dbuserid'];
		$result = $stmt->execute();
		if ($result!==false) {
			echo 'inserted|'.$this->dbconn->insert_id;
		} else {
			echo 'fail|'.$this->dbconn->error;
			$this->mb->addError($this->dbconn->error);
		}
		$stmt->close();		
	} // insertHeader()
	private function updateHeader($embed=false) {
		$this->resetHeader();
		$now = new DateTime();
		$data = $_POST['data'];
		$id = $data['product_id'];
		if ((!is_integer($id) && !ctype_digit($id)) || $id<1) {
			echo 'fail|Invalid product id for updating';
			return;
		}
		$this->product_id = $id;
		$this->display($id,'update'); // Display already has the logic for loading the record.  TODO: Refactor into separate function.
		$update = array();
		if (isset($data['entity_id'])) if ($data['entity_id']!=$this->entity_id) $update['entity_id'] = array('i',$data['entity_id']);
		if (isset($data['division_id'])) if ($data['division_id']!=$this->division_id) $update['division_id'] = array('i',$data['division_id']);
		if (isset($data['department_id'])) if ($data['department_id']!=$this->department_id) $update['department_id'] = array('i',$data['department_id']);
		if (isset($data['item_type_code'])) if ($data['item_type_code']!=$this->item_type_code) $update['item_type_code'] = array('s',$data['item_type_code']);
		if (isset($data['item_category_id'])) if ($data['item_category_id']!=$this->item_category_id) $update['item_category_id'] = array('i',$data['item_category_id']);
		if (isset($data['product_code'])) if ($data['product_code']!=$this->product_code) $update['product_code'] = array('s',$data['product_code']);
		if (isset($data['product_description'])) if ($data['product_description']!=$this->product_description) $update['product_description'] = array('s',$data['product_description']);
		if (isset($data['product_catalog_title'])) if ($data['product_catalog_title']!=$this->product_catalog_title) $update['product_catalog_title'] = array('s',$data['product_catalog_title']);
		if (isset($data['product_uom'])) if ($data['product_uom']!=$this->product_uom) $update['product_uom'] = array('s',$data['product_uom']);
		if (isset($data['gtin'])) if ($data['gtin']!=$this->gtin) $update['gtin'] = array('s',$data['gtin']);
		if (isset($data['standard_cost'])) if ($data['standard_cost']!=$this->standard_cost) $update['standard_cost'] = array('d',$data['standard_cost']);
		if (isset($data['suggested_retail'])) if ($data['suggested_retail']!=$this->suggested_retail) $update['suggested_retail'] = array('d',$data['suggested_retail']);
		if (isset($data['wholesale_price'])) if ($data['wholesale_price']!=$this->wholesale_price) $update['wholesale_price'] = array('d',$data['wholesale_price']);
		if (isset($data['currency_code'])) if ($data['currency_code']!=$this->currency_code) $update['currency_code'] = array('s',$data['currency_code']);
		if (isset($data['length'])) if ($data['length']!=$this->length) $update['length'] = array('d',$data['length']);
		if (isset($data['width'])) if ($data['width']!=$this->width) $update['width'] = array('d',$data['width']);
		if (isset($data['height'])) if ($data['height']!=$this->height) $update['height'] = array('d',$data['height']);
		if (isset($data['lwh_uom'])) if ($data['lwh_uom']!=$this->lwh_uom) $update['lwh_uom'] = array('s',$data['lwh_uom']);
		if (isset($data['weight'])) if ($data['weight']!=$this->weight) $update['weight'] = array('d',$data['weight']);
		if (isset($data['weight_uom'])) if ($data['weight_uom']!=$this->weight_uom) $update['weight_uom'] = array('s',$data['weight_uom']);
		if (isset($data['harmonized_tariff_code'])) if ($data['harmonized_tariff_code']!=$this->harmonized_tariff_code) $update['harmonized_tariff_code'] = array('i',$data['harmonized_tariff_code']);
		if (isset($data['tariff_revision'])) if ($data['tariff_revision']!=$this->tariff_revision) $update['tariff_revision'] = array('i',$data['tariff_revision']);
		$dt1 = null;
		$dt2 = null;
		if (!empty($data['promotion_start_dated']) && !empty($data['promotion_start_datet'])) {
			$dt1 = new DateTime($data['promotion_start_dated'].' '.$data['promotion_start_datet']);
		} else $this->promotion_start_date = null;
		if (!empty($data['promotion_end_dated']) && !empty($data['promotion_end_datet'])) {
			$dt2 = new DateTime($data['promotion_end_dated'].' '.$data['promotion_end_datet']);
		} else $this->promotion_end_date = null;
		$dt3 = !empty($data['product_launch_dated'])?(new DateTime($data['product_launch_dated'])):null;
		$dt4 = !empty($data['product_sunset_dated'])?(new DateTime($data['product_sunset_dated'])):null;
		$dt5 = !empty($data['product_end_of_support_dated'])?(new DateTime($data['product_end_of_support_dated'])):null;
		$dt6 = !empty($data['product_end_of_extended_support_dated'])?(new DateTime($data['product_end_of_extended_support_dated'])):null;
		if ($dt1!=$this->promotion_start_date) $update['promotion_start_date'] = array('s',empty($dt1)?null:$dt1->format('Y-m-d H:i:s'));
		if ($dt2!=$this->promotion_end_date) $update['promotion_end_date'] = array('s',empty($dt2)?null:$dt2->format('Y-m-d H:i:s'));
		if ($dt3!=$this->product_launch_date) $update['product_launch_date'] = array('s',empty($dt3)?null:$dt3->format('Y-m-d'));
		if ($dt4!=$this->product_sunset_date) $update['product_sunset_date'] = array('s',empty($dt4)?null:$dt4->format('Y-m-d'));
		if ($dt5!=$this->product_end_of_support_date) $update['product_end_of_support_date'] = array('s',empty($dt5)?null:$dt5->format('Y-m-d'));
		if ($dt6!=$this->product_end_extended_support_date) $update['product_end_extended_support_date'] = array('s',empty($dt6)?null:$dt6->format('Y-m-d'));
		$vis = isset($data['visible'])?($data['visible']=='true'?1:0):1;
		$rev = isset($data['rev_enabled'])?($data['rev_enabled']=='true'?'Y':'N'):'Y';
		if ($vis!=$this->visible) $update['visible'] = array('i',$vis);
		if ($rev!=$this->rev_enabled) $update['rev_enabled'] = array('s',$rev);
		if (isset($data['rev_number'])) if ($data['rev_number']!=$this->rev_number) $update['rev_number'] = array('i',$data['rev_number']);
		if (count($update)==0) {
			echo 'fail|Nothing to update';
			return;
		}
		$update['last_update_by'] = array('i',$_SESSION['dbuserid']);
		$update['last_update_date'] = array('s',$now->format('Y-m-d H:i:s'));
		$q = 'UPDATE item_master SET ';
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
		$q .= ' WHERE product_id=?';
		$ctr++;
		$bp_types .= 'i';
		$bp_values[$ctr] = $this->product_id;
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
} // class ItemManager
?>