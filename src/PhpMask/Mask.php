<?php
/**
 * Manage Mask like https://github.com/JsDaddy/ngx-mask - PHP Adaptation
 * User: Clemdesign
 * Date: 19/05/2019
 * Time: 20:23
 *
 * @ref: https://github.com/JsDaddy/ngx-mask/blob/develop/src/app/ngx-mask/mask-applier.service.ts
 */

namespace Clemdesign\PhpMask;

class Mask
{

  /**
   * List of available patterns
   * @var array
   */
  static $maskAvailablePatterns = array(
    '0' => array(
      'pattern' => "/\d/"
    ),
    '9' => array(
      'pattern' => "/\d/",
      'optional' => true
    ),
    'X' => array(
      'pattern' => "/\d/",
      'symbol' => '*'
    ),
    'A' => array(
      'pattern' => "/[a-zA-Z0-9]/"
    ),
    'S' => array(
      'pattern' => "/[a-zA-Z]/"
    ),
    'd' => array(
      'pattern' => "/\d/"
    ),
    'm' => array(
      'pattern' => "/\d/"
    ),
    'M' => array(
      'pattern' => "/\d/"
    ),
    'H' => array(
      'pattern' => "/\d/"
    ),
    'h' => array(
      'pattern' => "/\d/"
    ),
    's' => array(
      'pattern' => "/\d/"
    ),
  );

  /**
   * List of special characters
   * @var array
   */
  static $maskSpecialCharacters = array('-', '/', '(', ')', '.', ':', ' ', '+', ',', '@', '[', ']', '"', "'");

