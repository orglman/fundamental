<?php 
/**
 * @package orglman/fundamental
 * @link    https://github.com/orglman/fundamental/
 * @author  Tobias Jonson <git@orgelman.systems>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (GNU GPL)
 */

/**
 * define("OFS_SQL_HOST"       , "host");
 * define("OFS_SQL_PORT"       , "port");
 * define("OFS_SQL_NAME"       , "database");
 * define("OFS_SQL_USERNAME"   , "username");
 * define("OFS_SQL_PASSWORD"   , "password");
 * define("OFS_SQL_PREFIX"     , "pre_");
 * define("OFS_SQL_SOCKET"     , "");
 * 
 * Examples:
 * Check if _POST or _GET ["variable"] exists then load it or leave str empty
 * $str = ($sql->isRequest("variable") ? $sql->getRequest("variable") : "");
 *
 * Query:
 *            Måste inehålla [[DB]]                                         och ; i slutet och om det är något annat än SELECT måste det anges
 * $sql->SQL("SELECT * FROM `[[DB]]calendar` WHERE (`Uid` != '".$str."') LIMIT 1;");
 * $sql->SQL("INSERT INTO `[[DB]]calendar`;","insert");
 *
 * SELECT query loop throug results
 * foreach($sql->SQL("SELECT * FROM `[[DB]]calendar` WHERE (`Uid` != '".$str."') LIMIT 1;") as $row) {
 *
 * }
 */
namespace orgelman\mysqli {
   use PHPSQLParser\PHPSQLParser;

   class MySQLi {

      public  $DBh            = "";
      public  $result         = array();

      private $SQL_HOST       = "";
      private $SQL_USERNAME   = "";
      private $SQL_PASSWORD   = "";
      private $SQL_NAME       = "";
      private $SQL_PREFIX     = "";
      private $SQL_SOCKET     = "";

      public function __construct($SQL_HOST="",$SQL_USERNAME="",$SQL_PASSWORD="",$SQL_NAME="",$SQL_PREFIX="",$SQL_SOCKET="") {
         //Setting SQL 
         if(defined("OFS_SQL_HOST")) {
            $this->SQL_HOST      = constant("OFS_SQL_HOST");
         } else {
            $this->SQL_HOST      = $SQL_HOST;
            define("OFS_SQL_HOST",$this->SQL_HOST);
         }
         if(defined("OFS_SQL_USERNAME")) {
            $this->SQL_USERNAME  = constant("OFS_SQL_USERNAME");
         } else {
            $this->SQL_USERNAME  = $SQL_USERNAME;
            define("OFS_SQL_USERNAME",$this->SQL_USERNAME);
         }
         if(defined("OFS_SQL_PASSWORD")) {
            $this->SQL_PASSWORD  = constant("OFS_SQL_PASSWORD");
         } else {
            $this->SQL_PASSWORD  = $SQL_PASSWORD;
            define("OFS_SQL_PASSWORD",$this->SQL_PASSWORD);
         }
         if(defined("OFS_SQL_NAME")) {
            $this->SQL_NAME      = constant("OFS_SQL_NAME");
         } else {
            $this->SQL_NAME      = $SQL_NAME;
            define("OFS_SQL_NAME",$this->SQL_NAME);
         }
         if(defined("OFS_SQL_PREFIX")) {
            $this->SQL_PREFIX    = constant("OFS_SQL_PREFIX");
         } else {
            $this->SQL_PREFIX    = $SQL_PREFIX;
            define("OFS_SQL_PREFIX",$this->SQL_PREFIX);
         }
         if(defined("OFS_SQL_SOCKET")) {
            $this->SQL_SOCKET    = constant("OFS_SQL_SOCKET");
         } else {
            $this->SQL_SOCKET    = $SQL_SOCKET;
            define("OFS_SQL_SOCKET",$this->SQL_SOCKET);
         }

         $this->DBh = $this->StartDBConnection();
      }
      public function __destruct() {
         $this->StopDBConnection();
      }

