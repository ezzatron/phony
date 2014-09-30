<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Assertion\Renderer;

use Eloquent\Phony\Call\CallInterface;
use Eloquent\Phony\Matcher\MatcherInterface;
use ReflectionMethod;
use SebastianBergmann\Exporter\Exporter;

/**
 * Renders various data for use in assertion messages.
 *
 * @internal
 */
class AssertionRenderer implements AssertionRendererInterface
{
    /**
     * Get the static instance of this renderer.
     *
     * @return AssertionRendererInterface The static renderer.
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Construct a new call renderer.
     *
     * @param Exporter|null $exporter The exporter to use.
     */
    public function __construct(Exporter $exporter = null)
    {
        if (null === $exporter) {
            $exporter = new Exporter();
        }

        $this->exporter = $exporter;
    }

    /**
     * Get the exporter.
     *
     * @return Exporter The exporter.
     */
    public function exporter()
    {
        return $this->exporter;
    }

    /**
     * Render a value.
     *
     * @param mixed $value The value.
     *
     * @return string The rendered value.
     */
    public function renderValue($value)
    {
        return $this->exporter->shortenedExport($value);
    }

    /**
     * Render a sequence of matchers.
     *
     * @param array<integer,MatcherInterface> $matchers The matchers.
     *
     * @return string The rendered matchers.
     */
    public function renderMatchers(array $matchers)
    {
        if (count($matchers) < 1) {
            return '<none>';
        }

        $rendered = array();
        foreach ($matchers as $matcher) {
            $rendered[] = $matcher->describe();
        }

        return implode(', ', $rendered);
    }

    /**
     * Render a sequence of calls.
     *
     * @param array<integer,CallInterface> $calls The calls.
     *
     * @return string The rendered calls.
     */
    public function renderCalls(array $calls)
    {
        $rendered = array();
        foreach ($calls as $call) {
            $rendered[] =
                sprintf('    - %s', $this->renderCall($call));
        }

        return implode("\n", $rendered);
    }

    /**
     * Render a only the arguments of a sequence of calls.
     *
     * @param array<integer,CallInterface> $calls The calls.
     *
     * @return string The rendered call arguments.
     */
    public function renderCallsArguments(array $calls)
    {
        $rendered = array();
        foreach ($calls as $call) {
            $rendered[] =
                sprintf('    - %s', $this->renderArguments($call->arguments()));
        }

        return implode("\n", $rendered);
    }

    /**
     * Render the supplied call.
     *
     * @param CallInterface $call The call.
     *
     * @return string The rendered call.
     */
    public function renderCall(CallInterface $call)
    {
        $reflector = $call->reflector();

        if ($reflector instanceof ReflectionMethod) {
            if ($reflector->isStatic()) {
                $callOperator = '::';
            } else {
                $callOperator = '->';
            }

            $renderedSubject = $reflector->getDeclaringClass()->getName() .
                $callOperator .
                $reflector->getName();
        } else {
            $renderedSubject = $reflector->getName();
        }

        $arguments = $call->arguments();

        $renderedArguments = array();
        foreach ($arguments as $argument) {
            $renderedArguments[] = $this->exporter->shortenedExport($argument);
        }

        return sprintf(
            '%s(%s)',
            $renderedSubject, implode(', ', $renderedArguments)
        );
    }

    /**
     * Render a sequence of arguments.
     *
     * @param array<integer,mixed> $arguments The arguments.
     *
     * @return string The rendered arguments.
     */
    public function renderArguments(array $arguments)
    {
        if (count($arguments) < 1) {
            return '<none>';
        }

        $rendered = array();
        foreach ($arguments as $argument) {
            $rendered[] = $this->exporter->shortenedExport($argument);
        }

        return implode(', ', $rendered);
    }

    private static $instance;
}