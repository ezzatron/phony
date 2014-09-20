<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Matcher;

/**
 * A matcher that wraps a typical matcher from another library.
 */
class WrappedMatcher implements MatcherInterface
{
    /**
     * Construct a new wrapped matcher.
     *
     * @param object $matcher The matcher to wrap.
     */
    public function __construct($matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * Get the wrapped matcher.
     *
     * @return object The matcher.
     */
    public function matcher()
    {
        return $this->matcher;
    }

    /**
     * Returns true if the supplied value matches.
     *
     * @param mixed $value The value to check.
     *
     * @return boolen True if the value matches.
     */
    public function matches($value)
    {
        return $this->matcher->matches($value);
    }

    private $matcher;
}