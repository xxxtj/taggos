<? 
Class Main extends Controller{

	public function index(){
		if(check_authorization()){
          $userLogged = TRUE;
       	}else{
       	  $userLogged = FALSE;
       	}
		$data = array('title' => 'Home page', 'userLogged' => $userLogged);
		$this->view->load_view('user/home', $data);
	}



    public function sign_up(){  
    	if(check_authorization()){
          redirect('/projects');
       	}
	    $data = array( 
	        'email'     => clean_data($_POST['email']),
	        'password'  => clean_data($_POST['password'])
	        ); 

	    if(usersModel::validate($data)) {
	      $data['password']          = md5($data['password']); 
	      $data['date_registration'] = date("Y-m-d H:i:s");
	      $data['last_ip']           = $_SERVER['REMOTE_ADDR'];
	 
	      $this->db->query("INSERT INTO users SET ?u", $data); 
	      $userID = $this->db->insertId();
	      usersModel::authorize($userID);
	      return_true(); 
	      $this->greeting_email($data['email']);
	    }else{  
	      return_false();
	    }
    }

	public function sign_in(){  
		if(check_authorization()){
          redirect('/projects');
       	}
		$data = array(
			'email'     => clean_data($_POST['email']),
			'password'  => clean_data($_POST['password'])
		);  
		if(usersModel::validate($data, TRUE) && usersModel::login($data)) {	  
			return_true();
		}else{  
			return_false();
		}
	}

	public function remind_password(){ 
		if(check_authorization()){
          redirect('/projects');
       	}
		$data = array(
			'email' => clean_data($_POST['email'])
		);  
		if(usersModel::validate($data, TRUE)) {	 
			$user  = $this->db->getRow("SELECT id FROM `users` WHERE email = ?s", $data['email']); 
			if($user){
				$new_password = _uniq().mt_rand(100,999);
				$this->db->query("UPDATE `users` SET password = ?s WHERE email = ?s AND id = ?i", md5($new_password), $data['email'], $user->id);
				$this->new_password_email($data['email'], $new_password);
				return_true();
			}else{
				return_false();
			}
		}else{  
			return_false();
		}
	}



	public function settings_get(){
		$this->checkIfUSerAuthorized(); 
      	$userID = UserID(); 
		$user  = $this->db->getRow("SELECT email FROM `users` WHERE id = ?s", $userID);
		$data = array('title' => 'Account settings', 'user' => $user);
		$this->view->load_template('user/settings', $data);
	}


	public function download_report(){
		$this->checkIfUSerAuthorized(); 
      	$userID = UserID(); 
		$projectID = $this->url->project_id;
		$project   = $this->db->getRow("SELECT * FROM `projects` WHERE user_id = ?i AND id = ?i", $userID, $projectID);
		if($project){
			$elements = $this->db->getAll("SELECT * FROM elements WHERE project_id = ?i", $projectID);  
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename='.strtolower($project->name).'_report.csv'); 

			$x=1; 
			$output = fopen('php://output', 'w');
			
            foreach ($elements as $element):  
            	$attr_array = array();
				$columns = array('Date', 'URL', 'IP', 'User Agent');
            	$attr = $this->db->getAll("SELECT * FROM attributes WHERE element_id = ?i ORDER BY id ASC", $element->id);
            	foreach ($attr as $a) : 
					$attr_array[] = $a->id;
					$columns[] = $a->attribute_memo;
				endforeach ;
				fputcsv($output, array(' '));
				fputcsv($output, array('==========================================================='));
				fputcsv($output, array($element->selector.' ('.$element->action.')')); 
				fputcsv($output, $columns);
				$activity = $this->db->getAll("SELECT action_group_name FROM  activity WHERE element_id = ?i GROUP by(action_group_name) ORDER BY date DESC",$element->id);
				foreach($activity as $act ):
					$data = array();
                	$val = $this->db->getAll("SELECT * FROM activity WHERE action_group_name = ?s AND element_id = ?i ORDER BY attribute_id ASC", $act->action_group_name, $element->id);  
                	$data[] = $val->{0}->date;
                	$data[] = $val->{0}->url;
                	$data[] = $val->{0}->ip;
                	$data[] = $val->{0}->header;
                    foreach ($val as $value) :
                       if(in_array($value->attribute_id, $attr_array)){
                       		$data[] = $value->data; 
                        } 
                    endforeach;
                    fputcsv($output, $data);
                endforeach;
        	endforeach;
		}else{
			redirect('/projects');
		}
	}

	public function settings_post(){
		$this->checkIfUSerAuthorized(); 
      	$userID = UserID(); 
      	if(isset($_POST['current_password']) && !empty($_POST['current_password'])  && isset($_POST['new_password']) && !empty($_POST['new_password'])){ 
      		$user   = $this->db->getRow("SELECT password FROM `users` WHERE id = ?s", $userID); 
			$new_password = clean_data($_POST['new_password']);
			$data   = array('password'  => $new_password); 
			if(md5(clean_data($_POST['current_password'])) == $user->password  && usersModel::validate($data)) { 
				$this->db->query("UPDATE `users` SET password = ?s WHERE id = ?i", md5($new_password), $userID);
				Handler::create_message("<span id='successMessage'>Your profile has been successfully updated!</span>");
			}else{
				Handler::create_message("<span id='error'>Hmm...looks like your current password is wrong!</span>");
			}
      	}else{
      		Handler::create_message("<span id='error'>You cannot submit empty form!</span>");
      	}
		redirect('/settings');	 
	}


	public function create_project_get(){ 
		$this->checkIfUSerAuthorized();
      	$userID    = UserID(); 
		$projectID = (int)clean_data($this->url->project_id);
		if(!isset($projectID) || $projectID == 0){
			$title = "Create new project";
			$project = "";
			$elements = "";
		}else{
			$title = "Edit project";
			$project  = $this->db->getRow("SELECT * FROM `projects` WHERE user_id = ?i AND id = ?i",$userID, $projectID);
			if(!$project){
				redirect('/projects');
			}
			$elements = $this->db->getAll("SELECT * FROM `elements` WHERE project_id = ?i ORDER BY id ASC", $project->id);
		}
		$data = array('title' => $title, 'project' => $project, 'elements' => $elements);
		$this->view->load_template('user/create_project', $data);
	} 

	public function projects(){  		
		$this->checkIfUSerAuthorized();
      	$userID    = UserID(); 
		$projects  = $this->db->getAll("SELECT * FROM `projects` WHERE  `user_id` = ?i ORDER BY id DESC", $userID);
		$data 	   = array('title' => "Projects", 'projects' => $projects);
		$this->view->load_template('user/projects', $data);
	} 


	public function tagging(){  
		header('Access-Control-Allow-Origin: *');	

		$projectCode = clean_data($_GET['c']);
		$selector    = clean_data($_GET['s']);  
		$project     = $this->db->getRow("SELECT id FROM `projects` WHERE codename = ?s",$projectCode);
		$element     = $this->db->getRow("SELECT id FROM `elements` WHERE project_id = ?i AND codename = ?s", $project->id, $selector);

		if($element){
			$action_group_name = md5(_uniq());
			if(isset($_POST) && !empty($_POST)){
				foreach ($_POST as $key => $value) {
					$attr     = $this->db->getRow("SELECT id FROM `attributes` WHERE attribute_code = ?s", $key);
					$data = array("date"         	  => date("Y-m-d H:i:s"),
								  "url"				  => (empty($_SERVER['HTTP_REFERER'])) ? "direct" : $_SERVER['HTTP_REFERER'],
								  "attribute_id" 	  => $attr->id,
								  "element_id" 	  	  => $element->id,
								  "action_group_name" => $action_group_name,
					              "ip"          	  => $this->getIP(),   
					              "header"       	  => $_SERVER['HTTP_USER_AGENT'],
					              "data"         	  => $value);
					$this->db->query("INSERT INTO `activity` SET ?u", $data); 
				}
			}else{ 
				$data = array("date"         	  => date("Y-m-d H:i:s"),
							  "url"				  => (empty($_SERVER['HTTP_REFERER'])) ? "direct" : $_SERVER['HTTP_REFERER'],
							  "attribute_id" 	  => 0,
							  "element_id" 	  	  => $element->id,
							  "action_group_name" => $action_group_name,
				              "ip"          	  => $this->getIP(),   
				              "header"       	  => $_SERVER['HTTP_USER_AGENT'],
				              "data"         	  => "");
				$this->db->query("INSERT INTO `activity` SET ?u", $data);
			}
			
		}
	}



	public function create_project(){  	
		$this->checkIfUSerAuthorized();
      	$userID    			 = UserID(); 	
		$actions 	  	     = array('click', 'onload', 'change');
		$project_name 	     = clean_data($_POST['project_name']);
		$projectID    	     = clean_data($_POST['project_id']); 
		$elementsNotToRemove = array();
		$attrNotToRemove 	 = array();
		$var_name 			 = 'v'.md5(time());
		$js 			     = 'var '.$var_name.'=jQuery;'; 
		if(isset($projectID) && $projectID > 0){
			Handler::create_message('Project "<b>'.$project_name. '</b>" has been successfully edited!');
			$project  = $this->db->getRow("SELECT * FROM `projects` WHERE user_id = ?i AND id = ?i",$userID, $projectID);
			if(!$project){
				 redirect('/projects');
			}
		}else{
			Handler::create_message('Project "<b>'.$project_name. '</b>" has been successfully created!');
		} 

		$elements 	  = @$_POST['elements']; 
		if(isset($elements) && isset($project_name) && !empty($project_name)){

			if(isset($project)){
				$projectCodeName = $project->codename;
				$projectID 		 = $project->id;
				$this->db->query("UPDATE `projects` SET name = ?s WHERE user_id = ?i AND id = ?i",$project_name, $userID, $projectID);
			}else{
				$projectCodeName = md5(md5(_uniq()));
				$projectData = array('codename' => $projectCodeName, 
									 'name'     => $project_name, 
									 'user_id'  => $userID, 
									 'date'     => date("Y-m-d H:i:s"));
				$this->db->query("INSERT INTO `projects` SET ?u", $projectData); 
	      		$projectID = $this->db->insertId();
			}
			
	      	

			foreach ($elements as $element_id => $element) { 
				  if( !isset($elements[$element_id]["selector"]) ||
				  	  empty($elements[$element_id]["selector"])  ||
				  	  !in_array($elements[$element_id]["action"], $actions) ){
				  	exit('/error'); 
				  } 

				  $selector 	 = $elements[$element_id]["selector"];
				  $action 		 = $elements[$element_id]["action"];
				  
				  $elementArray = explode("_", $element_id);
				  if(isset($elementArray[1]) && $elementArray[1] > 0){
				  	$elementsNotToRemove[] = $elementArray[1];
				  	$this->db->query("UPDATE `elements` SET selector = ?s, action = ?s WHERE id = ?i",$selector, $action, $elementArray[1]);
				  	$elementID = $elementArray[1];
				  }else{
				  	$elementData  = array('codename'   => md5(md5(_uniq())),
				  						  'project_id' => $projectID, 
									  	  'selector'   => $selector, 
									  	  'action'     => $action);
				  
				  	$this->db->query("INSERT INTO `elements` SET ?u", $elementData); 
			      	$elementID = $this->db->insertId();
			      	$elementsNotToRemove[] = $elementID;
				  }
				  $attributes = @$elements[$element_id]["attributes"]; 

				  if(isset($attributes)){ 
				  	foreach ($attributes as $attribute_id => $attribute) { 
				  				$attributeArray = explode("_", $attribute_id);  
				  				if(isset($attributeArray[1]) && $attributeArray[1] > 0){
				  					$attrNotToRemove[] = $attributeArray[1];
								  	$this->db->query("UPDATE `attributes` SET attribute_memo = ?s, 
												  		attribute_selector = ?s, 
												  		attribute_code = ?s 
												  			WHERE 
												  		element_id = ?i AND id = ?i", 
												  		str_replace(' ', '_',$attribute['attribute_memo']), 
												  		$attribute['attribute_selector'], 
												  		md5(_uniq().mt_rand(1000,9999)), 
												  		$elementID, 
												  		$attributeArray[1]);
								  	$elementID = $elementArray[1];
							    }else{
							  		$attributeData = array('project_id'         => $projectID,
				  									       'element_id' 		=> $elementID, 
				  									       'attribute_memo' 	=> str_replace(' ', '_', $attribute['attribute_memo']), 
				  									       'attribute_selector' => $attribute['attribute_selector'],
				  									       'attribute_code' 	=> md5(_uniq().mt_rand(1000,9999)));
		      						$this->db->query("INSERT INTO `attributes` SET ?u", $attributeData); 
		      						$attrNotToRemove[] = $this->db->insertId();
							  }
				  	}
				  }
			}
		} 




		if(count($elementsNotToRemove) > 0){
			$sql =  "AND id NOT IN(".implode(",",$elementsNotToRemove).')';
			$this->db->query("DELETE FROM elements WHERE project_id = ?i $sql", $projectID);
		} 
		if(count($attrNotToRemove) > 0){
			$sql =  "AND id NOT IN(".implode(",",$attrNotToRemove).')';
		}else{
			$sql = "";
		}
		$this->db->query("DELETE FROM attributes WHERE project_id = ?i $sql", $projectID);

		$elements = $this->db->getAll("SELECT * FROM `elements` WHERE project_id = ?i ", $projectID);
		foreach ($elements as $element) {
			$url = '//taggos.com/mZgXaZ4otCYq6WR0kizJVzw7zYufXo67bcV9ZNogfcwcWe26kqD5NZ1dMjgz?c='.$projectCodeName.'&s='.$element->codename;

			$attr = $this->db->getAll("SELECT * FROM `attributes` WHERE element_id = ?i", $element->id);
			if($attr){
				$attributes = array();
				foreach ($attr as $a) {
					$attributes[] = '"'.$a->attribute_code.'"'.':(typeof '.$a->attribute_selector.'==="undefined")?"null":'.$a->attribute_selector;
				}
			}	
			
			if($element->action == "click"){
				$js .= $this->jsClickAction($element->selector, $attributes, $url, $var_name);
			}elseif($element->action == "change"){
				$js .= $this->jsChangeAction($element->selector, $attributes, $url, $var_name);
			}elseif($element->action == "onload"){
				$js .= $this->jsOnloadAction($element->selector, $attributes, $url, $var_name);
			}
		} 

		file_put_contents("public/js/$projectCodeName.js", $js);
 
		
		redirect('/projects');	
	} 


	public function delete_project(){  		
		$this->checkIfUSerAuthorized();
      	$userID    = UserID(); 
		$projectID = $this->url->project_id;
        $check 	   = $this->db->getRow("SELECT * FROM projects WHERE user_id = ?i AND id = ?i", $userID, $projectID);
        if($check){
        	$this->db->query("DELETE FROM projects WHERE id = ?i", $projectID);
	        $this->db->query("DELETE FROM elements WHERE project_id = ?i", $projectID);
	        $this->db->query("DELETE FROM attributes WHERE project_id = ?i", $projectID); 
	        $projectCodeName = $check->codename;
	        unlink("public/js/$projectCodeName.js");       	
        }
        redirect('/projects');
	} 


	public function report(){ 
		$this->checkIfUSerAuthorized();
      	$userID    = UserID();   
		$projectID = $this->url->project_id;
		$project   = $this->db->getRow("SELECT * FROM `projects` WHERE user_id = ?i AND id = ?i", $userID, $projectID);
		if($project){
			$elements = $this->db->getAll("SELECT * FROM elements WHERE project_id = ?i", $projectID); 
        	$data     = array('title' => 'Report', 'project' => $project, 'elements' => $elements); 
		}else{
			redirect('/projects');
		}
        $this->view->load_template('user/report', $data);
	} 




	private function jsClickAction($selector, $attributes, $url, $jq_var){
		$js  = $jq_var.'("body").on("click","'.$selector.'",function(){'; 
		$js .= $this->ajaxCall($attributes, $url, $jq_var);
		$js .= '});';   
		return $js;
	}


	private function jsChangeAction($selector, $attributes, $url, $jq_var){
		$js  = $jq_var.'("body").on("change","'.$selector.'",function(){'; 
		$js .= $this->ajaxCall($attributes, $url, $jq_var);
		$js .= '});';   
		return $js;
	}

	private function jsOnloadAction($selector, $attributes, $url, $jq_var){
		$js  = $jq_var.'(document).ready(function(){'; 
		$js .= 'setTimeout(function(){'; 
		$js .= $this->ajaxCall($attributes, $url, $jq_var);
		$js .= '},1000);});';   
		return $js;
	}


	private function ajaxCall($attributes, $url, $jq_var){
		$js  = $jq_var.'.ajax({';
			if(count($attributes) > 0){
				$js .= "url:'$url',";
				$js .= "method:'POST',";
				$js .= "data:{";
				$js .= implode(",", $attributes);
				$js .= "}";
			} else{
				$js .= "url:'$url'";
			}
		$js .= "});";	
		return $js;   
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

	public function logout(){
       if(check_authorization()){
          logout();
       }
       redirect("/");
    }

    private function checkIfUSerAuthorized(){
       if(!check_authorization()){
          redirect("/");
       }
    }

    private function greeting_email($to){ 
		$subject = "Welcome to Taggos.com";
		$message = "Welcome to <b>Taggos.com</b><br>Thank you for creating a Taggos account. Now you have full access to it.<br><a href='https://www.taggos.com/'>Taggos.com</a>
		";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <noreply@taggos.com>' . "\r\n"; 

		mail($to,$subject,$message,$headers);
    }

    private function new_password_email($to, $new_password){ 
		$subject = "New password for Taggos.com";
		$message = "Your new password is $new_password <br><a href='https://www.taggos.com/'>Taggos.com</a>
		";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <noreply@taggos.com>' . "\r\n"; 

		mail($to,$subject,$message,$headers);
    }
}