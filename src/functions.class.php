<?php 
/**
 * @package orglman/fundamental
 * @link    https://github.com/orglman/fundamental/
 * @author  Tobias Jonson <git@orgelman.systems>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (GNU GPL)
 */

namespace orgelman\fundamental\scripts {
   class functions {
      use \orgelman\fundamental\traits\bottrap;
      use \orgelman\fundamental\traits\externalLoadResource;
      use \orgelman\fundamental\traits\stringRandom;
      use \orgelman\fundamental\traits\stringSearch;
      use \orgelman\fundamental\traits\phones;
      
      protected $root = null;
      protected $subcat = null;
      protected $config = null;
      protected $scriptStart = null;

      public function __construct($root = null, $start = null, $config = null) {
         if($root != null) {
            $this->setRoot($root);
         } else {
            $this->setRoot(__DIR__);
         }
         if($start == null) {
            $start = microtime(true);
         }
         $this->scriptStart = $start;
         
         if($config != null) {
            $this->setConfig($config);
         }
         
         // check whats executing the code
         $this->client = new \stdClass;
         $this->client->cli = false;
         
         // check if command line
         if(php_sapi_name()=='cli') {
            $this->client->cli = true;
            $this->client->arguments = $this->parseArgv();
         }
      }
      public function timeElapsed($microtime = null) {
         if($microtime == null) {
            $microtime = $this->scriptStart;
         }
         return microtime(true) - $microtime.'s';
      }

      // Convert $argv to $_GET
      public function parseArgv() {
         if((isset($argv)) && ($argv!="")) {
            parse_str(implode('&', array_slice($argv, 1)), $_GET);
            array_push($_REQUIRE, $_GET);
            return $_GET;
         } else {
            return false;
         }
      }

      // Set config object
      public function setConfig($config) {
         if(is_object($config)) {
            return $this->config = $config;
         }
         return false;
      }
      
      // Get config object
      public function getConfig() {
         return $this->config;
      }

      // Set path to root
      public function setRoot($path) {
         $path = $this->cleanPath($path);
         if(is_dir($path)) {
            $this->root = $path;
            return $this->root;
         }
         return false;
      }
      public function getRoot() {
         return $this->root;
      }
      
      // Set path subcat
      public function setSubcat($path) {
         $this->subcat = $path;
         return $this->subcat;
      }
      public function getSubcat() {
         return $this->subcat;
      }
      
      // Clean up path
      public function cleanPath($path) {
         $path = preg_replace('#/+#', '/', $path);
         $path = preg_replace('#\\+#', '\\', $path);
         
         $path = str_replace(array('/','\\'), constant("DIRECTORY_SEPARATOR"), $path);
         
         $path = rtrim($path, constant("DIRECTORY_SEPARATOR")).constant("DIRECTORY_SEPARATOR");
         
         return $path;
      }

      // Clean up URL
      public function getURL($s = null, $f = null, $a = null, $use_forwarded_host = false ) {
         //scheme://username:password@domain:port/path?query_string#fragment_id
         $querystring = null;
         $urlstring = null;
         if((!is_string($s)) && ($this->client->cli)) {
            return false;
         }
         if((($s==null) || (is_array($s) || is_object($s))) && (isset($_SERVER))) {
            $s = $_SERVER;
         } elseif(is_string($s)) {
            $urlstring = $s;
         } 
         
         $authentication = "";
         if((is_array($a)) && ((isset($a["user"])) && (isset($a["pass"])))) {
            $authentication  .= $a["user"].':'.$a["pass"].'@';
         }
         
         $fragment = "";
         if((is_string($f)) && ($f!="")) {
            $fragment = '#'.$f;
         }
    
         if($urlstring!=null) {
            $parsed  = parse_url($urlstring);
            $sp      = "http";
            if(isset($parsed['scheme'])) {
               $sp   = strtolower( $parsed['scheme'] );
            }
            
            $ssl     = ( ( strtolower($sp) == "https" ) ? true : false );
            $protocol= $sp;
            
            $port = "";
            if(isset($parsed['port'])) {
               $port = $parsed['port'];
               $port = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
            }
            if(isset($parsed['host'])) {
               $host = $parsed['host'];
            }
            
            $querystring = '';
            $path = "/";
            if(isset($parsed['path'])) {
               $path = $parsed['path'];
               
               $querystring = $path;
            }
            
            if((isset($parsed['query'])) && ($parsed['query']!="")) {
               $querystring .= '?'.$parsed['query'];
            }
         } elseif(is_array($s)) {
            $ssl = 0;
            if(isset($s['HTTPS'])) {
               $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
            }
            
            $sp = "http";
            if(isset($s['SERVER_PROTOCOL'])) {
               $sp       = strtolower( $s['SERVER_PROTOCOL'] );
            }
            $protocol = "http";
            
            $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
            
            if(isset($s['SERVER_PORT'])) {
               $port     = $s['SERVER_PORT'];
               $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) || ( $ssl && $port=='80' ) ) ? '' : ':'.$port;
            }
            
