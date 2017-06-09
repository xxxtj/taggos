<?
// $time_start = microtime(true); 
session_start();
// $_SESSION['queries'] = 0;
ini_set('max_execution_time', 0);
ini_set('display_errors', 1);  

require_once 'app/func.inc.php'; 
// if(!preg_match('/www/', $_SERVER['HTTP_HOST']))
// {
//   redirect('https://www.'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
// }
require_once 'app/files.class.php';
require_once 'app/view.class.php';
require_once 'app/geo.class.php';
require_once 'app/db.class.php';
require_once 'app/routing.class.php';
require_once 'app/controller.class.php';
require_once 'app/model.class.php';
require_once 'app/handler.class.php';
require_once 'app/activity.class.php';
Model::loadModels(); 
// if(!isset($_SESSION['admin'])){
// 	$activity = new Activity();
// 	$activity->storeActivity();
// }
$init = new Routing;   
$init->init();
Handler::clearData(); 
 

// $time_end = microtime(true);  
// $execution_time = ($time_end - $time_start)/60;
// if($_SERVER['REQUEST_METHOD'] === "GET"){
// 	echo '<br><b style="    margin-left: 0px;">Total Execution Time:</b> '.(microtime(true) - $time_start).' sec<br>';
// 	echo 'DB queries: '.$_SESSION['queries'];
// }
 
 