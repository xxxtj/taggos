<?
function redirect($url)
{
  header("Location:" . $url);
  exit();
} 

function _uniq()
  {
    $time = microtime();
    $int  = substr($time, 11);
    $flo  = substr($time, 2, 5);
    return $int . $flo;
  }

function howOldUser($date){
  return floor((time()-strtotime($date))/(60*60*24*365.25));
}
function d($array)
  {
     echo '<pre>';
  	 print_r($array);
     echo '</pre>'; 
  }
function publish_date($date){
  return  date('F d, Y  \a\t g:ia', strtotime($date));
}
function how_many_items($data)
  { 
    if(is_object($data)){
      return count((array)$data);
    }else{
       return count($data);
    }
    
  }  

function findImageFromHtml($html){
  preg_match_all('~<img.*?src=["\']+(.*?)["\']+~', $html, $urls);
  return $urls[1][0];
}


function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
    // $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

function return_true(){
  header('Content-Type: application/json');
  echo json_encode(array(
    'success' => TRUE,
  ));
}

function return_false(){
  header('Content-Type: application/json');
  echo json_encode(array(
    'success' => FALSE,
  ));
}

function clean_data($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

  function check_authorization(){
    if (isset($_SESSION['user_id'])){
      return TRUE;
    }else{
      return FALSE;
    }
  }

  function logout(){
    if (isset($_SESSION['user_id'])){
      unset($_SESSION['user_id']);
      return TRUE;
    } 
  }

  function UserID(){
    if (isset($_SESSION['user_id'])){
      return  $_SESSION['user_id']; 
    } 
  }

function HowManyDays ($time){

$cdate =  strtotime($time);
$today = time();
if($cdate - $today > 0){
 $difference =  $cdate - $today  ;

} else{
  $difference =  $today - $cdate  ;
}
if ($difference < 0) { $difference = 0; }
return floor($difference/60/60/24);

}

function check_cc($cc, $extra_check = false){
    $cards = array(
        "visa" => "(4\d{12}(?:\d{3})?)",
        "amex" => "(3[47]\d{13})",
        "jcb" => "(35[2-8][89]\d\d\d{10})",
        "maestro" => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)",
        "solo" => "((?:6334|6767)\d{12}(?:\d\d)?\d?)",
        "mastercard" => "(5[1-5]\d{14})",
        "switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)",
    );
    $names = array("Visa", "American Express", "JCB", "Maestro", "Solo", "Mastercard", "Switch");
    $matches = array();
    $pattern = "#^(?:".implode("|", $cards).")$#";
    $result = preg_match($pattern, str_replace(" ", "", $cc), $matches);
     
    return ($result>0)?$names[sizeof($matches)-2]:false;
}

function HowLongOnline ($time){

    $time = time() - $time; // to get the time since that moment
    $time = ($time<1)? 1 : $time;
    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }

}