            if((isset($s['HTTP_X_FORWARDED_HOST'])) && (isset($s['HTTP_HOST']))) {
               $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
            }
            
            if(isset($s['SERVER_NAME'])) {
               $host     = isset( $host ) ? $host : $s['SERVER_NAME'];
            }
            
            if(isset($s['REQUEST_URI'])) {
               $querystring = $s['REQUEST_URI'];
            }
         }
         if(!isset($host)) {
            return false;
         }
         
         $q = "";
         if($querystring!="") { 
            $arr = array();
            $values = array();
            
            $r = explode("?",$querystring,2);
            if(!substr($querystring, 0, 1) !== '?') {
               $path = $r[0];
               unset($r[0]);
            } 
            if(!empty($r)) {
               foreach(explode("?",$r[1]) as $str) {
                  foreach(explode("&",$str) as $s) {
                     $arr[] = $s;
                  }
               }
            }
            
            sort($arr);
            foreach($arr as $str) {
               $vals = explode('=',$str,2);
               
               $value = urlencode($vals[0]);
               if(isset($vals[1])) {
                  $value .= '='.urlencode($vals[1]);
               } else {
                  $_GET[$value] = true;
                  $value .= '='.'true';
               }
               $values[] = $value;
            }
            if(!empty($values)) {
               $q = '?'.implode('&',$values);
            }
         }
         
         $url      = $protocol . '://' . $authentication . $host . $port . $path . $q . $fragment;
         
