<?php
/**
 * String manipulation class
 *
 * @author Frederic BAYLE
 */

namespace FastTrack;

/**
 * String manipulation class
 */
class Str extends ObjectBase {
	/**
	 * Binary compare 2 strings
	 *
	 * @param      string  $pStr1  Reference string
	 * @param      string  $pStr2  String to compare with
	 * @return     int             Binary comparison result (-1, 0, 1)
	 */
	public static function compare($pStr1, $pStr2) {
		return strcmp($pStr1, $pStr2);
	}
	
	/**
	 * Check if a string contains another string
	 *
	 * @param      string  $pInput         String to search in
	 * @param      string  $pSearchFor     String to search for
	 * @return     bool                    True if $pInput contains $pSearchFor, otherwise false
	 */
	public static function contains($pInput, $pSearchFor) {
		if(Str::isNullOrEmpty($pInput) || Str::isNullOrEmpty($pSearchFor)) {
			return false;
		}
		if(mb_strpos($pInput, $pSearchFor) === false) {
			return false;
		}
		return true;
	}
	
	/**
	 * Check if a string ends with another string
	 *
	 * @param      string  $pInput         String to search in
	 * @param      string  $pSearchFor     String to search for
	 * @return     bool                    True if $pInput ends with $pSearchFor, otherwise false
	 */
	public static function endsWith($pInput, $pSearchFor) {
		if(strpos(strrev($pInput), strrev($pSearchFor)) === 0) {
			return true;
		}
		return false;
	}
	
	/**
	 * Escape a string for using it in a Javascript code string
	 *
	 * @param      string  $pInput     String to escape
	 * @param      string  $pQuote     Quotes used to contain the Javascript string (" or ')
	 * @return     string
	 */
	public static function escapeJsString($pInput, $pQuote = '"') {
		$returnValue = Str::replace($pInput, "\\", "\\\\");
		$returnValue = Str::replace($returnValue, "\n", "\\n");
		
		if($pQuote == '"') {
			$returnValue = Str::replace($returnValue, '"', '\\"');
		}
		else if($pQuote == "'") {
			$returnValue = Str::replace($returnValue, "'", "\\'");
		}
		else {
			throw new \Exception('Unknown quote. You must use " or \'');
		}
		return $returnValue;
	}
	
	/**
	 * Decode a string to unescape its contained HTML tags
	 *
	 * @param      string  $pInputString   String to unespace
	 * @return     string
	 */
	public static function htmlDecode($pInputString) {
	    return htmlspecialchars_decode($pInputString);
	}
	
	/**
	 * Encode a string to escape its contained HTML tags
	 *
	 * @param      string  $pInputString   String to escape
	 * @return     string
	 */
	public static function htmlEncode($pInputString) {
	    return htmlspecialchars($pInputString);
	}
	
	/**
	 * Get the offset of a string in another one
	 *
	 * @param      string  $pInput         String to search in
	 * @param      string  $pSearchFor     String to search for
	 * @param      int     $pOffset        Start searching at the index...
	 * @return     int                     Position of the first occurence, false if not found
	 */
	public static function indexOf($pInput, $pSearchFor, $pOffset = 0) {
		if(Str::isNullOrEmpty($pInput) || Str::isNullOrEmpty($pSearchFor)) {
			return false;
		}
		return mb_strpos($pInput, $pSearchFor, $pOffset);
	}
	
	/**
	 * Check if a string is null or empty
	 *
	 * @param      string  $pInput     String to check
	 * @return     bool                True if string is null or empty, otherwise false
	 */
	public static function isNullOrEmpty($pInput) {
		if($pInput === null) {
			return true;
		}
		else if(print_r($pInput, true) === '') {
			return true;
		}
		return false;
	}
	
	/**
	 * Check if a string is null or only contains whitespace caracters
	 *
	 * @param      string  $pInput     String to check
	 * @return     bool                True if string is null or only contains whitespace caracters, otherwise false
	 */
	public static function isNullOrWhiteSpace($pInput) {
		if(Str::isNullOrEmpty($pInput))	{
			return true;
		}
		else if(Str::trim(print_r($pInput, true)) === '') {
			return true;
		}
		return false;
	}
	
