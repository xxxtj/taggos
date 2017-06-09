<?php  
class UsersModel extends Model{
	  
    public static $tablename = "users";
     
	public static $fields = [ 

        "email" => [
             "require"            => "Please fill out email!",
             "regular_expression" => ["/^([a-z0-9]+([_\.\-]{1}[a-z0-9]+)*){1}([@]){1}([a-z0-9]+([_\-]{1}[a-z0-9]+)*)+(([\.]{1}[a-z]{2,6}){0,3}){1}$/i", "Email is incorrect!"],
             "uniq"               => "This email already exists!"
             
        ],

        "password" => [
             "require"            => "Please fill out password!", 
             "min"                => ["6", "Please enter at least 6 characters!"]
             
        ]
	];

    public static function login($array){
        if(isset($array)){ 
            $array['password'] = md5($array['password']);
            $db = new SafeMySQL();            
            $login = $db->getOne("SELECT id FROM users WHERE  ?e", $array); 
             if($login){
                self::authorize($login);
                return TRUE;
             }else{
                return FALSE;
             }
        }
    }





}