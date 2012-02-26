<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Naive bayes classifier
 *
 * @package default
 * @author Luke Lanchester
 **/
abstract class Kohana_KoBayes {
	

	/**
	 * @var string Default KoBayes classification engine
	 **/
	public static $default = 'default';


	/**
	 * Generate a new KoBayes Classifier
	 *
	 * @return KoBayes
	 * @author Luke Lanchester
	 **/
	public static function factory($type = null) {
		if($type === null) $type = KoBayes::$default;
		$class = 'KoBayes_'.$type;
		if(!class_exists($class)) throw new Kohana_Exception('Unknown KoBayes classification engine: '.$type);
		return new $class;
	} // end func: factory



	/**
	 * Returns an array of features to classify text with
	 *
	 * The most basic example of a feature extractor would
	 * return an array containing each word in the string
	 * to be classified. This is what the KoBayes_Default
	 * classifier actually does.
	 *
	 * More advanced classifiers can return bigrams,
	 * specific features (numbers, emails, currency
	 * symbols) etc to improve accuracy.
	 *
	 * As a final note, stemming and other input
	 * pre-processors can aid accuracy but may also harm
	 * the accuracy just as much. Experiment!
	 *
	 * @param string Input string
	 * @return array Features
	 * @author Luke Lanchester
	 **/
	abstract protected function extract_features($string);



	/**
	 * @var array Classification subjects e.g. positive, negative
	 **/
	protected $subjects = array();


	/**
	 * @var array Tokens and their subject counts
	 **/
	protected $tokens = array();


	/**
	 * @var int Total number of rows trained with
	 **/
	protected $total_samples = 0;


	/**
	 * @var int Total number of tokens trained with
	 **/
	protected $total_tokens = 0;


	/**
	 * Protected constructor
	 *
	 * @return void
	 * @author Luke Lanchester
	 **/
	protected function __construct() {}



	/**
	 * Train dataset
	 *
	 * Call with a given subject and array of training strings, e.g.
	 *
	 *   ->train('positive', array(
	 *       'this is a happy string',
	 *       'what a great day!',
	 *   ))
	 *   ->train('negative', array(
	 *       'i hate doing housework',
	 *       'this movie is awful'
	 *   ))
	 *
	 * @param string Classification subject
	 * @param array Training strings
	 * @return self
	 * @author Luke Lanchester
	 **/
	public function train($subject, array $data) {
		
		if(!isset($this->subjects[$subject])) {
			$this->subjects[$subject] = array(
				'count_samples' => 0,
				'count_tokens'  => 0,
				'prior_value'   => null,
			);
		}

		if(empty($data)) return $this;

		foreach($data as $row) {
			$this->total_samples++;
			$this->subjects[$subject]['count_samples']++;

			$tokens = $this->extract_features($row);
			foreach($tokens as $token) {
				
				if(!isset($this->tokens[$token][$subject])) $this->tokens[$token][$subject] = 0;

				$this->tokens[$token][$subject]++;
				$this->subjects[$subject]['count_tokens']++;
				$this->total_tokens++;

			}

		} // end foreach

		return $this;

	} // end func: train



	/**
	 * Classify a given string and return the probability of each group,
	 * the most likely group is first. All probabilities equal 1.0
	 *
	 *   ->classify('That book was awful')
	 *   array(
	 *		'negative' => 0.9875
	 *      'positive' => 0.0125
	 *   )
	 *
	 * @param string String to classify
	 * @return array Group probabilities
	 * @author Luke Lanchester
	 **/
	public function classify($string) {

		$tokens = $this->extract_features($string);

		$total_score = 0;
		$scores = array();

		foreach($this->subjects as $subject => $subject_data) {
			$subject_data['prior_value'] = $subject_data['count_samples'] / $this->total_samples;
			$this->subjects[$subject] = $subject_data;

			$scores[$subject] = 1;

			foreach($tokens as $token) {
				$count = isset($this->tokens[$token][$subject]) ? $this->tokens[$token][$subject] : 0;
				$scores[$subject] *= ($count + 1) / ($subject_data['count_tokens'] + $this->total_tokens);
			}

			$scores[$subject] = $subject_data['prior_value'] * $scores[$subject];
			$total_score += $scores[$subject];

		}

		if($total_score === 0) $total_score = 1;
		$total_score = 1 / $total_score;
		foreach($scores as $subject => $score) $scores[$subject] = $score * $total_score;

		arsort($scores); # sort most likely first
		return $scores;

	} // end func: classify



} // end class: Kohana_KoBayes