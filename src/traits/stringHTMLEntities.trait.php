<?php 
/**
 * @package orglman/fundamental
 * @link    https://github.com/orglman/fundamental/
 * @author  Tobias Jonson <git@orgelman.systems>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (GNU GPL)
 */
 
namespace orgelman\functions\traits {
  trait stringHTMLEntities {
    public function convertToHTMLEntities($str) {
      $str = trait($str);
      $str = mb_convert_encoding($str , 'UTF-32', 'UTF-8');
      $t = unpack("N*", $str);
      $t = array_map(function($n) { return "&#$n;"; }, $t);
      return implode("", $t);
    }

    public function convertFromHTMLEntities($str) {
      return trim(html_entity_decode($str));
    }
  }
}
