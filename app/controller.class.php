<?
Class Controller{

    public $view;
    public $db;   
    public function __construct(){
        global $url;
        $this->url =& $url;
        $this->view = new View();  
        $this->db = new SafeMySQL();
        $this->files = new Files();
        $this->geo = new Geo();
    }

    public function clear_array($array){ 
          array_walk($array, array($this, 'clear'));
          return $array;  
    }

    private function clear(&$item){
    	    $item = stripslashes($item);
            $item = trim($item);
            $item = htmlspecialchars($item); 
            return $item;
    }
}