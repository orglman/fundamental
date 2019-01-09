<?php 
/**
 * @package orglman/fundamental
 * @link    https://github.com/orglman/fundamental/
 * @author  Tobias Jonson <git@orgelman.systems>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (GNU GPL)
 */
 
namespace orgelman\fundamental\traits {
  trait stringSearch {
    function startsWith($haystack, $needle) {
      $length = strlen($needle);
      return (substr($haystack, 0, $length) === $needle);
    }
    function endsWith($haystack, $needle) {
      $length = strlen($needle);
      if($length == 0) {
        return true;
      }
      return (substr($haystack, -$length) === $needle);
    }
  }
}
