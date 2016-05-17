<?php
/**
 * Manipulate array functions.
 */
class HArray
{
  private $_array;
  private $_keys = NULL;

  /**
   * @param array $array
   */
  public function __construct($array)
  {
    if (!is_array($array)) {
      throw new Exception("$array parameter should be an array.");
    }
    $this->_array = $array;
  }

  public function getKeys()
  {
    if ($this->_keys === NULL) {
      $this->_keys = array_keys($this->_array);
    }
    return $this->_keys;
  }

  /**
   * @param mixed $key Key to search.
   * @return mixed If $key does not exist, then return FALSE.
   *               If next element does not exist, then return FALSE, otherwise return the next key.
   *               NOTICE: This function does not work right if the next key is NULL or FALSE.
   */
  public function getNextKey($key)
  {
    $result = FALSE;

    $keys = $this->getKeys();
    $index = array_search($key, $keys);
    if ($index !== FALSE) {
      if (isset($keys[$index + 1])) {
        $result = $keys[$index + 1];
      }
    }

    return $result;
  }

  /**
   * @param mixed $key Key to search.
   * @return mixed If $key does not exist, then return FALSE.
   *               If previous element does not exist, then return FALSE, otherwise return the previous key.
   *               NOTICE: This function does not work right if the next key is NULL or FALSE.
   */
  public function getPrevKey($key)
  {
    $result = FALSE;

    $keys = $this->getKeys();
    $index = array_search($key, $keys);
    if ($index !== FALSE) {
      if (isset($keys[$index - 1])) {
        $result = $keys[$index - 1];
      }
    }

    return $result;
  }

//   /**
//    * Get the next key of current position of array.
//    * @param array $arr
//    * @return mixed If current element reachs the end of the array, then return FALSE.
//    *               Otherwise, return the key of the next element.
//    */
//   public static function nextKey(&$array)
//   {
//     $result = FALSE;

//     $next = next($array);
//     // Set the array pointer back the old position.
//     if ($next === FALSE) {
//       // There are no more next element. Reset the pointer to the end of array.
//       end($array);
//     } else {
//       $result = key($array);
//       prev($array);
//     }

//     return $result;
//   }

//   /**
//    * Get the previous key of current position of array.
//    * @param array $arr
//    * @return mixed If current element is the first of the array, then return FALSE.
//    *               Otherwise, return the key of the previous element.
//    */
//   public static function prevKey(&$array)
//   {
//     $result = FALSE;

//     $prev = prev($array);
//     // Set the array pointer back the old position.
//     if ($prev === FALSE) {
//       // There are no more prev element. Reset the pointer to the first of array.
//       reset($array);
//     } else {
//       $result = key($array);
//       next($array);
//     }

//     return $result;
//   }

  /**
   * Get the first value of the array.
   * @param array $array
   * @return mixed Return the value if exists, FALSE if array is empty.
   */
  public static function getFirstValue($array)
  {
    $values = array_values($array);
    return $values ? $values[0] : FALSE;
  }

  /**
   * Get the first key of the array.
   * @param array $array
   * @return mixed Return the key if exists, FALSE if array is empty.
   */
  public static function getFirstKey($array)
  {
    $keys = array_keys($array);
    return $keys ? $keys[0] : FALSE;
  }

  /**
   * Check if two arrays contain same value set.
   * @param array $arr1
   * @param array $arr2
   * @return boolean TRUE if two arrays contain same value set.
   */
  public static function equal($arr1, $arr2)
  {
    return !array_diff($arr1, $arr2) && !array_diff($arr2, $arr1);
  }

  /**
   * Flatten elements of a multi-dimension array.
   * @param mixed $arr Anything (normal object, or array).
   * @return array
   */
  public static function flatten($arr)
  {
    if (!is_array($arr)) {
      $arr = array($arr);
    }

    $result = array();
    foreach ($arr as $element) {
      // Merge element to $result if it is an array.
      if (is_array($element)) {
        $result = array_merge($result, self::flatten($element));
      } else {
        // Add element to $result.
        $result[] = $element;
      }
    }

    return $result;
  }
  
  /**
   * Get an element. If not exist, return the default value.
   */
  public static function get(&$var, $default = NULL) {
    return isset($var) ? $var : $default;
  }
}
?>