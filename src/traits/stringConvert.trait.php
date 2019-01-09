<?php 
/**
 * @package orglman/fundamental
 * @link    https://github.com/orglman/fundamental/
 * @author  Tobias Jonson <git@orgelman.systems>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (GNU GPL)
 */
 
namespace orgelman\fundamental\traits {
  trait stringConvert {
      public function textToBinary($str, $pad = 8) {
         $bin = array();
         for($i=0; strlen($str)>$i; $i++) {
            if(function_exists('mb_ord')) {
               if(is_numeric($pad)) {
                  $bin[] = str_pad(decbin(mb_ord($str[$i])), $pad, '0', STR_PAD_LEFT);
               } else {
                  $bin[] = decbin(mb_ord($str[$i]));
               }
            } else {
               if(is_numeric($pad)) {
                  $bin[] = str_pad(decbin(ord($str[$i])), $pad, '0', STR_PAD_LEFT);
               } else {
                  $bin[] = decbin(ord($str[$i]));
               }
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
