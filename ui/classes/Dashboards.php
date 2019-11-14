<?php
class Dashboards extends ERPBase {
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		$this->searchFields[] = array('dashboards','dashboard_list','Available Dashboards:','dropdown',array(
			array('1','Inventory On Hand')
			,array('2','Balance Sheet')
			,array('3','Income Statement')
		));
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
	
	} // resetHeader()
	public function listRecords() {
		parent::abstractListRecords('zzzz');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('DashboardsSearch');
	} // searchPage()
	private function runInventoryOnHand() {
		$html = '<FIELDSET class="searchPage" id="DashboardsSearch">';
		$html .= '<INPUT type="number" id="dashboardQueryEntity" placeholder="For Entity" />
			<BUTTON onClick="runDashboardQuery();">RUN</BUTTON>';
		$html .= '</FIELDSET>';
		echo $html;
		
	} // Inventory on Hand dashboard
	private function runBalanceSheet() {
		$html = '<FIELDSET class="searchPage" id="DashboardsSearch">';
		$html .= '<INPUT type="number" id="dashboardQueryYear" placeholder="For Year" />
			<BUTTON onClick="runDashboardQuery();">RUN</BUTTON>';
		$html .= '</FIELDSET>';
		echo $html;
		
	} // Balance Sheet dashboard
	private function runIncomeStatement() {
		$html = '<FIELDSET class="searchPage" id="DashboardsSearch">';
		$html .= '<INPUT type="number" id="dashboardQueryYear" placeholder="For Year" />
			<BUTTON onClick="runDashboardQuery();">RUN</BUTTON>';
		$html .= '</FIELDSET>';
		if (isset($_POST['searchParameters']) && is_array($_POST['searchParameters']) && count($_POST['searchParameters'])>=2 && $_POST['searchParameters'][0][0]=='dashboardQueryYear') {
			$dbYear = $_POST['searchParamters'][0][1];
			// TODO: This should all be coming from acgl_*, but the General Ledger is not yet fully supported.
			$revq = 'SELECT * FROM sales_header h JOIN sales_detail d ON h.sales_order_number=d.sales_order_number WHERE YEAR(h.order_invoiced_date)=? OR YEAR(d.line_invoiced_date)=?';
		}
		echo $html;
	} // Income Statment dashboard
	public function runDashboard($which=0) {
		if ($which==0) $which = $_SESSION['lastCriteria'];
		switch($which) {
			case 1: $this->runInventoryOnHand(); break;
			case 2: $this->runBalanceSheet(); break;
			case 3: $this->runIncomeStatement(); break;
			default: $mb = new MessageBar($this->dbconn); $mb->addWarning("Please select a dashboard from the list."); break;
		}
	} // runDashboard()
	public function executeSearch($criteria) {
		if (!is_null($criteria) && is_array($criteria) && count($criteria)>0) {
			// The only key for Dashboards is dashboard_list
			if (is_array($criteria[0]) && count($criteria[0])>=2 && $criteria[0][0]=='dashboard_list') {
				$criteria = $criteria[0][1];
			} elseif (isset($_SESSION['lastCriteria']) && $_SESSION['lastCriteria']+1 > 0) {
				$criteria = $_SESSION['lastCriteria'];
			} else {
				$mb = new MessageBar($this->dbconn);
				$mb->addWarning("Please select a dashboard from the list.");
				return;
			}
		} else {
			$mb = new MessageBar($this->dbconn);
			$mb->addWarning("Please select a dashboard from the list.");
			return;
		}
		$_SESSION['currentScreen'] = 1032;
		$_SESSION['lastCriteria'] = $criteria;
		$this->runDashboard($criteria);
	} // executeSearch()
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		if ($id<1) return false;
		if (is_integer($id)) return true;
		if (ctype_digit($id)) return true;
		return false;
	} // isIDValid()
	public function display($id) {
		if (!$this->isIDValid($id)) return;
		$readonly = true;
		$html = '';
		$q = "SELECT *
			FROM zzzz_master c 
			WHERE zzzz_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$zzzzid);
		$zzzzid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
			
			);
			$stmt->fetch();
			if ($readonly) $cls = 'RecordView'; else $cls = 'RecordEdit';
			if ($readonly) $inputtextro = ' readonly="readonly"'; else $inputtextro = '';
			$html .= '<FIELDSET id="zzzzRecord" class="'.$cls.'">';
			$html .= '<LABEL for="zzzzid">zzzz ID:</LABEL><B id="zzzzid">'.$id.'</B>';
			$html .= $this->statusSelect($status,$readonly);
			$html .= parent::displayRecordAudit($inputtextro,$crevyn,$crevnumber,$cuser_creation,$cdate_creation,$cuser_modify,$cdate_modify);
			$html .= '</FIELDSET>';
		} // if result
		$stmt->close();			
		echo $html;
		$_SESSION['currentScreen'] = 2032;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['zzzz']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['zzzz'],false);
			$f = $_SESSION['searchResults']['zzzz'][0];
			$l = $_SESSION['searchResults']['zzzz'][] = array_pop($_SESSION['searchResults']['zzzz']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['zzzz'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['zzzz'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
} // class Dashboards
?>