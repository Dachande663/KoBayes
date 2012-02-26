<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Naive bayes classifier: basic word unigram feature extraction
 *
 * @package default
 * @author Luke Lanchester
 **/
class Kohana_KoBayes_Default extends KoBayes {
	

	/**
	 * Convert a string to unigram
	 *
	 * @param string Input string
	 * @return array Features
	 * @author Luke Lanchester
	 **/
	protected function extract_features($string) {

		$string = strtolower($string);
		$features = array();

		$matches = array();
		preg_match_all('/\w+/', $string, $matches);
		foreach($matches[0] as $word) {
			$features[] = "$word";
		}

		return $features;

	} // end func: extract_features



} // end class: KoBayes