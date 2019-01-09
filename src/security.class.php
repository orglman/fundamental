<?php 
/**
 * @package orglman/fundamental
 * @link    https://github.com/orglman/fundamental/
 * @author  Tobias Jonson <git@orgelman.systems>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (GNU GPL)
 */
namespace orgelman\security {
   class encrypt {
      use \orgelman\functions\traits\stringHTMLEntities;
      use \orgelman\functions\traits\stringConvert;
      use \orgelman\functions\traits\stringRandom;
      use \orgelman\functions\traits\stringSearch;
      
      private $cipher_algorithm     = '';
      private $cipher_method        = 'AES-256-CBC';
      
      private $delimiter_characters = '==00==';
      private $delimiter_character  = '2';
      private $delimiter_breaks     = '3';
      private $delimiter_gz         = '4';
      
      private $compress             = true;
      
      public function __construct($compress = true, $cipher_algorithm = 'sha256') {
         if(!in_array($cipher_algorithm, hash_algos())) {
            trigger_error('Hashing algorithm invalid', E_USER_ERROR); 
            exit();
         }
         $this->delimiter_characters = $this->textToBinary($this->delimiter_characters, null);
         
         $this->cipher_algorithm = $cipher_algorithm;
         
         if(((function_exists('gzdecode')) && (function_exists('gzencode'))) && ($compress == true)) {
            $this->compress = true;
         } else {
            $this->compress = false;
         }
      }
      public function __destruct() {
         
      }
      
