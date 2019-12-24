<?php
class CustomerTypes extends ERPBase {
	public function __construct ($link=null) {
	
	} // __construct
	public function resetHeader() {
	
	} // resetHeader()
	public function customerTypeSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'cust_types','cust_type_code','description','customer type');
	} // customerSelect()
	public function listRecords() {
		parent::abstractListRecords('CustomerTypes');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('CustomerTypesSearch');
	} // searchPage()
	public function executeSearch($criteria) {
	
	} // executeSearch()
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		return true;
	} // isIDValid()
	public function display($id) {
	
	} // display()
	public function newRecord() {
		echo parent::abstractNewRecord('CustomerTypes');
		$_SESSION['currentScreen'] = 3023;
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
} // class CustomerTypes
?>