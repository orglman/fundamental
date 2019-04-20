<?php 
/**
 * @package orglman/fundamental
 * @link    https://github.com/orglman/fundamental/
 * @author  Tobias Jonson <git@orgelman.systems>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (GNU GPL)
 */
namespace orgelman\fundamental\debug {
   class debug {
      private $state    = true;
      private $level    = null;
      private $logPath  = null;
      
      public function __construct($state, $logPath, $start = '') {
         global $OFS_LogAndErrorArray;
         global $OFS_SettingsArray;
         
         $OFS_LogAndErrorArray   = array();
         $OFS_SettingsArray      = array();
         
         if(is_bool($state)) {
            $this->state = $state;
         } else {
            $this->state = false;
         }
         
         if($start == '') {
            $start = microtime(true);
         }
         if(is_dir($logPath)) {
            $this->logPath = $logPath;
         }
         $OFS_SettingsArray['logPath'] = $this->logPath;
         
         $OFS_SettingsArray['state'] = $this->state;
         
         $this->start = $start;
         $OFS_SettingsArray['start']  = $this->start;

         $this->setErrorLog(error_reporting());
      }
      public function __destruct() {
         global $OFS_LogAndErrorArray;
         global $OFS_SettingsArray;
         if($OFS_SettingsArray['state']) {
            ob_start();
            
            $i=1;
            echo '# Log entries and errors'."\n\n";
            foreach($OFS_LogAndErrorArray as $log) {
               $space = str_repeat(' ', strlen($log['Date'].'  '));
               echo $log['Date'].': '.round(($log['Time'] - $OFS_SettingsArray['start']), 5).' seconds after script start'.PHP_EOL;
               echo $space.$log['Type'].': '.str_replace("\n","\n".$space.str_repeat(' ', strlen($log['Type'].'  ')),$log['String']).PHP_EOL;
               echo $space.'In file:   '.$log['File'].' ['.$log['Line'].']'.PHP_EOL;
               array_shift($log['Backtrace']);
               if(count($log['Backtrace'])>0) {
                  echo $space.'Backtrace: ';
                  $j=0;
                  foreach($log['Backtrace'] as $backtrace) {
                     if($j!=0) {
                        echo $space.str_repeat(' ', strlen('Backtrace  '));
                     }
                     echo $backtrace['file'].' ['.$backtrace['line'].']'.PHP_EOL;
                     $j++;
                  }
               }

               if(count($OFS_LogAndErrorArray) != $i) {
                  echo "\n\n".'--'."\n\n";
               }
               $i++;
            }
            
            echo "\n\n\n--\n\n";
            echo '# Variables'."\n\n";
            if((isset($_GET)) && (is_array($_GET)) && (!empty($_GET))) {
               echo '## $_GET'.PHP_EOL;
               echo print_r($_GET, true);
               echo "\n\n".'--'."\n\n";
            }
            if((isset($_POST)) && (is_array($_POST)) && (!empty($_POST))) {
               echo '## $_POST'.PHP_EOL;
               echo print_r($_POST, true);
               echo "\n\n".'--'."\n\n";
            }
            if((isset($_SESSION)) && (is_array($_SESSION)) && (!empty($_SESSION))) {
               echo '## $_SESSION'.PHP_EOL;
               echo print_r($_SESSION, true);
               echo "\n\n".'--'."\n\n";
            }
            if((isset($_COOKIE)) && (is_array($_COOKIE)) && (!empty($_COOKIE))) {
               echo '## $_COOKIE'.PHP_EOL;
               echo print_r($_COOKIE, true);
               echo "\n\n".'--'."\n\n";
            }
            if((isset($_FILES)) && (is_array($_FILES)) && (!empty($_FILES))) {
               echo '## $_FILES'.PHP_EOL;
               echo print_r($_FILES, true);
               echo "\n\n".'--'."\n\n";
            }
            if((isset($_SERVER)) && (is_array($_SERVER)) && (!empty($_SERVER))) {
               echo '## $_SERVER'.PHP_EOL;
               echo print_r($_SERVER, true);
            }
            
            $logFile = fopen($OFS_SettingsArray['logPath']."log.log", "w") or die("Unable to open file!");
            fwrite($logFile, ob_get_clean());
            fclose($logFile);
         }
      }

