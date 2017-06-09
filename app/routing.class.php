<? 
global 	$routes;
$routes = [
			'routes'=>
			[
 				[
				 'request'    => 'POST',
				 'pattern'    => '/create_project', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'create_project'
				],

				[
				 'request'    => 'POST',
				 'pattern'    => '/sign_up', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'sign_up'
				],

				[
				 'request'    => 'POST',
				 'pattern'    => '/sign_in', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'sign_in'
				],

				[
				 'request'    => 'POST',
				 'pattern'    => '/remind_password', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'remind_password'
				],
 

				[
				 'request'    => 'GET',
				 'pattern'    => '/logout', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'logout'
				],

				[
				 'request'    => 'GET',
				 'pattern'    => '/settings', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'settings_get'
				],

				[
				 'request'    => 'POST',
				 'pattern'    => '/settings', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'settings_post'
				],

				[
				 'request'    => 'GET',
				 'pattern'    => '/project{project_id}', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'create_project_get'
				],
				[
				 'request'    => 'GET',
				 'pattern'    => '/download_report{project_id}', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'download_report'
				],

				[
				 'request'    => 'GET',
				 'pattern'    => '/report{project_id}', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'report'
				],

				[
				 'request'    => 'GET',
				 'pattern'    => '/mZgXaZ4otCYq6WR0kizJVzw7zYufXo67bcV9ZNogfcwcWe26kqD5NZ1dMjgz', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'tagging'
				],
				[
				 'request'    => 'POST',
				 'pattern'    => '/mZgXaZ4otCYq6WR0kizJVzw7zYufXo67bcV9ZNogfcwcWe26kqD5NZ1dMjgz', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'tagging'
				],

				[
				 'request'    => 'GET',
				 'pattern'    => '/delete_project{project_id}', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'delete_project'
				],

				[
				 'request'    => 'GET',
				 'pattern'    => '/projects', //id, slug, date
				 'controller' => 'main',
				 'method'     => 'projects'
				],

				

			]
		];


Class Routing{ 

	public function init() {
	   $currentURL = parse_url($_SERVER['REQUEST_URI'])['path'];
	   $urlParse   = $this->parseURL($currentURL);
	   $analyzeURL = $this->analyze_url_pattern($urlParse);  

  	   if($analyzeURL){
  	   		$controllerName = $analyzeURL['controller'];
  	   		$methodName     = $analyzeURL['method'];
  	   }elseif($currentURL == "/404"){
  	   		$controllerName = "main";
  	   		$methodName     = "error404";
  	   }else{
			if(!empty($urlParse[1])){
		   		$controllerName = $urlParse[1]; 
		   }else{
		   		$controllerName = "main";
		   }
		
		   if(!empty($urlParse[2])){
		   		$methodName = $urlParse[2];
		   }else{
		   		$methodName = "index";
		   }
  	   }	

 
	   if(file_exists('controllers/'.$controllerName.'Controller.php')){
	   		require_once ('controllers/'.$controllerName.'Controller.php'); 
	   }else{
	   		redirect('/404');
	   } 

	   	$controller = new $controllerName; 
		if (method_exists($controller, $methodName)){	     
			call_user_func(array($controller, $methodName), $urlParse);
		}else{
			redirect('/404');
		}
		global $url;
 	} 



	private function analyze_url_pattern($url){ 
		global 	$routes; 
		$base = array('admin','blog');
		foreach ($routes['routes'] as $route) { 
			if(in_array($url[1], $base) && isset($route['slug']) && $route['slug'] === TRUE){ 
				$slug = $url[count($url)-1];
				array_pop($url);
				$url[] = '';  
			}

			if(count($url) == $this->HowManySlashesInURL($route['pattern']) 
				&& $this->clean(implode("/",$url)) == $this->clean($route['pattern'])
				&& $_SERVER['REQUEST_METHOD'] === $route['request']){  
					preg_match_all('/[{](.*)[}]/siU', $route['pattern'], $patterns); 
				 	$ofset = 2; 
				 	if($this->HowManySlashesInURL($route['pattern'])==2){
				 		$ofset = 1;
				 	} 
				 	$url_list = array(); 
					foreach ($patterns[1] as $key => $variable_name) {
						if(strpos($variable_name, '_id') !== false) {
    					 	$url_list[$variable_name] = $this->getID($url[$key+$ofset]);
    					}elseif(strpos($variable_name, '_slug') !== false) { 
    					 	$url_list[$variable_name] = $slug; 
    					}else{
							$url_list[$variable_name] = $url[$key+$ofset];
    					}
						 
					}  
					global $url;
					$executed = array('controller' => $route['controller'], 'method' => $route['method']);
					$url_list  = array_merge($url_list, $executed);
					$url = (object)$url_list;
					return $executed;
					break;
				} 
		}
	}


	private function HowManySlashesInURL($url){
		$urlParse = explode('/', $url);
		return count($urlParse);
	}

	private function parseURL($url){
		$urlParse = explode('/', $url);
		return  $urlParse;
	}

	private function getID($string){
		preg_match ('!\d+!', $string, $matches); 
		if(isset($matches[0]) &&  $matches[0] >= 0){
			return $matches[0];
		} 
	} 
 

	private function clean($url){
		$url = preg_replace('/[{].*[}]/U', '', $url);
		return preg_replace('/[0-9]+/', '', $url);
	} 

}