<?php

use Eloquent\Phony\Test\Phony;

// setup
$stub = Phony::stub()->setLabel('label')->setUseIterableSpies(true);
$stub->with('aardvark')->returns(['AARDVARK']);
$stub->with('bonobo')->returns(['BONOBO']);
iterator_to_array($stub('aardvark'));
$stub('bonobo');

// verification
$stub->iterated()->never()->consumed();