  /**
   * Entry point of Mask library
   * @param string $inputValue      Value to apply mask
   * @param string $maskExpression  Mask for output
   * @param null|array $config      Config of operation
   * @return bool|string
   */
  static function apply($inputValue, $maskExpression, $config = null)
  {
    if (!$inputValue || !$maskExpression) {
      return '';
    }

    if($config === null){
      $config = array();
    }

    $cursor = 0;
    $result = '';
    $multi = false;
    // TODO: Implement shift

    $prefix = isset($config["prefix"]) ? $config["prefix"] : "";
    $suffix = isset($config["suffix"]) ? $config["suffix"] : "";

    // Remove prefix from input value
    if((substr($inputValue, 0, strlen($prefix)) == $prefix) && (strlen($prefix) > 0)) {
      $inputValue = substr($inputValue, strlen($prefix));
    }

    if (Mask::_startsWith($maskExpression, 'percent')) {
      $inputValue = Mask::_checkInput($inputValue);
      $precision = Mask::_getPrecision($maskExpression);
      $inputValue = Mask::_checkInputPrecision($inputValue, $precision, '.');
      if (intval($inputValue) >= 0 && intval($inputValue) <= 100) {
        $result = $inputValue;
      } else {
        $result = substr($inputValue, 0, strlen($inputValue) - 1);
      }

    } elseif(
      Mask::_startsWith($maskExpression, 'separator') ||
      Mask::_startsWith($maskExpression, 'dot_separator') ||
      Mask::_startsWith($maskExpression, 'comma_separator')
    ) {
      // Clean input
      if(
        preg_match("/[wа-яА-Я]/", $inputValue) ||
        preg_match("/[a-z]|[A-Z]/", $inputValue) ||
        preg_match("/[-@#!$%\\^&*()_£¬'+|~=`{}\[\]:\";<>.?\/]/", $inputValue)
      ) {
        $inputValue = Mask::_checkInput($inputValue);
      }

      if (Mask::_startsWith($maskExpression, 'separator')) {
        if (
        (strpos($inputValue,',') >= 0) &&
        Mask::_endsWith($inputValue, ',') &&
        strpos($inputValue,',') !== strrpos($inputValue,',')
        ) {
          $inputValue = substr($inputValue, 0, strlen($inputValue) - 1);
        }
      }

      if (Mask::_startsWith($maskExpression, 'dot_separator')) {
        if (
          (strpos($inputValue,',') > 0) &&
          strpos($inputValue,',') === strrpos($inputValue,',')
        ) {
          $inputValue = str_replace(",", ".", $inputValue);
        }
      }

      if (Mask::_startsWith($maskExpression, 'comma_separator')) {
        $inputValue = strlen($inputValue) > 1 && substr($inputValue, 0, 1) === '0' && substr($inputValue, 1, 1) !== '.'
          ? substr($inputValue, 1)
          : $inputValue;
      }

      $precision = Mask::_getPrecision($maskExpression);
      if($precision === null) {
        $precision = strlen(substr($inputValue, strrpos($inputValue, ".")+1));
      }

      $inputValue = floatval($inputValue);

      if (Mask::_startsWith($maskExpression, 'separator')) {
        if (preg_match("/[@#!$%^&*()_+|~=`{}\[\]:.\";<>?\/]/", $inputValue)) {
          $inputValue = substr($inputValue, 0, strlen($inputValue) - 1);
        }
        $result = number_format($inputValue, $precision, ",", " ");
      } elseif (Mask::_startsWith($maskExpression, 'dot_separator')) {
        if (preg_match("/[@#!$%^&*()_+|~=`{}\[\]:\s\";<>?\/]/", $inputValue)) {
          $inputValue = substr($inputValue, 0, strlen($inputValue) - 1);
        }
        $result = number_format($inputValue, $precision, ",", ".");
      } elseif (Mask::_startsWith($maskExpression, 'comma_separator')) {
        $result = number_format($inputValue, $precision, ".", ",");
      }

      // TODO: Implement line 139 to 160 ?

    } else {

      for ($i = 0, $inputSymbol = substr($inputValue, 0, 1); $i < strlen($inputValue); $i++, $inputSymbol = substr($inputValue, $i, 1)) {
        if ($cursor === strlen($maskExpression)) {
          break;
        }

        $maskCursor = substr($maskExpression, $cursor, 1);
        $maskCursorP1 = substr($maskExpression, $cursor + 1, 1);
        $maskCursorP2 = substr($maskExpression, $cursor + 1, 1);
        $maskCursorM1 = ($cursor > 0) ? substr($maskExpression, $cursor - 1, 1) : "";

//        DEBUG
//        var_dump("ME=".$maskCursor.", ME+1=".$maskCursorP1.", ME+2=".$maskCursorP2.", ME-1=".$maskCursorM1.", IS=".$inputSymbol.", Cur=".$cursor.", IV=".$inputValue);

        // 171
        if (Mask::_checkSymbolMask($inputSymbol, $maskCursor) && ($maskCursorP1 === '?')) {
          $result .= $inputSymbol;
          $cursor += 2;
        } elseif ($maskCursorP1 === '*' && $multi && Mask::_checkSymbolMask($inputSymbol, $maskCursor)) {
          $result .= $inputSymbol;
          $cursor += 3;
          $multi = false;
          //182
        } elseif (Mask::_checkSymbolMask($inputSymbol, $maskCursor) && ($maskCursorP1 === '*')) {
          $result .= $inputSymbol;
          $multi = true;
        } elseif (($maskCursorP1 === '?') && Mask::_checkSymbolMask($inputSymbol, $maskCursorP2)) {
          $result .= $inputSymbol;
          $cursor += 3;
          // 194
        } elseif (Mask::_checkSymbolMask($inputSymbol, $maskCursor)) {
          if ($maskCursor === 'H') {
            if (intval($inputSymbol) > 2) {
              $cursor += 1;
              $i--;
              continue;
            }
          }
          // 211
          if ($maskCursor === 'h') {
            if ($result === '2' && intval($inputSymbol) > 3) {
              continue;
            }
          }
          if ($maskCursor === 'm') {
            if (intval($inputSymbol) > 5) {
              $cursor += 1;
              $i--;
              continue;
            }
          }
          if ($maskCursor === 's') {
            if (intval($inputSymbol) > 5) {
              $cursor += 1;
              $i--;
              continue;
            }
          }
          if ($maskCursor === 'd') {
            if (intval($inputSymbol) > 3) {
              $cursor += 1;
              $i--;
              continue;
            }
          }
          if ($maskCursorM1 === 'd') {
            if (intval(substr($inputValue, $cursor - 1, 2)) > 0) {
              continue;
            }
          }
          if ($maskCursor === 'M') {
            if (intval($inputSymbol) > 1) {
              $cursor += 1;
              $i--;
              continue;
            }
          }
          if ($maskCursorM1 === 'M') {
            if (intval(substr($inputValue, $cursor - 1, 2)) > 12) {
              continue;
            }
          }
          $result .= $inputSymbol;
          $cursor++;
          // 272
        } elseif (Mask::_findSpecialChar($maskCursor)) {
          $result .= $maskCursor;
          $cursor++;
          $i--;
        } elseif(
          Mask::_findSpecialChar($inputSymbol) &&
          isset(Mask::$maskAvailablePatterns[$maskCursor]) &&
          isset(Mask::$maskAvailablePatterns[$maskCursor]["optional"]) &&
          Mask::$maskAvailablePatterns[$maskCursor]["optional"] == true
        ) {
          $cursor++;
          $i--;
        } elseif ($maskCursorP1 === '*' &&
          Mask::_findSpecialChar($maskCursorP2) &&
          Mask::_findSpecialChar($inputSymbol) === $maskCursorP2 &&
          $multi
        ) {
          $cursor += 3;
          $result .= $inputSymbol;
        } elseif ($maskCursorP1 === '?' &&
          Mask::_findSpecialChar($maskCursorP2) &&
          Mask::_findSpecialChar($inputSymbol) === $maskCursorP2 &&
          $multi
        ) {
          $cursor += 3;
          $result .= $inputSymbol;
        }

      }
    }

    //305
    // Last char
    if (
      ((strlen($result) + 1) === strlen($maskExpression)) &&
      Mask::_findSpecialChar(substr($maskExpression, -1))
    ) {
      $result .= substr($maskExpression, -1);
    }

    // TODO: Implement line 320
    // TODO: Implement line 324 and 323

    // Add prefix and suffix
    if($prefix != "") {
      $result = $prefix . $result;
    }
    if($suffix != "") {
      $result = $result . $suffix;
    }

    return $result;
  }

