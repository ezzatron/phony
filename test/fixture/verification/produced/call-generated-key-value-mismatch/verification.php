<?php

use Eloquent\Phony\Test\Phony;

// setup
$stub = Phony::stub()->setLabel('label')->setUseIterableSpies(true);
$stub->with('aardvark')->generates(['AARDVARK' => 'MECHA-AARDVARK']);
$stub->with('bonobo')->generates(['BONOBO' => 'MECHA-BONOBO']);
iterator_to_array($stub('aardvark'));
iterator_to_array($stub('bonobo'));

// verification
$stub->lastCall()->generated()->produced('CHAMELEON', 'MECHA-CHAMELEON');