      public function setCompress($bool) {
         if(is_bool($bool)) {
            $this->compress = $bool;
         }
         $this->compress = false;
      }
      
      
      public function encrypt($str, $key, $method = '') {
         if($method == '') {
            $method = $this->cipher_method;
         }
         $return = array('decrypted' => addslashes(trim($str)), 'encrypted' => '', 'key' => $key, 'safekey' => '', 'algorithm' => $this->cipher_algorithm, 'method' => $method, 'iv' => '', 'base64_encode' => array(), 'error' => array(), 'decryptedlen' => strlen(addslashes(trim($str))), 'encryptedlen' => 0, 'compressedlen' => 0);

         if($str == '') {
            $return['error'][] = 'String empty';
            return $return;
         }
         if($key=='') {
            $return['error'][] = 'Key empty';
            trigger_error('Key can not be empty', E_USER_NOTICE); 
            return $return;
         } else {
            $key = substr(hash($this->cipher_algorithm, $key, true), 0, 32);
            $return['safekey'] = bin2hex($key);
         }
         
         $len = 32;
         $algorithm = $this->textToBinary(mb_substr($this->cipher_algorithm.'_'.$this->generateRandomString($len-strlen($this->cipher_algorithm)), 0, $len));
         if(count(explode(' ',$algorithm)) !== $len) {
            $return['error'][] = 'Algorithm error';
            trigger_error('Algorithm error', E_USER_NOTICE); 
            return $return;
         }
         
         $ivlen = openssl_cipher_iv_length($method);
         $iv = openssl_random_pseudo_bytes($ivlen);
         
         $return['iv'] = bin2hex($iv); 
         foreach(str_split($return['decrypted'], 20) as $str) {
            $return['base64_encode'][] = $this->textToBinary(base64_encode(openssl_encrypt($str, $method, $key, OPENSSL_RAW_DATA, $iv)), null);
         }
         $return['encrypted'] = $algorithm. PHP_EOL . implode(PHP_EOL, $return['base64_encode']) . PHP_EOL . $this->textToBinary($return['iv']);
         $return['encrypted'] = str_replace(array(' ',PHP_EOL),array($this->delimiter_character,$this->delimiter_breaks),$this->delimiter_characters.' '.$return['encrypted'].' '.$this->delimiter_characters);
         $return['encryptedlen'] = strlen($return['encrypted']);
         
         if($this->compress==true) {
            $return['encryptednotcompressed'] = $return['encrypted'];
            $return['encrypted'] = addslashes($this->delimiter_gz.utf8_encode(gzencode($return['encrypted'],9)).$this->delimiter_gz);
            $return['compressedlen'] = strlen($return['encrypted']);
         }
         
         return $return;
      }
      public function decrypt($str, $key, $method = '') {
         if(!is_string($str)) {
            if((is_array($str)) && (isset($str['encrypted']))) {
               $str = $str['encrypted'];
            } else {
               $return['decrypted'] = trim($str);
               $return['error'][]   = $str = '#'.__LINE__.': String not valid';
               trigger_error($str, E_USER_NOTICE); 
               return $return;
            }
         }
         $str = trim(stripslashes($str));
         if(($this->compress==true) && (($this->startsWith($str, $this->delimiter_gz)) && ($this->endsWith($str, $this->delimiter_gz)))) {
            $str = gzdecode(trim(utf8_decode($str),$this->delimiter_gz));
         }
         if($method == '') {
            $method = $this->cipher_method;
         }
         $rawencrypted = $str;
         $str = str_replace(array($this->delimiter_character,$this->delimiter_breaks),array(' ',PHP_EOL),$str);
         
         if((!$this->startsWith($str, $this->delimiter_characters)) || (!$this->endsWith($str, $this->delimiter_characters))) {
            $return['decrypted'] = trim($str);
            $return['error'][]   = $str = '#'.__LINE__.': String not valid';
            trigger_error($str, E_USER_NOTICE); 
            return $return;
         } else if(($this->startsWith($str, $this->delimiter_characters)) && ($this->endsWith($str, $this->delimiter_characters))) {
            $str = trim(substr(substr($str,strlen($this->delimiter_characters)), 0, (0-strlen($this->delimiter_characters))));
         } else {
            $return['error'][] = $str = '#'.__LINE__.': String not valid';
            trigger_error($str, E_USER_NOTICE); 
            return $return;
         }

         $return = array('decrypted' => '', 'encrypted' => trim($str), 'rawencrypted' => $rawencrypted, 'key' => $key, 'safekey' => '', 'algorithm' => $this->cipher_algorithm, 'method' => $method, 'iv' => '', 'base64_encode' => array(), 'error' => array());
         
         
         if($str == '') {
            $return['error'][] = 'String empty';
            return $return;
         }
         if($key=='') {
            $return['error'][] = 'Key empty';
            trigger_error('Key can not be empty', E_USER_NOTICE); 
            return $return;
         } else {
            $key = substr(hash($this->cipher_algorithm, $key, true), 0, 32);
            $return['safekey'] = bin2hex($key);
         }
         
         
         $ivlen = openssl_cipher_iv_length($method);
         $iv = openssl_random_pseudo_bytes($ivlen);
         $ivlen = strlen(bin2hex($iv)); 
         
         $decrypts = explode(PHP_EOL, $return['encrypted']);
         
         $iv = hex2bin($this->binaryToText(end($decrypts)));
         $return['iv'] = bin2hex($iv); 
         array_pop($decrypts);

         
         $algorithm = explode('_',$this->binaryToText(current($decrypts)));
         array_pop($algorithm);
         $algorithm = implode('_',$algorithm);
         unset($decrypts[0]);
         
         
         $return['base64_encode'] = $decrypts;
         foreach($return['base64_encode'] as $decrypting) {
            $return['decrypted'] .= $str = openssl_decrypt(base64_decode($this->binaryToText($decrypting)), $method, $key, OPENSSL_RAW_DATA, $iv);
         }
         $return['decrypted'] = stripslashes($return['decrypted']);
         
         return $return;
      }
   }
   
   class hash {
      use \orgelman\functions\traits\stringHTMLEntities;
      use \orgelman\functions\traits\stringRandom;
      
      private $saltMaxLength = 255;

      public function __construct($compress = true) {
         $this->encrypt = new encrypt($compress);
      }
      public function __destruct() {
         
      }
      public function generate($password) {
         $password = $this->convertToHTMLEntities($password);
         if(defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
            $salt = '$2y$11$' . trim(substr($this->generateRandomString(10).md5(uniqid(rand(), true)).$this->generateRandomString(10), 0, $this->saltMaxLength)) . '$';
            $hash = crypt($password, $salt);
         }
         return $this->encrypt->encrypt($hash, substr(mb_strtolower($password),0,ceil(strlen($password)/2)))['encrypted'];
      }
      public function verify($password, $hashedPassword) {
         $hashedPassword = $this->encrypt->decrypt($hashedPassword,substr(mb_strtolower($password),0,ceil(strlen($password)/2)))['decrypted'];
         return crypt($password, $hashedPassword) == $hashedPassword;
      }
   }
}
