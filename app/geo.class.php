<?
Class Geo{

    public function __construct(){  
        $this->db = new SafeMySQL();  
    } 

    public static $states = array( 'AL'=>'Alabama',
                            'AK'=>'Alaska',
                            'AZ'=>'Arizona',
                            'AR'=>'Arkansas',
                            'CA'=>'California',
                            'CO'=>'Colorado',
                            'CT'=>'Connecticut',
                            'DE'=>'Delaware',
                            'DC'=>'District of Columbia',
                            'FL'=>'Florida',
                            'GA'=>'Georgia',
                            'HI'=>'Hawaii',
                            'ID'=>'Idaho',
                            'IL'=>'Illinois',
                            'IN'=>'Indiana',
                            'IA'=>'Iowa',
                            'KS'=>'Kansas',
                            'KY'=>'Kentucky',
                            'LA'=>'Louisiana',
                            'ME'=>'Maine',
                            'MD'=>'Maryland',
                            'MA'=>'Massachusetts',
                            'MI'=>'Michigan',
                            'MN'=>'Minnesota',
                            'MS'=>'Mississippi',
                            'MO'=>'Missouri',
                            'MT'=>'Montana',
                            'NE'=>'Nebraska',
                            'NV'=>'Nevada',
                            'NH'=>'New Hampshire',
                            'NJ'=>'New Jersey',
                            'NM'=>'New Mexico',
                            'NY'=>'New York',
                            'NC'=>'North Carolina',
                            'ND'=>'North Dakota',
                            'OH'=>'Ohio',
                            'OK'=>'Oklahoma',
                            'OR'=>'Oregon',
                            'PA'=>'Pennsylvania',
                            'RI'=>'Rhode Island',
                            'SC'=>'South Carolina',
                            'SD'=>'South Dakota',
                            'TN'=>'Tennessee',
                            'TX'=>'Texas',
                            'UT'=>'Utah',
                            'VT'=>'Vermont',
                            'VA'=>'Virginia',
                            'WA'=>'Washington',
                            'WV'=>'West Virginia',
                            'WI'=>'Wisconsin',
                            'WY'=>'Wyoming');

    public function getStates(){
        return self::$states;
    }

    public function validateZipcode($zipcode){
        $zipcode = $this->db->getOne("SELECT * FROM `__geo` WHERE zipcode = ?s", $zipcode);
        if ($zipcode) {
            return TRUE;
        }else{
            Handler::create_custom_handler("zipcode","Wrong zipcode!"); 
            return FALSE;
        }
    }

    public function locationExistsByZipcode($zipcode){  
        $zipcode = $this->db->getOne("SELECT * FROM `__geo` WHERE zipcode = ?s", $zipcode);
        if ($zipcode) {
            return TRUE;
        }else{ 
            return FALSE;
        }
    }

    public function SearchLocationsByName($city){   
        $locations = $this->db->getAll("SELECT * FROM `__geo` WHERE city ?l GROUP BY city,state ORDER BY priority DESC, city ASC, state ASC LIMIT 0, 10", $city   );
        $json = array();
        $i = 0;
        foreach ($locations as $location) { 
            $json_entry = array();
            $json_entry['id'] = mt_rand(100,9999);
            $json_entry['label'] = $location->city.', '.$location->state;
            $json_entry['value'] = $location->city.', '.$location->state;
            $json[]= $json_entry;
        }
        return json_encode($json);
    }

     public function SearchLocationsByZipcode($zipcode){   
        $location = $this->db->getRow("SELECT * FROM `__geo` WHERE zipcode = ?s", $zipcode   );
        $json_entry = array(); 
        $json_entry['id'] = mt_rand(100,9999);
        $json_entry['label'] = $location->city.', '.$location->state;
        $json_entry['value'] = $location->city.', '.$location->state; 
        return json_encode(array($json_entry));
    }

    public function getLocation($city, $state){   
        $location = $this->db->getRow("SELECT * FROM `__geo` WHERE city = ?s AND state = ?s", $city, $state);
        if($location){
            return $location;
        }else{
            return false;
        }
        
    }

    public function getLocationByName($city){
        $location = $this->db->getRow("SELECT * FROM `__geo` WHERE city = ?s GROUP BY city,state ORDER BY priority DESC, city ASC, state ASC LIMIT 0, 1", $city); 
            return $location;
    }


    public function getLocationArrayByZipcode($zipcode){
        $location = $this->db->getRow("SELECT * FROM `__geo` WHERE zipcode = ?s", $zipcode);
        if ($location) {
            return $location;
        }
    }

    public function fullLocationByZipcode($zipcode){
        $location = $this->db->getRow("SELECT * FROM `__geo` WHERE zipcode = ?s", $zipcode);
        if ($location) {
            return $location->city . ', ' . $location->state;
        }
    }

    public function stateByZipcode($zipcode){
        $location = $this->db->getRow("SELECT state FROM `__geo` WHERE zipcode = ?s", $zipcode);
        if ($location) {
            return $location->state;
        }
    }

    public function cityByZipcode($zipcode){
        $location = $this->db->getRow("SELECT city FROM `__geo` WHERE zipcode = ?s", $zipcode);
        if ($location) {
            return $location->city;
        }
    }

    public function GetCenterFromDegrees($data){
    if (!is_array($data)) return FALSE;

    $num_coords = count($data);

    $X = 0.0;
    $Y = 0.0;
    $Z = 0.0;

    foreach ($data as $coord)
    {
        $lat = $coord[0] * pi() / 180;
        $lon = $coord[1] * pi() / 180;

        $a = cos($lat) * cos($lon);
        $b = cos($lat) * sin($lon);
        $c = sin($lat);

        $X += $a;
        $Y += $b;
        $Z += $c;
    }

    $X /= $num_coords;
    $Y /= $num_coords;
    $Z /= $num_coords;

    $lon = atan2($Y, $X);
    $hyp = sqrt($X * $X + $Y * $Y);
    $lat = atan2($Z, $hyp);

    return array($lat * 180 / pi(), $lon * 180 / pi());
}
 


    public function ZipcodesWithinRadius($zipcode, $d){ 
        $getZipcodeInfo =$this->db->getRow("SELECT * FROM `__geo` WHERE zipcode = ?s", $zipcode);
        $lat1 = $getZipcodeInfo->lat;
        $lon1 = $getZipcodeInfo->lng;
        $r = 3959; 
        if($getZipcodeInfo){
            $latN = rad2deg(asin(sin(deg2rad($lat1)) * cos($d / $r) + cos(deg2rad($lat1)) * sin($d / $r) * cos(deg2rad(0))));
            $latS = rad2deg(asin(sin(deg2rad($lat1)) * cos($d / $r) + cos(deg2rad($lat1)) * sin($d / $r) * cos(deg2rad(180))));
            $lonE = rad2deg(deg2rad($lon1) + atan2(sin(deg2rad(90)) * sin($d / $r) * cos(deg2rad($lat1)), cos($d / $r) - sin(deg2rad($lat1)) * sin(deg2rad($latN))));
            $lonW = rad2deg(deg2rad($lon1) + atan2(sin(deg2rad(270)) * sin($d / $r) * cos(deg2rad($lat1)), cos($d / $r) - sin(deg2rad($lat1)) * sin(deg2rad($latN))));
            $zipcodes = $this->db->getAll("SELECT * FROM `__geo` WHERE (lat <= $latN AND lat >= $latS AND lng <= $lonE AND lng >= $lonW)");
            //$array = array();
            foreach ($zipcodes as $number => $zipcode) {
                $zipcode->distance = round(acos(sin(deg2rad($lat1)) * sin(deg2rad($zipcode->lat)) + cos(deg2rad($lat1)) * cos(deg2rad($zipcode->lat)) * cos(deg2rad($zipcode->lng) - deg2rad($lon1))) * $r, 2);
                //$array[]= (array)$zipcode;
            } 

           // usort($array, function ($a, $b) { return strnatcmp($a['distance'], $b['distance']); });

            //return (object)$array;
            return $zipcodes;
        }
    }
 
}