<?php 
/**
 * @package orglman/fundamental
 * @link    https://github.com/orglman/fundamental/
 * @author  Tobias Jonson <git@orgelman.systems>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (GNU GPL)
 */
 
namespace orgelman\functions\traits {
  trait stringConvert {
      public function textToBinary($str) {
         $bin = array();
         for($i=0; strlen($str)>$i; $i++) {
            if(function_exists('mb_ord')) {
               $bin[] = str_pad(decbin(mb_ord($str[$i])), 8, '0', STR_PAD_LEFT);
            } else {
               $bin[] = str_pad(decbin(ord($str[$i])), 8, '0', STR_PAD_LEFT);
            }
         }
         return implode(' ',$bin);
      }
      public function binaryToText($str) {
         $bin = explode(' ', $str);
         
         $text = array();
         for($i=0; count($bin)>$i; $i++) {
            if(function_exists('mb_chr')) {
               $text[] = mb_chr(bindec(ltrim($bin[$i],'0')));
            } else {
               $text[] = chr(bindec(ltrim($bin[$i],'0')));
            }
         }
      
         return implode($text);
      }
  }
}
