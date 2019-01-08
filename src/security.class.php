<?php 
/**
 * @package orglman/fundamental
 * @link    https://github.com/orglman/fundamental/
 * @author  Tobias Jonson <git@orgelman.systems>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (GNU GPL)
 */
namespace orgelman\security {
   class encrypt {
      use \orgelman\functions\traits\stringConvert;
      use \orgelman\functions\traits\stringHTMLEntities;
      use \orgelman\functions\traits\stringRandom;
      use \orgelman\functions\traits\stringSearch;
      
      private $cipher_algorithm = '';
      private $cipher_method = 'AES-256-CBC';
      
      private $delimiter_characters = '==00==';
      
      private $compress = false;
      
      public function __construct($cipher_algorithm = 'sha256') {
         if(!in_array($cipher_algorithm, hash_algos())) {
            trigger_error('Hashing algorithm invalid', E_USER_ERROR); 
            exit();
         }
         $this->delimiter_characters = $this->textToBinary($this->delimiter_characters);
         
         $this->cipher_algorithm = $cipher_algorithm;
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
         $str = $this->convertToHTMLEntities($str);
         $key = $this->convertToHTMLEntities($key);
         
         if($method == '') {
            $method = $this->cipher_method;
         }
         $return = array('decrypted' => addslashes(trim($str)), 'encrypted' => '', 'key' => $key, 'safekey' => '', 'algorithm' => $this->cipher_algorithm, 'method' => $method, 'iv' => '', 'base64_encode' => array(), 'error' => array());

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
            $return['base64_encode'][] = $this->textToBinary(base64_encode(openssl_encrypt($str, $method, $key, OPENSSL_RAW_DATA, $iv)));
         }
         $return['encrypted'] = $algorithm. "\n" . implode("\n", $return['base64_encode']) . "\n" . $this->textToBinary($return['iv']);
         if($this->compress==true) {
            $return['encrypted'] = gzcompress($return['encrypted'],9);
         }
         
         $return['encrypted'] = $this->delimiter_characters.' '.$return['encrypted'].' '.$this->delimiter_characters;
         
         return $return;
      }
      public function decrypt($str, $key, $method = '') {
         $key = $this->convertToHTMLEntities($key);
         if($method == '') {
            $method = $this->cipher_method;
         }
         
         if((!$this->startsWith($str, $this->delimiter_characters)) || (!$this->endsWith($str, $this->delimiter_characters))) {
            return array('decrypted' => trim($str));
         } else if(($this->startsWith($str, $this->delimiter_characters)) && ($this->endsWith($str, $this->delimiter_characters))) {
            $str = trim(substr(substr($str,strlen($this->delimiter_characters)), 0, (0-strlen($this->delimiter_characters))));
         } else {
            $return['error'][] = 'String not valid';
            trigger_error('String not valid', E_USER_NOTICE); 
            return $return;
         }

         $return = array('decrypted' => '', 'encrypted' => trim($str), 'key' => $key, 'safekey' => '', 'algorithm' => $this->cipher_algorithm, 'method' => $method, 'iv' => '', 'base64_encode' => array(), 'error' => array());
         if($this->compress==true) {
            $return['encrypted'] = gzuncompress($return['encrypted']);
         }
         
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
         
         $decrypts = explode("\n", $return['encrypted']);
         
         $iv = hex2bin($this->binaryToText(end($decrypts)));
         $return['iv'] = bin2hex($iv); 
         array_pop($decrypts);

         
         $algorithm = explode('_',$this->binaryToText(current($decrypts)));
         array_pop($algorithm);
         $algorithm = implode('_',$algorithm);
         unset($decrypts[0]);
         
         
         $return['base64_encode'] = $decrypts;
         foreach($return['base64_encode'] as $decrypting) {
            $return['decrypted'] .= $str = convertFromHTMLEntities(openssl_decrypt(base64_decode($this->binaryToText($decrypting)), $method, $key, OPENSSL_RAW_DATA, $iv));
         }
         $return['decrypted'] = stripslashes($return['decrypted']);
         
         return $return;
      }
   }
   
   class hash {
      use \orgelman\functions\traits\stringHTMLEntities;
      use \orgelman\functions\traits\stringRandom;
      
      private $saltMaxLength = 255;

      public function __construct() {
         $this->encrypt = new encrypt();
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