      public function setErrorLog($level = '') {
         global $OFS_SettingsArray;
         if(($level == '')
         && ($level != E_ERROR)
         && ($level != E_WARNING)
         && ($level != E_NOTICE)
         && ($level != E_CORE_ERROR)
         && ($level != E_CORE_WARNING)
         && ($level != E_COMPILE_ERROR)
         && ($level != E_COMPILE_WARNING)
         && ($level != E_USER_ERROR)
         && ($level != E_USER_WARNING)
         && ($level != E_USER_NOTICE)
         && ($level != E_STRICT)
         && ($level != E_RECOVERABLE_ERROR)
         && ($level != E_DEPRECATED)
         && ($level != E_USER_DEPRECATED)
         && ($level != E_ALL)
         && ($level != E_USER_DEPRECATED)
         && ($level != -1)
         ) {
            $level = '0';
         }
         $this->level = $level;
         $OFS_SettingsArray['level'] = $this->level;
         
         error_reporting(E_ALL);
         ini_set('display_errors', TRUE);
         ini_set('display_startup_errors', TRUE);

         set_error_handler(array($this, 'log'));
      }
      
      
      public static function log($errNo, $errStr = NULL, $errFile = '', $errLine = '') {
         if($errStr == NULL) {
            $errStr = $errNo;
            $errNo = NULL;
         }
         global $OFS_SettingsArray;
         global $OFS_LogAndErrorArray;
         switch($errNo) {
            case NULL:                 $errseverity = "Log"; $errNo = 40000;  break;
            case E_ERROR:              $errseverity = "Error";                break;
            case E_WARNING:            $errseverity = "Warning";              break;
            case E_NOTICE:             $errseverity = "Notice";               break;
            case E_CORE_ERROR:         $errseverity = "Core Error";           break;
            case E_CORE_WARNING:       $errseverity = "Core Warning";         break;
            case E_COMPILE_ERROR:      $errseverity = "Compile Error";        break;
            case E_COMPILE_WARNING:    $errseverity = "Compile Warning";      break;
            case E_USER_ERROR:         $errseverity = "User Error";           break;
            case E_USER_WARNING:       $errseverity = "User Warning";         break;
            case E_USER_NOTICE:        $errseverity = "User Notice";          break;
            case E_STRICT:             $errseverity = "Strict Standards";     break;
            case E_RECOVERABLE_ERROR:  $errseverity = "Recoverable Error";    break;
            case E_DEPRECATED:         $errseverity = "Deprecated";           break;
            case E_USER_DEPRECATED:    $errseverity = "User Deprecated";      break;
            default:                   $errseverity = "Log"; $errNo = 40000;  break;
         }
         if($errFile == '') {
            $errFile = debug_backtrace()[0]['file'];
         }
         if($errLine == '') {
            $errLine = debug_backtrace()[0]['line'];
         }
         
         if(!is_string($errStr)) {
            $errStr = print_r($errStr, true);
         }
         
         $m = microtime(true);
         $log = array('Date' => date("Y m d H:i:s e"), 'Time' => $m, 'Type' => $errseverity, 'String' => $errStr, 'File' => $errFile, 'Line' => $errLine, 'Backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
         $OFS_LogAndErrorArray[] = $log;
         
         ob_start();
         $space = str_repeat(' ', strlen($log['Date'].'  '));
         echo $log['Date'].': '.round(($log['Time'] - $OFS_SettingsArray['start']), 5).' seconds after script start'.PHP_EOL;
         echo $space.$log['Type'].': '.$log['String'].PHP_EOL;
         echo $space.'In file:   '.$log['File'].' ['.$log['Line'].']'.PHP_EOL;
         array_shift($log['Backtrace']);
         if(count($log['Backtrace'])>0) {
            echo $space.'Backtrace: ';
            $j=0;
            foreach($log['Backtrace'] as $backtrace) {
               if($j!=0) {
                  echo $space.str_repeat(' ', strlen('Backtrace  '));
               }
               if((isset($backtrace['file'])) && (isset($backtrace['line']))) {
                  echo $backtrace['file'].' ['.$backtrace['line'].']'.PHP_EOL;
               }
               $j++;
            }
         }
         
         error_log(ob_get_clean());
         if(php_sapi_name() !== 'cli') {
            if(($OFS_SettingsArray['level'] >= $errNo) || ($OFS_SettingsArray['level'] == -1)) {
               $v = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
               $Date = date("Y m d H:i:s e");

               $out  = '<pre style="border-bottom:1px solid #eee;">';
               $out .= '<strong>'.$Date.'</strong> '.round(($m - $OFS_SettingsArray['start']), 5).' seconds after script start'.PHP_EOL;
               $out .= str_repeat(' ', strlen($Date.' ')).'<span style="color:red;">'.$errseverity.':</span> '.str_replace("\n","\n".str_repeat(' ', strlen($Date.' '.$errseverity.'  ')), $errStr).PHP_EOL;
               $out .= str_repeat(' ', strlen($Date.' ')).'<span style="color:#3D9700;">In file: '.$errFile.' ['.$errLine.']</span>'.PHP_EOL;
               if(count($v)>1) {     
                  $out .= str_repeat(' ', strlen($Date.' ')).'<strong>Backtrace: </strong>';
                  for ($i = 1; $i<count($v); $i++) {
                     if($i!=1) {
                        $out .= str_repeat(' ', strlen($Date.' Backtrace: '));
                     }
                     $out .= (isset($v[$i]["file"]) ? $v[$i]["file"] : "unknown")." [".(isset($v[$i]["line"]) ? $v[$i]["line"] : "unknown").']' . PHP_EOL;
                  }
               }
               $out.='</pre>';

               echo $out;
            }
         }
      }
      
      public function isDebug() {
         return $this->state;
      }
   }
}
