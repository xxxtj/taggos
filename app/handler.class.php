<?

Class Handler{

    public static function create_handler($array){ 
        if(isset($array)){
            foreach ($array as $field => $error){
                $_SESSION['errors'][$field] = $error;
            }
        }
    }

    public static function create_message($m){
        if(isset($m)){
           $_SESSION['message'] = $m;
        }else{
           $_SESSION['message'] =  "";
        }
    }

    public static function messageExists(){
        if($_SESSION['message']){ 
            return true;
        }else{
           return false;
        }
    }

    public static function render_message(){
        if(isset($_SESSION['message'])){
            $render = $_SESSION['message'];
            unset($_SESSION['message']);
            return $render;  
        }else{
            $render ="";
        }
    }

    public static function render_success_message(){
        if(isset($_SESSION['message'])){
            $render = $_SESSION['message'];
            unset($_SESSION['message']);
            return "<button type='button' class='btn btn-success'>$render</button>";  
        }else{
            $render ="";
        }
    }

    public static function create_custom_handler($field, $echo){ 
        if(isset($field)){  
            $_SESSION['errors'][$field] = $echo;
        }
    }


 
    public static function render_errors(){
        if(isset($_SESSION['errors'])){
        	$render = $_SESSION['errors'];
            $html = "<ul>";
            foreach ($render  as $key=> $error) {
                $html .= "<li>";
                $html .=  $error;
                $html .= "</li>";
            }
            $html .= "</ul>";
            unset($_SESSION['errors']);
            return $html;  
        }else{
            $render ="";
        }
    }

    public static function json_render_errors(){
        if(isset($_SESSION['errors'])){
            $render = json_encode($_SESSION['errors']);
            return $render;  
        }else{
            $render ="";
        }
    }
    
    public static function render_error($name){
        if(isset($_SESSION['errors'])){
            $render = $_SESSION['errors']; 
                if(isset($render[$name]) && !empty($render[$name])){
                    unset($_SESSION['errors'][$name]);
                    return $render[$name]; 
                } 
                
        }  
    }

    public static function create_input_values($array){
       if(isset($array)){
            foreach ($array as $field => $error){
                $_SESSION['input_values'][$field] = $error;
            }
        }
    }   

    public static function create_custom_value($field, $value){
        if(isset($field)){
           $_SESSION['input_values'][$field] = $value;
        }
    }

    public static function render_input_value($name){
        if(isset($_SESSION['input_values'])){
            $render = $_SESSION['input_values']; 
                if(isset($render[$name]) && !empty($render[$name])){
                    unset($_SESSION['input_values'][$name]); 
                    return $render[$name];
                }

            
        }else{
            $render = "";
        }
    }

    public static function hasErrors(){
        if(empty($_SESSION['errors'])){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    public static function clearData(){
        unset($_SESSION['input_values']);
        unset($_SESSION['errors']);
    }

}