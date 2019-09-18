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
	unset($link);
}
Options::LoadSessionOptions($link);
$_SESSION['dbuserid'] = 1;

// Establish base inventory
$inv = new InventoryManager($link);
$item = new ItemManager($link);
function initialInventory() {
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
function createProductionRecord($entity,$division,$department,$item,$bom,$qty,$start) {
	$_POST = array();
	$_POST['entity_id'] = $entity;
	$_POST['division_id'] = $division;
	$_POST['department_id'] = $department;
	$_POST['resulting_product_id'] = $item;
	$_POST['maximum_quantity'] = $qty;
	$_POST['prod_start_date'] = substring($start,0,10);
	$_POST['prod_start_time'] = substring($start,11);
	$_POST['bom_id'] = $bom;
	$_POST['rev_enabled'] = 'N';
	$_POST['rev_number'] = 1;
	$prod = new Production($link);
	$result =  $prod->insertHeader();
	return $result;
} // createProductionRecord()
function updateProductionDetails() {
	
} // updateProductionDetails()
function generateProductionHistory() {
	// Begin fish cycle
	$entities = array(1,5,8,11,12,13,23,34);
	$start = new DateTime('2015-01-05 12:00:00');
	$pid = $item->apiSearch('FISHTAL5');
	$result = createProductionRecord(1,2,null,$pid,15,40,$start->format('Y-m-d H:i:s'));
	
} // generateProductionHistory()
initialInventory();
generateProductionHistory();
?>