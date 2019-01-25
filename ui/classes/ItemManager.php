<?php
class ItemManager extends ERPBase {
	public function __construct($link=null) {
		parent::__construct($link);
		
	} // constructor
	public function searchPage() {
		parent::abstractSearchPage('ItemSearch');
	} // function searchPage()
	public function executeSearch() {
		
	}
	public function display($id) {
		
	}
} // class ItemManager
?>