         return array(
            "url" => $url,
            "root" => $this->getRoot(),
            "protocol" => $protocol,
            "authentication" => $authentication,
            "host" => $host,
            "port" => $port,
            "path" => $path,
            "subcat" => $this->getSubcat(),
            "alias" => trim($path,$this->getSubcat()),
            "query" => $q,
            "fragment" => $fragment,
            "get" => $_GET
         );
      }

      // Get client browser info
      // https://github.com/cbschuld/Browser.php/tree/master/lib
      public function get_client() {
         if (class_exists('Browser')) {
            $this->browser = new \Browser();

            $this->client->browser = $this->browser->getBrowser();
            $this->client->platform = $this->browser->getPlatform();
            return $this->browser;
         }
         return false;
      }

      // Get client User Agent
      public function get_client_ua() {
         $ua = 'UNKNOWN';
         if((isset($_SERVER['HTTP_USER_AGENT'])) && ($_SERVER['HTTP_USER_AGENT']!="")) {
            $ua = $_SERVER['HTTP_USER_AGENT'];
         }
         return $ua;
      }

      // Get client IP
      public function get_client_ip($ip="") {
         if($ip!="") {
            $ipaddress = $ip;
         } else {
            $ipaddress = '';
            if (getenv('HTTP_CLIENT_IP'))
               $ipaddress = getenv('HTTP_CLIENT_IP');
            else if(getenv('HTTP_X_FORWARDED_FOR'))
               $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
            else if(getenv('HTTP_X_FORWARDED'))
               $ipaddress = getenv('HTTP_X_FORWARDED');
            else if(getenv('HTTP_FORWARDED_FOR'))
               $ipaddress = getenv('HTTP_FORWARDED_FOR');
            else if(getenv('HTTP_FORWARDED'))
               $ipaddress = getenv('HTTP_FORWARDED');
            else if(getenv('REMOTE_ADDR'))
               $ipaddress = getenv('REMOTE_ADDR');
            else
               $ipaddress = 'UNKNOWN';
         }
         if (!filter_var($ipaddress, FILTER_VALIDATE_IP) === false) {
         } else {
            $ipaddress = 'UNKNOWN';
         }

         return $ipaddress;
      }

      // Clean string URL
      public function toAscii($str, $replace=array(), $delimiter='-') {
         if( !empty($replace) ) {
            $str = str_replace((array)$replace, ' ', $str);
         }

         $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
         $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
         $clean = strtolower(trim($clean, '-'));
         $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

         return $clean;
      }

      //If internet access
      public function is_connected() {
         $connected = @fsockopen("www.example.com", 80); 
         if ($connected){
            $is_conn = true; 
            fclose($connected);
         } else {
            $is_conn = false;
         }
         return $is_conn;
      }

      //IF remote file exists
      //if(remoteFileExists("https://example.com/file.zip")) {
      public function remoteFileExists($url) {
         $curl = curl_init($url);
         curl_setopt($curl, CURLOPT_NOBODY, true);
         $result = curl_exec($curl);
         $ret = false;
         if ($result !== false) {
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
            if ($statusCode == 200) {
               $ret = true;   
            }
         }
         curl_close($curl);
         return $ret;
      }

      // Obfuscate email
      public function obfuscate_email($email) {
         $em   = explode("@",$email);
         $name = implode(array_slice($em, 0, count($em)-1), '@');
         $len  = floor(strlen($name)/2);

         return substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);
      }

      // Get directory size in byte
      public function folderSize($dir) {
         $count_size = 0;
         $count = 0;
         $dir_array = scandir($dir);
         foreach($dir_array as $key=>$filename){
            if($filename!=".." && $filename!="."){
               if(is_dir($dir."/".$filename)){
                  $new_foldersize = foldersize($dir."/".$filename);
                  $count_size = $count_size+ $new_foldersize;
               } else if(is_file($dir."/".$filename)) {
                  $count_size = $count_size + filesize($dir."/".$filename);
                  $count++;
               }
            }
         }
         return $count_size;
      }

      // Format bytes
      public function formatBytes($bytes, $precision = 2) {
         $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
         $bytes = max($bytes, 0); 
         $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
         $pow = min($pow, count($units) - 1); 

         $bytes /= pow(1024, $pow);

         return round($bytes, $precision) . ' ' . $units[$pow]; 
      } 

      // Format numbers
      public function formatNumbers($numbers, $precision = 1) { 
         $units = array('', 'K', 'M'); 
         if($numbers>999) {
            $numbers = max($numbers, 0); 
            $pow = floor(($numbers ? log($numbers) : 0) / log(1000)); 
            $pow = min($pow, count($units) - 1); 

            $numbers /= pow(1000, $pow);

            return round($numbers, $precision) . ' ' . $units[$pow];
         } else {
            return $numbers;
         }
      } 

      // Invers RBG
      public function color_inverse($color){
         $color = str_replace('#', '', $color);
         if (strlen($color) != 6){ 
            return '000000'; 
         }
         $rgb = '';
         for ($x=0;$x<3;$x++){
            $c = 255 - hexdec(substr($color,(2*$x),2));
            $c = ($c < 0) ? 0 : dechex($c);
            $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
         }
         return strtoupper('#'.$rgb);
      }

      // Adjust color
      function adjustBrightness($hex, $steps) {
         // Steps should be between -255 and 255. Negative = darker, positive = lighter
         $steps = max(-255, min(255, $steps));

         // Normalize into a six character long hex string
         $hex = str_replace('#', '', $hex);
         if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
         }

         // Split into three parts: R, G and B
         $color_parts = str_split($hex, 2);
         $return = '#';

         foreach ($color_parts as $color) {
            $color   = hexdec($color); // Convert to decimal
            $color   = max(0,min(255,$color + $steps)); // Adjust color
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
         }

         return strtoupper($return);
      }
      public function getContrastColor($hexColor) {
           //////////// hexColor RGB
           $R1 = hexdec(substr($hexColor, 1, 2));
           $G1 = hexdec(substr($hexColor, 3, 2));
           $B1 = hexdec(substr($hexColor, 5, 2));

           //////////// Black RGB
           $blackColor = "#000000";
           $R2BlackColor = hexdec(substr($blackColor, 1, 2));
           $G2BlackColor = hexdec(substr($blackColor, 3, 2));
           $B2BlackColor = hexdec(substr($blackColor, 5, 2));

            //////////// Calc contrast ratio
            $L1 = 0.2126 * pow($R1 / 255, 2.2) +
                  0.7152 * pow($G1 / 255, 2.2) +
                  0.0722 * pow($B1 / 255, 2.2);

           $L2 = 0.2126 * pow($R2BlackColor / 255, 2.2) +
                 0.7152 * pow($G2BlackColor / 255, 2.2) +
                 0.0722 * pow($B2BlackColor / 255, 2.2);

           $contrastRatio = 0;
           if ($L1 > $L2) {
               $contrastRatio = (int)(($L1 + 0.05) / ($L2 + 0.05));
           } else {
               $contrastRatio = (int)(($L2 + 0.05) / ($L1 + 0.05));
           }

           //////////// If contrast is more than 5, return black color
           if ($contrastRatio > 5) {
               return 'black';
           } else { //////////// if not, return white color.
               return 'white';
           }
      }
      function hex2rgba($color, $opacity = false) {
         $default = 'rgb(0,0,0)';

         //Return default if no color provided
         if(empty($color))
                return $default; 

         //Sanitize $color if "#" is provided 
              if ($color[0] == '#' ) {
               $color = substr( $color, 1 );
              }

              //Check if color has 6 or 3 characters and get values
              if (strlen($color) == 6) {
                      $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
              } elseif ( strlen( $color ) == 3 ) {
                      $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
              } else {
                      return $default;
              }

              //Convert hexadec to rgb
              $rgb =  array_map('hexdec', $hex);

              //Check if opacity is set(rgba or rgb)
              if($opacity){
               if(abs($opacity) > 1)
                  $opacity = 1.0;
               $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
              } else {
               $output = 'rgb('.implode(",",$rgb).')';
              }

              //Return rgb(a) color string
              return $output;
      }


      
      
      public function luhnCreate($s) {
        // Add a zero check digit
        $s = $s . '0';
        $sum = 0;
        // Find the last character
        $i = strlen($s);
        $odd_length = $i % 2;
        // Iterate all digits backwards
        while ($i-- > 0) {
            // Add the current digit
            $sum+=$s[$i];
            // If the digit is even, add it again. Adjust for digits 10+ by subtracting 9.
            ($odd_length == ($i % 2)) ? ($s[$i] > 4) ? ($sum+=($s[$i] - 9)) : ($sum+=$s[$i]) : false;
        }
        return (10 - ($sum % 10)) % 10;
      }
      public function luhnValidate($number) {
        $sum = 0;
        $numDigits = strlen($number) - 1;
        $parity = $numDigits % 2;
        for ($i = $numDigits; $i >= 0; $i--) {
            $digit = substr($number, $i, 1);
            if (!$parity == ($i % 2)) {
                $digit <<= 1;
            }
            $digit = ($digit > 9) ? ($digit - 9) : $digit;
            $sum += $digit;
        }
        return (0 == ($sum % 10));
      } 
   }
}
