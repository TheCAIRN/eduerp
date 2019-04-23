<?php
class Customer extends ERPBase {
	public function __construct ($link=null) {
	
	} // __construct
	public function resetHeader() {
	
	} // resetHeader()
	public function customerSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'cust_master','customer_id','customer_name','customer');
	} // customerSelect()
	public function statusSelect($status='',$readonly=false) {
		$html = '<LABEL for="customerStatus">Status:</LABEL><SELECT id="customerStatus">';
		if ($status=='A' || !$readonly) $html .= '<OPTION value="A"'.($status=='A'?' selected="selected">':'>').'Active</OPTION>';
		if ($status=='B' || !$readonly) $html .= '<OPTION value="B"'.($status=='B'?' selected="selected">':'>').'Bankrupt</OPTION>';
		if ($status=='D' || !$readonly) $html .= '<OPTION value="D"'.($status=='D'?' selected="selected">':'>').'Defunct</OPTION>';
		if ($status=='I' || !$readonly) $html .= '<OPTION value="I"'.($status=='I'?' selected="selected">':'>').'Temporarily Inactive</OPTION>';
		if ($status=='S' || !$readonly) $html .= '<OPTION value="S"'.($status=='A'?' selected="selected">':'>').'Seasonally Inactive</OPTION>';
		$html .= '</SELECT>';
		return $html;
	} // statusSelect()
	public function listRecords() {
		parent::abstractListRecords('Customer');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('CustomerSearch');
	} // searchPage()
	public function executeSearch($criteria) {
	
	} // executeSearch()
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		if ($id<1) return false;
		if (is_integer($id)) return true;
		if (ctype_digit($id)) return true;
		return false;
	} // isIDValid()
	public function display($id) {
	
	} // display()
	public function newRecord() {
		echo parent::abstractNewRecord('Customer');
		$_SESSION['currentScreen'] = 324;
	} // newRecord()
	private function insertHeader() {
	
	} // insertHeader()
	private function updateHeader() {
	
	} // updateHeader()
	public function insertRecord() {
		$this->insertHeader();
	} // insertRecord()
	public function updateRecord() {
		$this->updateHeader();
	} // updateRecord()
	public function saveRecord() {
	
	} // saveRecord()
} // class Customer
?>