      public function StartDBConnection() {
         $DBh = false;
         if((isset($DBh)) && ($DBh!="")) { 

         } else {
            $DBh = "";
            $DBh = @mysqli_connect(OFS_SQL_HOST,OFS_SQL_USERNAME,OFS_SQL_PASSWORD,OFS_SQL_NAME) or die("SQL ERROR (".__LINE__."): Connection error: ".__LINE__);
            if (mysqli_connect_errno()) {
               trigger_error('SQL ERROR ('.__LINE__.'): Connection error',E_USER_ERROR);
               die("SQL ERROR (".__LINE__."): Connection error: ".__LINE__);
            }
            if($DBh=="") {
               trigger_error('SQL ERROR ('.__LINE__.'): Connection error',E_USER_ERROR);
               die("SQL ERROR (".__LINE__."): Connection error: ".__LINE__);
            }
         }

         $this->DBh = $DBh;
         return $DBh;
      }
      public function StopDBConnection(){
         if((isset($this->DBh)) && ($this->DBh!="")) { 
            @mysqli_close($this->DBh);
         }
      }
      private function wash($q) {

         return $q;
      }
      private function verify($q,$allow,$caller) {
         $parser = new PHPSQLParser($q);
         $this->result[$this->start]["queryp"] = $parser;
         $i = 0;
         foreach($parser->parsed as $par => $val) {
            if($i==0) {
               if(trim(strtolower($par)) != trim(strtolower($allow))) {
                  trigger_error('SQL ERROR ('.__LINE__.'): String type not match',E_USER_ERROR);
                  die("SQL ERROR (".__LINE__."): ".$q."<br>\nString type not match<hr>\nCalled: ". $caller["file"]." [".$caller["line"]."]");
               }
            }
            $i++;
         }
         unset($i);

         if(strtolower(substr(trim($q), 0, strlen("drop"))) === strtolower("drop")) {
            trigger_error('SQL ERROR ('.__LINE__.'): Can not drop table',E_USER_ERROR);
            die("SQL ERROR (".__LINE__."): ".$q."<br>\nCan not drop table<hr>\nCalled: ". $caller["file"]." [".$caller["line"]."]");
         }
         if(strtolower(substr(trim($q), 0, strlen("trunkate"))) === strtolower("trunkate")) {
            trigger_error('SQL ERROR ('.__LINE__.'): Can not trunkate table',E_USER_ERROR);
            die("SQL ERROR (".__LINE__."): ".$q."<br>\nCan not trunkate table<hr>\nCalled: ". $caller["file"]." [".$caller["line"]."]");
         }
         if(strtolower(substr(trim($q), 0, strlen("alter"))) === strtolower("alter")) {
            trigger_error('SQL ERROR ('.__LINE__.'): Can not alter table',E_USER_ERROR);
            die("SQL ERROR (".__LINE__."): ".$q."<br>\nCan not alter table<hr>\nCalled: ". $caller["file"]." [".$caller["line"]."]");
         }
         if((strtolower(substr(trim($q), 0, strlen($allow))) === strtolower($allow)) && ((substr(trim($q), -1) === ';'))) {
            return $q;
         } else {
            if(strtolower(substr(trim($q), 0, strlen($allow))) !== strtolower($allow)) {
               trigger_error('SQL ERROR ('.__LINE__.'): String type not match',E_USER_ERROR);
               die("SQL ERROR (".__LINE__."): ".$q."<br>\nString type not match<hr>\nCalled: ". $caller["file"]." [".$caller["line"]."]");
            } elseif(substr(trim($q), -1) !== ';') {
               trigger_error('SQL ERROR ('.__LINE__.'): String end not match',E_USER_ERROR);
               die("SQL ERROR (".__LINE__."): ".$q."<br>\nString end not match<hr>\nCalled: ". $caller["file"]." [".$caller["line"]."]");
            } else {
               trigger_error('SQL ERROR ('.__LINE__.'): Unknown Error',E_USER_ERROR);
               die("SQL ERROR (".__LINE__."): ".$q."<br>\nUnknown Error<hr>\nCalled: ". $caller["file"]." [".$caller["line"]."]");
            }
            return false;
         }
         return false;
      }
      public function insert($variable) {
         $old = array("  ");
         $new = array(" ");
         if(isset($this->DBh)) {
            $variable = $this->DBh->real_escape_string(str_replace($old,$new,trim(urldecode($variable))));
         }

         return $variable; 
      }
      public function isRequest($str="") {
         if(isset($_REQUEST[$str])) {
            return true;
         } 
         if(isset($_GET[$str])) {
            $_REQUEST[$str] = $_GET[$str];
            return true;
         }
         if(isset($_POST[$str])) {
            $_REQUEST[$str] = $_POST[$str];
            return true;
         }
         return false;
      }
      public function getRequest($str="") {
         $deb = debug_backtrace()[0];
         if(isset($_REQUEST[$str])) {
            $str = $this->insert($_REQUEST[$str]);
         } elseif(isset($_GET[$str])) {
            $_REQUEST[$str] = $_GET[$str];
            $str = $this->insert($_GET[$str]);
         } elseif(isset($_POST[$str])) {
            $_REQUEST[$str] = $_POST[$str];
            $str = $this->insert($_POST[$str]);
         } else {
            trigger_error("Variable '".$str."' not found<br>\n".$deb["file"]." [".$deb["line"]."]", E_USER_ERROR);
            return false;
         }
         return $str; 
      }
      public function SQL($q,$allow="select") { 
         $arr     = array();
         $sel     = "Select";
         $prefix  = "[[DB]]";
         $caller = debug_backtrace()[0];
         $this->start = count($this->result)+1;
         $start = $this->start;

         if((is_array($q)) || (is_object($q))) {
            $query = "SELECT \n";
            $i=0;
            foreach($q as $v => $qu) {
               if(strpos($qu, $prefix) == false) {
                  trigger_error('SQL ERROR ('.__LINE__.'): Missing '.$prefix,E_USER_ERROR);
                  die("SQL ERROR (".__LINE__."): Missing ".$prefix." Called: ". $caller["file"]." [".$caller["line"]."]");
               }
               if($qu!="") {
                  $strpos = strpos($qu,$prefix);
                  if(substr($qu,($strpos-1),1) == "`") {
                     $qu = str_replace($prefix,constant("OFS_SQL_NAME")."`.`".constant("OFS_SQL_PREFIX")."",$qu);
                  } else {
                     $qu = str_replace($prefix,"`".constant("OFS_SQL_NAME")."`.`".constant("OFS_SQL_PREFIX")."",$qu);
                  }
               }
               $this->verify($qu,$allow,$caller);
               if($i!=0) {
                  $query .= ",\n";
               }
               $query .= "   (".trim($qu,";").") as `".$v."`";
               $i++;
            }
            $q = $query.";";
         } else {
            if(strpos($q, $prefix) == false) {
               trigger_error('SQL ERROR ('.__LINE__.'): Missing '.$prefix,E_USER_ERROR);
               die("SQL ERROR (".__LINE__."): Missing ".$prefix." Called: ". $caller["file"]." [".$caller["line"]."]");
            }
            if($q!="") {
               $strpos = strpos($q,$prefix);
               if(substr($q,($strpos-1),1) == "`") {
                  $q = str_replace($prefix,constant("OFS_SQL_NAME")."`.`".constant("OFS_SQL_PREFIX")."",$q);
               } else {
                  $q = str_replace($prefix,"`".constant("OFS_SQL_NAME")."`.`".constant("OFS_SQL_PREFIX")."",$q);
               }
            }
            $this->verify($q,$allow,$caller);
         }
         if($q!="") {
            $q = $this->wash($q);
            $this->verify($q,$allow,$caller);
            if(isset($this->DBh)) {} else {
               $this->DBh = $this->StartDBConnection();
            }
            $MySQLi[0]["Result"] = $this->DBh->query($q);
            $this->result[$start]["query"] = $q;

            if(strtolower(substr($q, 0, strlen($sel))) === strtolower($sel)) {
               if(!$MySQLi[0]["Result"]) {
                  trigger_error('SQL ERROR ('.__LINE__.'): SQL ERROR '.$this->DBh->error."<br>\n".$caller["file"]." [".$caller["line"]."]",E_USER_ERROR);
                  die("SQL ERROR (".__LINE__."): ".$q."<br>\nSQL ERROR (".__LINE__."): ".$this->DBh->error."<br>\n".$caller["file"]." [".$caller["line"]."]");
               } elseif($MySQLi[0]["Result"]->num_rows>0) {
                  while($MySQLi[0]["Rows"]=$MySQLi[0]["Result"]->fetch_object()){
                     $arr[] = $MySQLi[0]["Rows"];
                  }
                  $this->result[$start]["status"] = 1;
                  $this->result[$start]["count"] = $MySQLi[0]["Result"]->num_rows;
                  $this->result[$start]["rows"] = $arr;
                  return $arr;
               } elseif($MySQLi[0]["Result"]->num_rows==0) {
                  $this->result[$start]["status"] = 1;
                  $this->result[$start]["count"] = $MySQLi[0]["Result"]->num_rows;
                  $this->result[$start]["rows"] = array();
                  return array();
               }
            } else {
               if(!$MySQLi[0]["Result"]) {
                  trigger_error('SQL ERROR ('.__LINE__.'): SQL ERROR '.$this->DBh->error."<br>\n".$caller["file"]." [".$caller["line"]."]",E_USER_ERROR);
                  die("SQL ERROR (".__LINE__."): ".$q."<br>\nSQL ERROR (".__LINE__."): ".$this->DBh->error."<br>\n".$caller["file"]." [".$caller["line"]."]");
                  $this->result[$start]["status"] = 0;
                  $this->result[$start]["rows"] = array();
                  return array();
               }
               $this->result[$start]["status"] = 1;
               $this->result[$start]["count"] = mysqli_affected_rows($this->DBh);
               $this->result[$start]["rows"] = array();
               return array();
            }
         }
         $this->result[$start]["status"] = 0;
         $this->result[$start]["rows"] = array();
         return array();
      }
      public function SQLBackup($tables = '*', $path = '', $title = '') {
         $return = "";
         if((is_array($tables)) || (is_object($tables))) {
            $t = $tables;
            $tables = array();
            foreach($t as $ta) {
               $tables[] = "`".constant("OFS_SQL_PREFIX").$ta."`";
            }
         } else {
            if($tables!='*') {
               $t = $tables;
               $tables = array();
               $tables[] = "`".constant("OFS_SQL_PREFIX").$t."`";
            }
            if($tables == '*') {
               $tables = array();
               $result = $this->DBh->query('SHOW TABLES');
               while($row = mysqli_fetch_row($result)) {
                  $tables[] = $row[0];
               }
            }
         } 

         $creates = array();
         $result = $this->DBh->query('SHOW TABLES');
         while($row = mysqli_fetch_row($result)) {
            $creates[] = $row[0];
         }

         foreach($creates as $table) {
            if(!in_array($table, $tables)) { 
               $return.= '-- phpMyAdmin SQL Dump'."\n";
               $return.= '-- Generation Time '.date("Y-m-d H:i:s e")."\n"."\n";
               $return.= '-- --------------------------------------------------------'."\n";
               $return.= '-- Table structure for table '.$table."\n";

               $row2 = preg_replace('/CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', mysqli_fetch_row($this->DBh->query('SHOW CREATE TABLE '.$table)), 1);
               $return.= "\n\n".$row2[1].";\n\n";
            }
         }
         $return.= '-- --------------------------------------------------------'."\n";
         $return.= '-- --------------------------------------------------------'."\n";
         $return.= '-- --------------------------------------------------------'."\n";

         foreach($tables as $table) {
            $result = $this->DBh->query('SELECT * FROM '.$table);
            if(!is_bool($result)) {
               $num_fields = mysqli_num_fields($result);

               $return.= '-- phpMyAdmin SQL Dump'."\n";
               $return.= '-- http://www.phpmyadmin.net'."\n"."\n";
               $return.= '-- Generation Time '.date("Y-m-d H:i:s e")."\n"."\n";
               $return.= '-- orgelman systems'."\n"."\n";
               $return.= '--'."\n";
               $return.= '-- Database: '.constant("OFS_SQL_NAME").''."\n";
               $return.= '--'."\n"."\n";
               $return.= '-- --------------------------------------------------------'."\n";
               $return.= '--'."\n";
               $return.= '-- Table structure for table '.$table."\n";
               $return.= '--'."\n"."\n";

               $return.= 'DROP TABLE IF EXISTS '.$table.';';
               $row2 = mysqli_fetch_row($this->DBh->query('SHOW CREATE TABLE '.$table));
               $return.= "\n\n".$row2[1].";\n\n";

               $return.= '--'."\n";
               $return.= '-- Dumping data for table '.$table."\n";
               $return.= '--'."\n"."\n";

               for ($i = 0; $i < $num_fields; $i++) {
                  while($row = mysqli_fetch_row($result)) {
                     $return.= 'INSERT INTO '.$table.' VALUES(';
                     for($j=0; $j < $num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = str_replace("\n","\\n",$row[$j]);
                        if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                        if ($j < ($num_fields-1)) { $return.= ','; }
                     }
                     $return.= ");\n";
                  }
               }
               $return.="\n\n\n";
            }
         }
         if($path=="") {
            $path = __DIR__;
         }
         if($title!="") {
            $title = $title." ";
         }
         $paths = rtrim($path,"/").'/'.$this->toAscii($title).'db-backup-'.$this->toAscii(date("YmdHis")).'-'.(md5(implode(',',$tables))).'.sql';
         $handle = fopen($paths,'w+');
         fwrite($handle,$return);
         fclose($handle);
         return $paths;
      }

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
   }
}
