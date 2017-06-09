<?
 Class Activity{ 
 

  public function storeActivity(){ 
     $db = new SafeMySQL();
     $userID = $this->UserID();
     $data = array("date_time"      => date("Y-m-d H:i:s"),
                  "user_id"         => $userID,
                  "page_url"        => $this->getCurrentURL(),
                  "ip"              => $this->getIP(), 
                  "method"          => $this->getRequestMethod(),
                  "header_response" => $this->getHeaderCode(),
                  "header_info"     => $this->getBrowser(),
                  "referer_page"    => (empty($_SERVER['HTTP_REFERER'])) ? "direct" : $_SERVER['HTTP_REFERER']);
     $db->query("INSERT INTO `_activity` SET ?u", $data); 
  }
 
  private function UserID(){
    if(UserID()){
      return UserID();
    }
    elseif($this->ifCookieExists() &&  intval($this->getTemporaryUserID()) == 0  ){
      return $this->getTemporaryUserID();
    }
    else{   
      return $this->generateTemporaryUserID();
    }
  }

  private function ifCookieExists(){
    if(isset($_COOKIE['TUI'])){
      return true;
    }else{
      return false;
    }
  }
 

  private function getTemporaryUserID(){
    return $_COOKIE["TUI"];
  }

  private function generateTemporaryUserID(){
     $id = "u_".md5(_uniq());
     setcookie("TUI", $id);
     return $id;
  }

  private function getBrowser(){
    return $_SERVER['HTTP_USER_AGENT'];
  }
  private function getHeaderCode(){
    return http_response_code();
  }

  private function getRequestMethod(){
    return $_SERVER['REQUEST_METHOD'];
  }

  private function getIP(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
  }

  private function getCurrentURL(){
    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    return $url;
  }
 

  
 
}