	/**
	 * Join array elements into a string using a separator
	 *
	 * @param      array   $pArray         Array that contains elemnts to join
	 * @param      string  $pSeparator     Separator
	 * @return     string                  Result
	 */
	public static function join($pArray, $pSeparator = ',')	{
		return implode($pSeparator, $pArray);
	}
	
	/**
	 * Get the length of a string
	 *
	 * @param      string  $pInput     String to check
	 * @return     int                 Length of the given string
	 */
	public static function length($pInput) {
		return mb_strlen($pInput);
	}
	
	/**
	 * Get a lowercased string
	 *
	 * @param      string  $pInput     String to process
	 * @return     string              Lowercased string
	 */
	public static function lower($pInput) {
		return mb_strtolower($pInput);
	}
	
	/**
	 * Pad a string from the left with a caracter
	 *
	 * @param      string  $pInput         String to pad
	 * @param      int     $pPadLength     Final string length
	 * @param      string  $pPadChar       Caracter to use for padding the string
	 * @return     string                  Padded string
	 */
	public static function padLeft($pInput, $pPadLength, $pPadChar)	{
		return str_pad($pInput, $pPadLength, $pPadChar, STR_PAD_LEFT);
	}
	
	/**
	 * Pad a string from the right with a caracter
	 *
	 * @param      string  $pInput         String to pad
	 * @param      int     $pPadLength     Final string length
	 * @param      string  $pPadChar       Caracter to use for padding the string
	 * @return     string                  Padded string
	 */
	public static function padRight($pInput, $pPadLength, $pPadChar) {
		return str_pad($pInput, $pPadLength, $pPadChar, STR_PAD_RIGHT);
	}
	
	/**
	 * Repeat a string n times
	 *
	 * @param      string  $pInput     String to repeat
	 * @param      int     $pNTimes    Number of repetitions
	 * @return     string              Repeated string
	 */
	public static function repeat($pInput, $pNTimes) {
		if($pNTimes < 0) {
			// Must be >= 0
			throw new \Exception('$pNTimes must be higher that 0');
		}
		$returnValue = '';
		for ($i = 0; $i < $pNTimes; $i++) {
			$returnValue .= $pInput;
		}
		return $returnValue;
	}
	
	/**
	 * Replace all occurences of a string by another
	 *
	 * @param      string  $pInput         String to search in
	 * @param      string  $pSearchFor     String to search for
	 * @param      string  $pReplacement   Replacement string
	 * @return     string                  Result
	 */
	public static function replace($pInput, $pSearchFor, $pReplacement) {
		return str_replace($pSearchFor, $pReplacement, $pInput);
	}
	
	/**
	 * Split a string into an array using a separator
	 *
	 * @param      string  $pInput         String to split
	 * @param      string  $pSeparator     Separator
	 * @return     array                   Result
	 */
	public static function split($pInput, $pSeparator) {
		return explode($pSeparator, $pInput);
	}
	
	/**
	 * Check if a string starts with another string
	 *
	 * @param      string  $pInput         String to search in
	 * @param      string  $pSearchFor     String to search for
	 * @return     bool                    True if $pInput starts with $pSearchFor, otherwise false
	 */
	public static function startsWith($pInput, $pSearchFor)	{
		if(strpos($pInput, $pSearchFor) === 0) {
			return true;
		}
		return false;
	}
	
	/**
	 * Get a substring
	 *
	 * @param      string  $pInput     String to search in
	 * @param      int     $pStart     Start at the index...
	 * @param      int     $pLength    Length of the substring
	 * @return     string              Result
	 */
	public static function substring($pInput, $pStart, $pLength = NULL)	{
		return mb_substr($pInput, $pStart, $pLength);
	}
	
	/**
	 * Remove all starting and ending withespace caracters from a string
	 *
	 * @param      string  $pInput     String to clean
	 * @return     string              Result
	 */
	public static function trim($pInput) {
		return trim($pInput);
	}
	
	/**
	 * Get an uppercased string
	 *
	 * @param      string  $pInput     String to process
	 * @return     string              Uppercased string
	 */
	public static function upper($pInput) {
		return mb_strtoupper($pInput);
	}
}
