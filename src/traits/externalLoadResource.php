<?php 
/**
 * @package orglman/fundamental
 * @link    https://github.com/orglman/fundamental/
 * @author  Tobias Jonson <git@orgelman.systems>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (GNU GPL)
 */
 
namespace orgelman\functions\traits {
   trait externalLoadResource {
      private $loadedResources = array();
      private $returnResources = array();
     
      public function remote_file_esists($url) {
         $retcode = null;
         $return = false;
         
         if(isset($this->returnResources[$url])) {
            $retcode = $this->returnResources[$url];
         } else {
            $ch = curl_init($url);
            if(filter_var($url, FILTER_VALIDATE_URL)) {
               curl_setopt($ch, CURLOPT_NOBODY, true);
               curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
               curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds
               curl_exec($ch);
               $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
               $this->returnResources[$url] = $retcode;
               curl_close($ch);
            }
         }
         if($retcode != null) {
            if(($retcode>=200) && ($retcode<400)) {
               $return = true;
            }
         }
         return $return;
     }
      
      public function createHtmlLink($href, $type = 'text/css', $rel = 'stylesheet', $media = 'all', $hreflang = '', $size = '', $extra = '') {
         $rels = array("alternate","author","dns-prefetch","help","icon","license","next","pingback","preconnect","prefetch","preload","prerender","prev","search","stylesheet");
         $medias = array('all', 'print', 'screen', 'speech');
         
         $return = '';
         if(!isset($this->loadedResources[$href])) {
            if($this->remote_file_esists($href)) {
               $elements = array();
               if(filter_var($href, FILTER_VALIDATE_URL)) {
                  $elements[] = 'href="'.trim($href).'"';
               } else {
                  return $return;
               }
               if($hreflang!='') {
                  $elements[] = 'hreflang="'.trim($hreflang).'"';
               }
               if($type!='') {
                  $elements[] = 'type="'.trim($type).'"';
               }
               if(in_array(strtolower($rel), $rels)) {
                  $elements[] = 'rel="'.trim($rel).'"';
               }
               if((strtolower($rel)=='icon')&&($size!='')) {
                  $elements[] = 'sizes="'.$size.'"';
               }
               if($extra!='') {
                  $elements[] = trim($extra);
               }

               $return = '<link '.implode(' ',$elements).' />';
               $this->loadedResources[$src] = $return;
            }
         }
         
         return $return;
      }
      public function createHtmlScript($src, $type = 'text/javascript', $charset = '', $async = '', $defer = '', $extra = '') {
         $return = '';
         if(!isset($this->loadedResources[$src])) {
            if($this->remote_file_esists($src)) {
               $elements = array();
               if(filter_var($src, FILTER_VALIDATE_URL)) {
                  $elements[] = 'src="'.trim($src).'"';
               } else {
                  return $return;
               }
               if($type!='') {
                  $elements[] = 'type="'.trim($type).'"';
               }
               if($charset!='') {
                  $elements[] = 'charset="'.trim($charset).'"';
               }
               if($async!='') {
                  $elements[] = 'async'; // When present, it specifies that the script will be executed asynchronously as soon as it is available.
               }
               if($defer!='') {
                  $elements[] = 'defer'; // When present, it specifies that the script is executed when the page has finished parsing.
               }
               if($extra!='') {
                  $elements[] = trim($extra);
               }

               $return = '<script '.implode(' ',$elements).'></script>';
               $this->loadedResources[$src] = $return;
            }
         }
         
         return $return;
      }
   }
}
