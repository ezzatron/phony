<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Call\Event;

use PHPUnit_Framework_TestCase;

class ReturnedEventTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->returnValue = 'returnValue';
        $this->sequenceNumber = 111;
        $this->time = 1.11;
        $this->subject = new ReturnedEvent($this->returnValue, $this->sequenceNumber, $this->time);
    }

    public function testConstructor()
    {
        $this->assertSame($this->returnValue, $this->subject->returnValue());
        $this->assertSame($this->sequenceNumber, $this->subject->sequenceNumber());
        $this->assertSame($this->time, $this->subject->time());
    }
}
