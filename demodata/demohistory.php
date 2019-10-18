<?php
/*
 * This script is to create the backstory transactions for the entities in demodata.
 * See democron.php for the continued daily simulation.
 */
session_name('eduerpcfg');
session_start();
/*
 * Modify $basepath to reflect the directory where the ui is stored.
 * If using the full structure as found in github,
 * Note that $basepath cannot be used in the autoloader, so must be declared again.
 * $basepath = '../ui';
 */
$basepath = '../ui';
# $basepath = '/srv/www/htdocs/eduerp';
spl_autoload_register(function ($class) {
	$basepath = '../ui';
    include $basepath.'/classes/' . $class . '.php';
});
include($basepath.'/globals.php');
$link = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
if ($link->connect_error) {
	$messagebar->addError($link->connect_error);
	echo $link->connect_error;
	unset($link);
}
Options::LoadSessionOptions($link);
$_SESSION['dbuserid'] = 1;

// Establish base inventory
function initialInventory($link) {
	$inv = new InventoryManager($link);
	$item = new ItemManager($link);
	// Provide Tilapia fry to all entities who will be farming fish.
	$pid = $item->apiSearch('FISHTAL1');
	$inv->physicalSet(1,$pid,560);
	$inv->physicalSet(5,$pid,560);
	$inv->physicalSet(8,$pid,1120);
	$inv->physicalSet(11,$pid,1120);
	$inv->physicalSet(12,$pid,560);
	$inv->physicalSet(13,$pid,5200);
	$inv->physicalSet(23,$pid,2600);
	$inv->physicalSet(34,$pid,1680);
	// Provide mature apple trees
	$pid = $item->apiSearch('TREE4126E');
	$inv->physicalSet(1,$pid,8);
	$inv->physicalSet(2,$pid,40);
	$inv->physicalSet(8,$pid,60);
	$inv->physicalSet(10,$pid,40);
	$inv->physicalSet(12,$pid,50);
	$inv->physicalSet(14,$pid,30);
	$inv->physicalSet(29,$pid,3);
	$pid = $item->apiSearch('TREE4017E');
	$inv->physicalSet(1,$pid,1);
	$inv->physicalSet(2,$pid,5);
	$inv->physicalSet(8,$pid,8);
	$inv->physicalSet(10,$pid,5);
	$inv->physicalSet(12,$pid,6);
	$inv->physicalSet(14,$pid,4);
	$inv->physicalSet(29,$pid,1);
	// Provide blueberry bushes
	$pid = $item->apiSearch('PLANT4240C');
	$inv->physicalSet(3,$pid,120);
	$inv->physicalSet(8,$pid,60);
	$inv->physicalSet(11,$pid,30);
	$inv->physicalSet(12,$pid,55);
	$inv->physicalSet(16,$pid,800);
	$inv->physicalSet(28,$pid,312);
	$inv->physicalSet(33,$pid,80);
	// Romain red lettuce
	$pid = $item->apiSearch('SEED3097');
	$inv->physicalSet(1,$pid,5000);
	$inv->physicalSet(5,$pid,5000);
	$inv->physicalSet(8,$pid,7500);
	$inv->physicalSet(11,$pid,10000);
	$inv->physicalSet(12,$pid,7500);
	$inv->physicalSet(13,$pid,50000);
	$inv->physicalSet(23,$pid,22500);
	$inv->physicalSet(34,$pid,12500);
	// Iceburg lettuce
	$pid = $item->apiSearch('SEED4061');
	$inv->physicalSet(1,$pid,7500);
	$inv->physicalSet(5,$pid,10000);
	$inv->physicalSet(8,$pid,15000);
	$inv->physicalSet(11,$pid,18000);
	$inv->physicalSet(12,$pid,17500);
	$inv->physicalSet(13,$pid,80000);
	$inv->physicalSet(23,$pid,42000);
	$inv->physicalSet(34,$pid,32000);
	// Other varieties of lettuce
	$pid = $item->apiSearch('SEED4076');
	$inv->physicalSet(1,$pid,2500);
	$inv->physicalSet(5,$pid,20000);
	$inv->physicalSet(8,$pid,10000);
	$inv->physicalSet(11,$pid,15000);
	$inv->physicalSet(12,$pid,7500);
	$inv->physicalSet(13,$pid,30000);
	$inv->physicalSet(23,$pid,6000);
	$inv->physicalSet(34,$pid,5000);
	// Spinach
	$pid = $item->apiSearch('SEED4749');
	$inv->physicalSet(1,$pid,1000);
	$inv->physicalSet(5,$pid,30000);
	$inv->physicalSet(8,$pid,4000);
	$inv->physicalSet(11,$pid,1500);
	$inv->physicalSet(12,$pid,7500);
	$inv->physicalSet(13,$pid,20000);
	$inv->physicalSet(23,$pid,1000);
	$inv->physicalSet(34,$pid,1000);
	
} // initialInventory()	
function createProductionRecord($link,$entity,$division,$department,$item,$bom,$qty,$start) {
	$_POST = array();
	$_POST['entity_id'] = $entity;
	$_POST['division_id'] = $division;
	$_POST['department_id'] = $department;
	$_POST['resulting_product_id'] = $item;
	$_POST['maximum_quantity'] = $qty;
	$_POST['prod_start_date'] = substr($start,0,10);
	$_POST['prod_start_time'] = substr($start,11);
	$_POST['bom_id'] = $bom;
	$_POST['rev_enabled'] = 'N';
	$_POST['rev_number'] = 1;
	$prod = new Production($link);
	$result =  $prod->insertHeader(true);
	return $result;
} // createProductionRecord()
function updateProductionDetails($link,$prodid,$fixedmax=true) {
	echo "\r\nUpdating details for prod_id $prodid.\r\n";
	$item = new ItemManager($link);
	$fish1 = $item->apiSearch('FISHTAL1');
	$fish2 = $item->apiSearch('FISHTAL2');
	$fish3 = $item->apiSearch('FISHTAL3');
	$fish4 = $item->apiSearch('FISHTAL4');
	$fish5 = $item->apiSearch('FISHTAL5');
	$fish_qty = 0;
	$fish_array = array($fish1,$fish2,$fish3,$fish4,$fish5);
	$q = 'SELECT * FROM prod_detail d JOIN bom_detail b ON d.bom_detail_id=b.bom_detail_id WHERE prod_id='.$prodid;
	$result = $link->query($q);
	$prod = new Production($link);
	$row = null;
	$finished_date = '';
	$finished_time = '';
	$genqty = array();
	if ($result!==false) {
		$row = $result->fetch_assoc();
		$prevPost = array();
		$prevRow = array();
		while ($row1 = $result->fetch_assoc()) {
			$launch = (new DateTime($row['step_started']))->getTimeStamp();
			$pct = 0.08;
			if (rand(0,1000)<2) $pct = 0.50;
			$_POST = array();
			$_POST['prod_id'] = $row['prod_id'];
			$_POST['prod_detail_id'] = $row['prod_detail_id'];
			$_POST['step_started_date'] = substr($row['step_started'],0,10);
			$_POST['step_started_time'] = substr($row['step_started'],11);
			$launch1 = (new DateTime($row1['step_started']))->getTimeStamp();
			//$interval = $launch1 - $launch;
			$interval = $row['seconds_to_process'];
			if (is_null($interval)) $interval = 0;
			$delta = rand()/getrandmax();
			$delta = 1.00 + $pct - ((2.00 * $pct) * $delta);
			$n = $launch + floor($delta * $interval);
			//$finished = (new DateTime($row['step_started']))->setTimeStamp($n);
			$finished = (new DateTime())->setTimeStamp($n);
			echo 'Finished: '.$finished->format('Y-m-d H:i:s')."\r\n";
			$_POST['step_finished_date'] = $finished->format('Y-m-d');
			$_POST['step_finished_time'] = $finished->format('H:i:s');
			$qdelta = rand()/getrandmax();
			if ($fixedmax) $qdelta = 1.00 - ($pct * $qdelta);
			else $qdelta = 1.00 + $pct - ((2.00 * $pct) * $qdelta);
			if ($row['planned_consumed']>0) {
				if ($row['planned_consumed']==floor($row['planned_consumed']))
					$_POST['quantity_consumed'] = floor(($row['planned_consumed']*$qdelta)+0.5);
				else
					$_POST['quantity_consumed'] = ($row['planned_consumed']*$qdelta);
				if (isset($prevRow['item_generated_id']) && $prevRow['item_generated_id']==$row['item_consumed_id'] && $_POST['quantity_consumed']>$prevPost['quantity_generated'])
					$_POST['quantity_consumed'] = $prevPost['quantity_generated'];
				if (in_array($row['item_consumed_id'],$fish_array)) {
					if ($fish_qty==0) $fish_qty = $_POST['quantity_consumed'];
					elseif ($_POST['quantity_consumed'] > $fish_qty) $_POST['quantity_consumed'] = $fish_qty;
					elseif ($fish_qty > $_POST['quantity_consumed']) $fish_qty = $_POST['quantity_consumed'];
				}
			}
			if ($row['planned_generated']>0) {
				if ($row['planned_generated']==floor($row['planned_generated']))
					$_POST['quantity_generated'] = floor(($row['planned_generated']*$qdelta)+0.5);
				else
					$_POST['quantity_generated'] = ($row['planned_generated']*$qdelta);
				$genqty[$row['item_generated_id']] = $_POST['quantity_generated'];
				if (in_array($row['item_generated_id'],$fish_array)) {
					if ($fish_qty==0) $fish_qty = $_POST['quantity_generated'];
					elseif ($_POST['quantity_generated'] > $fish_qty) $_POST['quantity_generated'] = $fish_qty;
					elseif ($fish_qty > $_POST['quantity_generated']) $fish_qty = $_POST['quantity_generated'];
				}
			}
			$updateresult = $prod->updateDetail();
			echo $updateresult."\r\n";
			$prevPost = $_POST;
			$prevRow = $row;
			$row = $row1;
			$row['step_started'] = $_POST['step_finished_date'].' '.$_POST['step_finished_time'];
		}
		$prevPost = $_POST;
		$_POST = array();
		$_POST['prod_id'] = $row['prod_id'];
		$_POST['prod_detail_id'] = $row['prod_detail_id'];
		$_POST['step_started_date'] = $prevPost['step_finished_date'];
		$_POST['step_started_time'] = $prevPost['step_finished_time'];
		$_POST['step_finished_date'] = $prevPost['step_finished_date'];
		$_POST['step_finished_time'] = $prevPost['step_finished_time'];
		$qdelta = rand()/getrandmax();
		if ($fixedmax) $qdelta = 1.00 - ($pct * $qdelta);
		else $qdelta = 1.00 + $pct - ((2.00 * $pct) * $qdelta);
		if ($row['planned_consumed']>0) {
			if ($row['planned_consumed']==floor($row['planned_consumed']))
				$_POST['quantity_consumed'] = floor(($row['planned_consumed']*$qdelta)+0.5);
			else
				$_POST['quantity_consumed'] = ($row['planned_consumed']*$qdelta);
			if (in_array($row['item_consumed_id'],$fish_array)) {
				if ($fish_qty==0) $fish_qty = $_POST['quantity_consumed'];
				elseif ($_POST['quantity_consumed'] > $fish_qty) $_POST['quantity_consumed'] = $fish_qty;
				elseif ($fish_qty > $_POST['quantity_consumed']) $fish_qty = $_POST['quantity_consumed'];
			}
		}
		if ($row['planned_generated']>0) {
			if ($row['planned_generated']==floor($row['planned_generated']))
				$_POST['quantity_generated'] = floor(($row['planned_generated']*$qdelta)+0.5);
			else
				$_POST['quantity_generated'] = ($row['planned_generated']*$qdelta);
			if (in_array($row['item_generated_id'],$fish_array)) {
				if ($fish_qty==0) $fish_qty = $_POST['quantity_generated'];
				elseif ($_POST['quantity_generated'] > $fish_qty) $_POST['quantity_generated'] = $fish_qty;
				elseif ($fish_qty > $_POST['quantity_generated']) $fish_qty = $_POST['quantity_generated'];
			}
		}
		$updateresult = $prod->updateDetail();
		echo $updateresult."\r\n";
		$finished_date = $_POST['step_finished_date'];
		$finished_time = $_POST['step_finished_time'];
	} else echo 'fail|'.$link->error;
	$_POST = array();
	$_POST['prod_id'] = $row['prod_id'];
	$_POST['prod_finished_date'] = $finished_date;
	$_POST['prod_finished_time'] = $finished_time;
	$result = $prod->updateHeader();
	echo $result."\r\n";
} // updateProductionDetails()
function createSalesOrder($link,$ent, $div, $cust,$pid,$ats,$orderdate, $uom, $price) {
	$_POST = array();
	$so = new SalesOrders($link);
	$_POST['level'] = 'header';
	$_POST['h1'] = 0; // sales order #
	$_POST['h4'] = 'I'; // Order status: invoiced
	$_POST['h5'] = $cust; // customer ID
	$_POST['h7'] = 1; // Seller ID = admin/root
	$_POST['h8'] = $ent; // entity
	$_POST['h9'] = $div; // division
	$_POST['h11'] = $ent; // inventory entity = entity
	$_POST['h12'] = 'USD'; // currency = U.S. dollars
	$_POST['h13'] = true; // visible = true
	$_POST['o1'] = 'FMKT'.$orderdate->format('Ymd').'A'; // customer PO #
	$_POST['o6'] = $orderdate->format('Y-m-d H:i:s'); // order date
	$_POST['o7'] = $_POST['o6']; // credit release date
	$_POST['o8'] = $orderdate->format('Y-m-d');	// start ship date
	$endship = new DateTime();
	$endship = $endship->setTimestamp($orderdate->getTimestamp() + (86400 * 2));
	$_POST['o9'] = $endship->format('Y-m-d');
	$mustarrive = new DateTime();
	$mustarrive = $mustarrive->setTimestamp($orderdate->getTimestamp() + (86400 * 4));
	$_POST['o11'] = $mustarrive->format('Y-m-d');
	$_POST['p2'] = $_POST['o6']; // wave date
	$_POST['s1'] = 4; // Shipper = local customer pickup
	$_POST['s6d'] = $orderdate->format('Y-m-d'); // Pickup date appointment
	$_POST['s6t'] = $orderdate->format('H:i:s');
	$_POST['s7d'] = $_POST['s6d']; // Inventory loaded date
	$_POST['s7t'] = $_POST['s6t'];
	$_POST['s9'] = $_POST['o6']; // order shipped date
	$_POST['i2'] = $_POST['i3'] = $_POST['o6']; // invoice and invoice paid date
	$rtn = $so->insertRecord(true);
	echo $rtn;
	if (strpos($rtn,'inserted')===0) $sonum = substr($rtn,9);
	else return;
	$hpost = $_POST;
	$_POST = array();
	$_POST['level'] = 'detail';
	$_POST['sales_order_number'] = $sonum;
	$_POST['sales_order_line'] = 1;
	$_POST['dentity_id'] = $ent;
	$_POST['ddivision_id'] = $div;
	$_POST['customer_line'] = 1;
	$_POST['item_id'] = $pid;
	$pct = rand()/getrandmax();
	if ($pct < 0.5) $pct *= 2;
	$qty = round($pct*$ats);
	$_POST['quantity_requested'] = $qty;
	$_POST['quantity_shipped'] = $qty;
	$_POST['quantity_uom'] = $uom;
	$_POST['price'] = $price;
	$_POST['dcredit_release_date'] = $hpost['o7'];
	$_POST['dwave_date'] = $hpost['p2'];
	$_POST['line_shipped_date'] = $hpost['s9'];
	$_POST['line_invoiced_date'] = $hpost['i2'];
	$_POST['dvisible'] = true;
	$rtn = $so->insertRecord(true);
	echo $rtn;	
} // createSalesOrder
function generateProductionHistory($link) {
	$inv = new InventoryManager($link);
	$item = new ItemManager($link);
	// Begin fish cycle
	$entities = array(1,5,8,11,12,13,23,34);
	$customers = array(7,11,14,17,18,19);
	$pid = $item->apiSearch('FISHTAL5');
	for ($ee=0;$ee<count($entities);$ee++) {
		$start = new DateTime('2015-01-05 12:00:00');
		$stop = new DateTime('2019-03-14 12:00:00');
		while ($start->getTimestamp() <= $stop->getTimestamp()) {
			$result = createProductionRecord($link,1,2,null,$pid,15,40,$start->format('Y-m-d H:i:s'));
			//if (strpos($result,'inserted|')!==false) 
			updateProductionDetails($link,substr($result,9),true);
			// purchase more fish food
			
			// purchase more fry
			
			// sell fish
			$ats = $inv->getEntityInventory($entities[$ee],$pid,'ATS');
			if (isset($customers[$ee])) {
				$orderdate = new DateTime();
				$orderdate = $orderdate->setTimestamp($start->getTimestamp()+(3600 * 24 * 7 * 35));
				$order = createSalesOrder($link,$entities[$ee],2,$customers[$ee],$pid,$ats,$orderdate, 'LBS',9.99);
			} // Only create sales for the appropriate customer
			// Advance timestamp
			$start_ts = $start->getTimestamp();
			$start_ts += (3600 * 24 * 7); // Advance activity by one week
			$start->setTimestamp($start_ts);
			break;
		} // while time keeps going
		break;
	} // for each entity
} // generateProductionHistory()
//initialInventory($link);
generateProductionHistory($link);
?>