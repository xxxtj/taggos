<? 
Class Model{ 

	public static function loadModels()
	{
		$models = scandir("models");
		foreach ($models as $model) {
			if (pathinfo($model, PATHINFO_EXTENSION) == "php") {
				require_once('models/' . $model);
			}
		}
	}
	
	public static function validate($data, $exclude_uniq=FALSE)
	{
		$errors = array();
		$inputs = array(); 
		$called_class = get_called_class();
		$tablename 	  = $called_class::$tablename;
		$fields 	  = $called_class::$fields;

		if (isset($data)) {
			foreach ($data as $key => $value) { //$key it is input field name
				 if(isset($fields[$key])){
				 	$validation = self::field_validation($value, $fields[$key], $exclude_uniq); 
				 	 if($validation !== TRUE && !empty($validation)){	
				 	 	$errors[$key] = $validation;
				 	 }
				 }
			$inputs[$key] = $value;	
			}
		}  
		
		Handler::create_input_values($inputs); 

		if (count($errors) > 0) { 
			Handler::create_handler($errors);   
		} else {
			return TRUE;
		}
	}

	public static  function validate_file($file, $rules){ 
		$ext = $rules['ext'];
		$upload_file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
		if(isset($rules['require']) && filesize($file['tmp_name']) == 0){
			return $rules['require'];
			break;
		}
 


		if(in_array($upload_file_ext, $ext)){

			if(isset($rules['size']) && (filesize($file['tmp_name']) > (int)$rules['size'])){
				$max_size = formatBytes($rules['size']); 
				return "Maximum file limit is $max_size";
				break;
			}

			if(isset($rules['resolution'])){
				$get_resolution = getimagesize($file["tmp_name"]);
				$image_width_property = $rules['resolution'][0];
				$image_height_property = $rules['resolution'][1];
				$action = $rules['resolution'][2];

				if( $action == "max" && ($get_resolution[0] > $image_width_property || $get_resolution[1] > $image_height_property) ){
					return "Image should be less than $image_width_property x $image_height_property px!";
					break;
				}elseif($action == "min" && ($get_resolution[0] < $image_width_property || $get_resolution[1] < $image_height_property)){
					return "Image should be min as $image_width_property x $image_height_property px!";	
					break;
				}elseif($action == "exactly" && ($get_resolution[0] !== $image_width_property || $get_resolution[1] !== $image_height_property)){
					return "Image should be $image_width_property x $image_height_property px!";	
					break;
				}else{
				}
				
			}
			return TRUE;
		}else{
			$error_extensions_list = implode(", ", $ext);
			return "We are support only $error_extensions_list";
		}




	}


	public static function field_validation($value, $rules, $exclude_uniq){

		if(isset($rules['require']) && empty($value)){
			return $rules['require'];
			break;
		} 

		if(isset($rules['uniq']) && !$exclude_uniq){
			$called_class = get_called_class();
			$tablename 	  = $called_class::$tablename; 
			$db        	  = new SafeMySQL();
			$check        = $db->getOne("SELECT COUNT(*) as `count` FROM `$tablename` WHERE `email` = ?s", $value);

				if ($check>0) { 
	  				return  $rules['uniq'][0];
	 				break;
		 		} 
		}

		if(isset($rules['regular_expression'])){
			if (!preg_match($rules['regular_expression'][0], trim($value))) {
  				return  $rules['regular_expression'][1];
 				break;
	 		} 
		}

		if(isset($rules['max'])){
			if (strlen($value) > $rules['max'][0]) {
  				return  $rules['max'][1];
 				break;
	 		} 
		}

		if(isset($rules['min'])){
			if (strlen($value) < $rules['min'][0]) {
  				return  $rules['min'][1];
 				break;
	 		} 
		}


		if(isset($rules['type']) && $rules['type'] == "file"){
			 	return self::validate_file($value, $rules); 
		}
	}


	public static function authorize($user_id){
		$_SESSION['user_id'] = $user_id;
	}


	
}
?>