  /**
   * _checkSymbolMask function
   * @param $inputSymbol
   * @param $maskSymbol
   * @return bool
   */
  static function _checkSymbolMask($inputSymbol, $maskSymbol)
  {
    return (
      isset(Mask::$maskAvailablePatterns[$maskSymbol]) &&
      Mask::$maskAvailablePatterns[$maskSymbol]["pattern"] &&
      preg_match(Mask::$maskAvailablePatterns[$maskSymbol]["pattern"], $inputSymbol)
    );
  }

  /**
   * Check if inputSymbol is special char
   * @param $inputSymbol
   * @return bool|string
   */
  static function _findSpecialChar($inputSymbol)
  {
    $returnChar = false;
    foreach (Mask::$maskSpecialCharacters as $char) {
      if ($inputSymbol === $char) {
        $returnChar = $char;
        break;
      }
    }
    return $returnChar;
  }

  /**
   * Determine if a string $haystack starts with $needle
   * @param $haystack
   * @param $needle
   * @return bool
   */
  static function _startsWith($haystack, $needle)
  {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
  }

  /**
   * Determine if a string $haystack ends with $needle
   * @param $haystack
   * @param $needle
   * @return bool
   */
  static function _endsWith($haystack, $needle)
  {
    $length = strlen($needle);
    if ($length == 0) {
      return true;
    }

    return (substr($haystack, -$length) === $needle);
  }

  /**
   * Check that input is a number as string
   * @param $str
   * @return string
   */
  static function _checkInput($str)
  {
    $strArr = str_split($str);
    $strRet = "";

    foreach ($strArr as $char) {
      if(preg_match("/\d/", $char) || ($char === '.') || ($char === ',')) {
        $strRet .= $char;
      }
    }

    return $strRet;
  }

  /**
   * Get precision of expression
   * @param $maskExpression
   * @return int|null
   */
  static function _getPrecision($maskExpression){
    $pos = strrpos($maskExpression, ".");
    $precision = null;
    if($pos) {
      $precision = intval(substr($maskExpression, $pos + 1));
    }
    return $precision;
  }

  /**
   * Convert a float in string format with precision
   * @param $inputValue
   * @param $precision
   * @param string $decimalMarker
   * @return string
   */
  static function _checkInputPrecision($inputValue, $precision, $decimalMarker = '.')
  {
    if($precision !== null) {
      $pos = strrpos($inputValue, $decimalMarker);
      $decimal = "";
      if($pos) {
        $decimal = substr($inputValue, $pos, $precision + 1);
      }
      $inputValue = substr($inputValue, 0, $pos) . ($decimal === "." ? "" : $decimal);
    }

    return $inputValue;
  }
}
