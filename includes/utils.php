<?php
/**
 *  @package Cumula
 *  @subpackage Utils
 *  @version    $Id$
 */

//Utility function to format a string using camelcase
function toCamelCase($str, $capitalise_first_char = false)
{
	if ($capitalise_first_char) {
		$str[0] = strtoupper($str[0]);
	}
	$func = create_function('$c', 'return strtoupper($c[1]);');
	return preg_replace_callback('/_([a-z])/', $func, $str);
}

//Utility function to remove camelcase formatting on a string
function fromCamelCase($str)
{
	$str[0] = strtolower($str[0]);
	$func = create_function('$c', 'return "_" . strtolower($c[1]);');
	return preg_replace_callback('/([A-Z])/', $func, $str);
}