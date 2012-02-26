KoBayes Kohana Naive Bayes Classifier
===============================

KoBayes is a Naive Bayes Classifier written for the Kohana framework. It supports open classification groups and extensible feature extraction libraries. It is based on code originally written by Ian Barber.


Features
--------

* Naive Bayes Classifier
* Open classification groups (support for multiple groups)
* Extensible feature extraction (use bigrams, symbols etc)
* Simple chainable interface
* Kohana 3 compatible


Usage
-----

```php
// Train the classifier
$classifier = KoBayes::factory('my_classifier')
    ->train('positive', array(
    	'that film was excellent',
    	'loved the movie we saw last night!',
    	'great time at the new steakhouse, loved the food!',
    ))
    ->train('negative', array(
    	'feel sick after eating all that steak, worst food ever',
    	'wish I could get those 2 hours back, that movie sucked!',
    	'what a horrible day, I hate this weather',
    ))
    ->train('positive', array(
    	'love that I can extend a trained group!',
    ));

// Classify our text
$results = $classifier->classify('Just read a book, what an excellent ending!');

// Example output (all results total 1.0)
$results = array(
	'positive' => 0.9875,
	'negative' => 0.0125,
);

```


Thanks
------

* Kohana (http://kohanaframework.org/)
* Ian Barber (http://phpir.com/bayesian-opinion-mining)