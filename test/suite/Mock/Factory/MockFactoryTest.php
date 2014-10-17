<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Factory;

use Eloquent\Phony\Mock\Builder\MockBuilder;
use Eloquent\Phony\Mock\Generator\MockGenerator;
use Eloquent\Phony\Stub\Factory\StubVerifierFactory;
use Eloquent\Phony\Test\TestMockGenerator;
use Mockery\Generator\Generator;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class MockFactoryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->generator = new MockGenerator();
        $this->stubVerifierFactory = new StubVerifierFactory();
        $this->subject = new MockFactory($this->generator, $this->stubVerifierFactory);
    }

    public function testConstructor()
    {
        $this->assertSame($this->generator, $this->subject->generator());
        $this->assertSame($this->stubVerifierFactory, $this->subject->stubVerifierFactory());
    }

    public function testConstructorDefaults()
    {
        $this->subject = new MockFactory();

        $this->assertSame(MockGenerator::instance(), $this->subject->generator());
        $this->assertSame(StubVerifierFactory::instance(), $this->subject->stubVerifierFactory());
    }

    public function testCreateMockClass()
    {
        $builder = new MockBuilder(
            'Eloquent\Phony\Test\TestClassB',
            array(
                'static methodA' => function () {
                    return 'static custom ' . implode(func_get_args());
                },
                'methodB' => function () {
                    return 'custom ' . implode(func_get_args());
                },
            ),
            __NAMESPACE__ . '\PhonyMockFactoryTestCreateMockClass'
        );
        $actual = $this->subject->createMockClass($builder);
        $protectedMethod = $actual->getMethod('testClassAStaticMethodC');
        $protectedMethod->setAccessible(true);

        $this->assertInstanceOf('ReflectionClass', $actual);
        $this->assertTrue($actual->implementsInterface('Eloquent\Phony\Mock\MockInterface'));
        $this->assertTrue($actual->isSubclassOf('Eloquent\Phony\Test\TestClassB'));
        $this->assertSame('ab', PhonyMockFactoryTestCreateMockClass::testClassAStaticMethodA('a', 'b'));
        $this->assertSame('protected ab', $protectedMethod->invoke(null, 'a', 'b'));
        $this->assertSame('static custom ab', PhonyMockFactoryTestCreateMockClass::methodA('a', 'b'));
    }

    public function testCreateMockClassFailure()
    {
        $source = <<<'EOD'
// this line is NOT context
// this line is context
// this line is context
// this line is context
function function () {}
// this line is context
// this line is context
// this line is context
// this line is NOT context
EOD;
        $this->subject = new MockFactory(new TestMockGenerator($source));
        $builder = new MockBuilder();
        $expected = <<<'EOD'
Relevant lines:
       2: // this line is context
       3: // this line is context
       4: // this line is context
       5: function function () {}
       6: // this line is context
       7: // this line is context
       8: // this line is context
EOD;

        if (defined('HHVM_VERSION')) {
            $this->setExpectedException('RuntimeException', 'Mock class generation failed.');
        } else {
            $this->setExpectedException('RuntimeException', $expected);
        }
        $this->subject->createMockClass($builder);
    }

    public function testCreateMockClassFailureAtStart()
    {
        $source = <<<'EOD'
// this line is context
function function () {}
// this line is context
// this line is context
// this line is context
// this line is NOT context
EOD;
        $this->subject = new MockFactory(new TestMockGenerator($source));
        $builder = new MockBuilder();
        $expected = <<<'EOD'
Relevant lines:
       1: // this line is context
       2: function function () {}
       3: // this line is context
       4: // this line is context
       5: // this line is context
EOD;

        if (defined('HHVM_VERSION')) {
            $this->setExpectedException('RuntimeException', 'Mock class generation failed.');
        } else {
            $this->setExpectedException('RuntimeException', $expected);
        }
        $this->subject->createMockClass($builder);
    }

    public function testCreateMockClassFailureAtEnd()
    {
        $source = <<<'EOD'
// this line is NOT context
// this line is context
// this line is context
// this line is context
function function () {}
// this line is context
EOD;
        $this->subject = new MockFactory(new TestMockGenerator($source));
        $builder = new MockBuilder();
        $expected = <<<'EOD'
Relevant lines:
       2: // this line is context
       3: // this line is context
       4: // this line is context
       5: function function () {}
       6: // this line is context
EOD;

        if (defined('HHVM_VERSION')) {
            $this->setExpectedException('RuntimeException', 'Mock class generation failed.');
        } else {
            $this->setExpectedException('RuntimeException', $expected);
        }
        $this->subject->createMockClass($builder);
    }

    public function testCreateMock()
    {
        $builder = new MockBuilder(
            'Eloquent\Phony\Test\TestClassB',
            array(
                'static methodA' => function () {
                    return 'static custom ' . implode(func_get_args());
                },
                'methodB' => function () {
                    return 'custom ' . implode(func_get_args());
                },
            ),
            __NAMESPACE__ . '\PhonyMockFactoryTestCreateMock'
        );
        $actual = $this->subject->createMock($builder);
        $class = new ReflectionClass($actual);
        $protectedMethod = $class->getMethod('testClassAMethodC');
        $protectedMethod->setAccessible(true);
        $protectedStaticMethod = $class->getMethod('testClassAStaticMethodC');
        $protectedStaticMethod->setAccessible(true);

        $this->assertInstanceOf('Eloquent\Phony\Mock\MockInterface', $actual);
        $this->assertInstanceOf('Eloquent\Phony\Test\TestClassB', $actual);
        $this->assertSame('ab', $actual->testClassAMethodA('a', 'b'));
        $this->assertSame('protected ab', $protectedMethod->invoke($actual, 'a', 'b'));
        $this->assertSame('custom ab', $actual->methodB('a', 'b'));
        $this->assertSame('ab', PhonyMockFactoryTestCreateMock::testClassAStaticMethodA('a', 'b'));
        $this->assertSame('protected ab', $protectedStaticMethod->invoke(null, 'a', 'b'));
        $this->assertSame('static custom ab', PhonyMockFactoryTestCreateMock::methodA('a', 'b'));
    }

    public function testCreateMockWithConstructorArguments()
    {
        $builder = new MockBuilder(
            'Eloquent\Phony\Test\TestClassB',
            null,
            __NAMESPACE__ . '\PhonyMockFactoryTestCreateMockWithConstructorArguments'
        );
        $actual = $this->subject->createMock($builder, array('a', 'b'));

        $this->assertSame(array('a', 'b'), $actual->constructorArguments);
    }

    public function testCreateMockWithConstructorArgumentsWithReferences()
    {
        $builder = new MockBuilder(
            'Eloquent\Phony\Test\TestClassA',
            null,
            __NAMESPACE__ . '\PhonyMockFactoryTestCreateMockWithConstructorArgumentsWithReferences'
        );
        $a = 'a';
        $b = 'b';
        $actual = $this->subject->createMock($builder, array(&$a, &$b));

        $this->assertSame(array('a', 'b'), $actual->constructorArguments);
        $this->assertSame('first', $a);
        $this->assertSame('second', $b);
    }

    public function testCreateMockWithOldConstructor()
    {
        $builder = new MockBuilder(
            'TestClassOldConstructor',
            null,
            __NAMESPACE__ . '\PhonyMockFactoryTestCreateMockWithOldConstructor'
        );
        $actual = $this->subject->createMock($builder, array('a', 'b'));

        $this->assertSame(array('a', 'b'), $actual->constructorArguments);
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
