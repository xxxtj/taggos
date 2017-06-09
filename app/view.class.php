<?
 Class View{ 

    public function __construct(){

        static $page_schema = [
          "user/header",
          "user/sidebar",
          "user/navigation", 
          "user/footer" ]; 

 
    }

  public function load_view($template, $data){ 
      global $render;
      if(isset($data)){
        $render = $this->array_to_object($data);
      }
      $this->include_template_file('views/'.$template.'.php');
  }


  public function load_template($template_part, $data){ 
      global $render;
      if(isset($data)){
        $render = $this->array_to_object($data);
      }
      $this->build_page(array("user/header","user/sidebar", "user/menu", $template_part => "sub", "user/footer"));
       
  }
 

  public static function include_template ($template){ 
         $this->include_template_file('views/'.$template.'.php');
  }


  private function build_page($array){ 
  	foreach ($array as $key=>$template) {  
  		if(!is_numeric($key)){
	   	 		$this->include_template_file('views/'.$key.'.php');
	   	 	}else{
	   	 		$this->include_template_file('template/'.$template.'.php');
	   	 	}
  	}
  }

  private static function include_template_file($path){ 
    global $render;
  	if(file_exists($path)){	
      include_once($path);
		}else{
      die("Error Template");		
		}
  }

  private function array_to_object($array){ 
    $object = new stdClass();
    foreach ($array as $key => $value) {  
        if (is_array($value) || is_object($value)) { 
            $value = self::array_to_object($value); 
        }  
        $object->$key = $value; 
    }
    return $object;
  }
 
}