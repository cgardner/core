<?php

class DummyComponent {
	public function __call($name, $args) {
		trigger_error('You called an instance which doesnt exist');
	}
}

function I($component) {
	$loader = \Cumula\Autoloader::getInstance();
	if($abs = $loader->absoluteClassName($component)) {
		return $abs::getInstance();
	} else {
		return new DummyComponent();
	}
}

