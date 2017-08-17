<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2017 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Invocation;

use Eloquent\Phony\Reflection\FeatureDetector;
use Eloquent\Phony\Test\TestInvocable;
use Eloquent\Phony\Test\TestWrappedInvocable;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

class InvocableInspectorTest extends TestCase
{
    protected function setUp()
    {
        $this->subject = new InvocableInspector();

        $this->callback = function () {};
        $this->invocable = new TestInvocable();
        $this->wrappedInvocable = new TestWrappedInvocable($this->callback, null);

        $this->featureDetector = FeatureDetector::instance();
    }

    public function testCallbackReflector()
    {
        $this->assertEquals(
            new ReflectionMethod(__METHOD__),
            $this->subject->callbackReflector([$this, __FUNCTION__])
        );
        $this->assertEquals(
            new ReflectionMethod(__METHOD__),
            $this->subject->callbackReflector([__CLASS__, __FUNCTION__])
        );
        $this->assertEquals(new ReflectionMethod(__METHOD__), $this->subject->callbackReflector(__METHOD__));
        $this->assertEquals(new ReflectionFunction('implode'), $this->subject->callbackReflector('implode'));
        $this->assertEquals(
            new ReflectionFunction($this->callback),
            $this->subject->callbackReflector($this->callback)
        );
        $this->assertEquals(
            new ReflectionMethod($this->invocable, '__invoke'),
            $this->subject->callbackReflector($this->invocable)
        );
        $this->assertEquals(
            new ReflectionFunction($this->callback),
            $this->subject->callbackReflector($this->wrappedInvocable)
        );
    }

    public function testCallbackReflectorFailure()
    {
        $this->expectException('ReflectionException');
        $this->subject->callbackReflector(111);
    }

    public function testCallbackReflectorFailureObject()
    {
        $this->expectException('ReflectionException', 'Invalid callback.');
        $this->subject->callbackReflector((object) []);
    }

    public function testCallbackReturnType()
    {
        $this->assertNull($this->subject->callbackReturnType(function () {}));

        if ($this->featureDetector->isSupported('return.type')) {
            $type = $this->subject->callbackReturnType(eval('return function () : int {};'));

            $this->assertInstanceOf('ReflectionType', $type);
            $this->assertSame('int', strval($type));

            $type = $this->subject->callbackReturnType(eval('return function () : stdClass {};'));

            $this->assertInstanceOf('ReflectionType', $type);
            $this->assertSame('stdClass', strval($type));
        }
    }

    public function testIsBoundClosureSupported()
    {
        $reflectorReflector = new ReflectionClass('ReflectionFunction');
        $expected = $reflectorReflector->hasMethod('getClosureThis');

        $this->assertSame($expected, $this->subject->isBoundClosureSupported());
        $this->assertSame($expected, $this->subject->isBoundClosureSupported());
    }

    public function testInstance()
    {
        $class = get_class($this->subject);
        $reflector = new ReflectionClass($class);
        $property = $reflector->getProperty('instance');
        $property->setAccessible(true);
        $property->setValue(null, null);
        $instance = $class::instance();

        $this->assertInstanceOf($class, $instance);
        $this->assertSame($instance, $class::instance());
    }
}
